<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\GuideDraft;
use App\Entity\GuideTranslation;
use App\Field\Admin\TranslationField;
use App\Form\Admin\GuideDraftAdditionalHelpFormType;
use App\Form\Admin\GuideDraftCategoryFormType;
use App\Service\GuideService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class GuideDraftCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AdminUrlGenerator
     */
    protected $router;

    /**
     * @var GuideService
     */
    private $guideService;

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     * @param AdminUrlGenerator      $router
     * @param GuideService           $guideService
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        AdminUrlGenerator $router,
        GuideService $guideService
    ) {
        $this->translator = $translator;
        $this->em = $em;
        $this->router = $router;
        $this->guideService = $guideService;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return GuideDraft::class;
    }

    /**
     * Add Customize wysiwyg them to forms.
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $guide = $this->translator->trans('content.draft.guide');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.draft.guides')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $guide)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $guide)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.slug', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * By default, sort ASC guide by title in current locale.
     *
     * @param SearchDto        $searchDto
     * @param EntityDto        $entityDto
     * @param FieldCollection  $fields
     * @param FilterCollection $filters
     *
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $alias = $qb->getRootAliases()[0];
        $qb->leftJoin($alias . '.translations', 'tt', Join::WITH, 'tt.locale = :locale')
            ->setParameter('locale', $this->getContext()->getRequest()->getLocale())
            ->addOrderBy('tt.title', 'ASC');

        return $qb;
    }

    /**
     * Set permissions on CRUD actions.
     * Customize label on create button.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Preview a GuideDraft on Front Office like a Guide.
        $previewOne = Action::new('previewOne', $this->translator->trans('bo.preview'))
            ->linkToRoute('front.preview.guidedraft', function (GuideDraft $draft): array {
                return [
                    'slug' => ($draft->getSlug()) ? $draft->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);
        // Publish the draft ie. create a copy as Guide object.
        $toPublish = Action::new('toPublish', ucfirst($this->translator->trans('bo.topublish')))
            // Callable method implemented in this class.
            ->linkToCrudAction('publishDraft')
            //  "action-toPublish" is the autogenerated classname if no specify.
            ->setCssClass('action-toPublish btn btn-success');

        // Duplicate item
        $duplicate = Action::new('duplicate', $this->translator->trans('content.clone.button'))
            ->linkToCrudAction('duplicateDraft')
            ->setIcon('fa fa-copy')
            ->setCssClass('btn');

        $actions = parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_CONTRIB',
                Action::EDIT   => 'ROLE_CONTRIB',
                Action::DELETE => 'ROLE_CONTRIB',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.draft.guide'))
                );
            })
            ->add(Crud::PAGE_INDEX, $previewOne)
            ->add(Crud::PAGE_EDIT, $toPublish) // Publish action only if draft has been saved.
            ->add(Crud::PAGE_INDEX, $duplicate)
        ;

        return $actions;
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $fieldsConfig = [
            'title'   => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('guide.prop.title')),
                'attr'       => ['maxlength' => GuideTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => GuideTranslation::LEN_TITLE]
                ),
            ],
            'imageLocale' => [
                'field_type' => ElfinderType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('guide.prop.img')),
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('guide.prop.content')),
            ],
            'slug'    => [
                'field_type' => SlugType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('prop.slug')),
                'attr'       => ['maxlength' => GuideTranslation::LEN_SLUG],
                'target'     => 'title',
                'help'       => $this->translator->trans('prop.slug.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => GuideTranslation::LEN_SLUG]),
            ],
            'imgLicence'   => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('prop.img.licence')),
                'attr'       => ['maxlength' => GuideTranslation::LEN_IMG_LICENCE],
                'help'       => $this->translator->trans('prop.img.licence.help') .
                    ' - ' . $this->translator->trans(
                        'bo.length.helper.max',
                        ['%len%' => GuideTranslation::LEN_IMG_LICENCE]) . '</br>' .
                        $this->translator->trans('prop.img.licence.help.symbol'
                    ),
            ],
            'imgLegend'     => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('prop.img.legend')),
                'attr'       => ['maxlength' => GuideTranslation::LEN_IMG_LEGEND],
                'help'       => $this->translator->trans('prop.img.legend.help') . 
                    ' - ' . $this->translator->trans(
                        'bo.length.helper.max',
                        ['%len%' => GuideTranslation::LEN_IMG_LEGEND]
                    ),
            ],
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true);

            $fields[] = FormField::addPanel($this->translator->trans('guideCategory.group.category'))
                ->setHelp($this->translator->trans('guideCategory.group.category.help'));
            $fields[] = CollectionField::new('categories', ucfirst($this->translator->trans('guide.prop.categories')))
                ->renderExpanded()
                ->setEntryIsComplex(true)
                ->setEntryType(GuideDraftCategoryFormType::class)
                ->setRequired(true)
            ;

            $fields[] = FormField::addPanel($this->translator->trans('guideAdditionalHelp.prop.additionalHelp'))
                ->setHelp($this->translator->trans('guideAdditionalHelp.prop.additionalHelp.help'));
            $fields[] = CollectionField::new(
                'additionalHelps',
                ucfirst($this->translator->trans('content.additionalHelps'))
            )
                ->renderExpanded()
                ->setEntryIsComplex(true)
                ->setEntryType(GuideDraftAdditionalHelpFormType::class)
                ->setRequired(false)
            ;
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('guide.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = ImageField::new('imageLocale', ucfirst($this->translator->trans('guide.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = TextField::new('imgLegend', ucfirst($this->translator->trans('prop.img.legend')));
            $fields[] = TextField::new('guide', ucfirst($this->translator->trans('guidedraft.prop.guide')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }
        return $fields;
    }

    /**
     * Call a Custom service dedicated to publish ie create/update the associated Guide object.
     *
     * @param AdminContext $adminContext
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function publishDraft(AdminContext $adminContext)
    {
        /** @var GuideDraft $draft */
        $draft = $adminContext->getEntity()->getInstance();
        $message = $this->translator->trans('content.publish.success');

        try {
            $this->guideService->publish($draft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }

        $urlToReturn = $this->router
            ->setController(GuideCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($draft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }

    /**
     * Duplicate a Guide Draft
     *
     * @param AdminContext    $context
     * @param GuideDraft      $originalDraft
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function duplicateDraft(AdminContext $context)
    {
        // Get the current item
        $originalDraft = $context->getEntity()->getInstance();
        $message = $this->translator->trans('content.clone.success');

        try {
            // Create a new instance of the entity
            $this->guideService->duplicate($originalDraft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }

        // Redirect back to the index page
        $urlToReturn = $this->router
            ->setController(GuideDraftCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($originalDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }
}
