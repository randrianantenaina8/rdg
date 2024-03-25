<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\DatasetDraft;
use App\Entity\DatasetTranslation;
use App\Field\Admin\TranslationField;
use App\Service\DatasetService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class DatasetDraftCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var DatasetService
     */
    private $datasetService;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param TranslatorInterface    $translator
     * @param DatasetService         $datasetService
     * @param AdminUrlGenerator      $router
     * @param EntityManagerInterface $em
     */
    public function __construct(
        TranslatorInterface $translator,
        DatasetService $datasetService,
        AdminUrlGenerator $router,
        EntityManagerInterface $em
    ) {
        $this->translator = $translator;
        $this->datasetService = $datasetService;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return DatasetDraft::class;
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
        $dataset = $this->translator->trans('content.draft.dataset');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.draft.datasets')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $dataset)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $dataset)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.slug', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * By default, sort ASC page by title in current locale.
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
     * Preview added on edit and index action (need a valid slug to preview).
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Preview a DatasetDraft on Front Office like a Dataset.
        $previewOne = Action::new('previewOne', $this->translator->trans('bo.preview'))
            ->linkToRoute('front.preview.datasetdraft', function (DatasetDraft $dataset): array {
                return [
                    'slug' => ($dataset->getSlug()) ? $dataset->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank'])
        ;
        // Publish the draft ie. create a copy as Page object.
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
                    ucfirst($this->translator->trans('content.draft.dataset'))
                );
            })
            ->add(Crud::PAGE_INDEX, $previewOne)
            ->add(Crud::PAGE_EDIT, $toPublish)
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
                'label'      => ucfirst($this->translator->trans('dataset.prop.title')),
                'attr'       => ['maxlength' => DatasetTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => DatasetTranslation::LEN_TITLE]
                )
                    . ' - ' .
                    $this->translator->trans(
                        'bo.length.helper.max.lamina.spotlight',
                        ['%len_spot%' => $this->getParameter('lame_spotlight_len_max_title')]
                    )
                    . ' - ' .
                    $this->translator->trans(
                        'bo.length.helper.max.lamina.highlighted',
                        ['%len_high%' => $this->getParameter('lame_highlighted_len_max_title')]
                    ),
            ],
            'hook'    => [
                'field_type' => TextareaType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('dataset.prop.hook')),
            ],
            'imageLocale' => [
                'field_type' => ElfinderType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('dataset.prop.img')),
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('dataset.prop.content')),
            ],
            'slug'    => [
                'field_type' => SlugType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('prop.slug')),
                'attr'       => ['maxlength' => DatasetTranslation::LEN_SLUG],
                'target'     => 'title',
                'help'       => $this->translator->trans('prop.slug.help') .
                    ' - ' . $this->translator->trans(
                        'bo.length.helper.max',
                        ['%len%' => DatasetTranslation::LEN_TITLE]
                    ),
            ],
            'imgLicence' => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('prop.img.licence')),
                'attr'       => ['maxlength' => DatasetTranslation::LEN_IMG_LICENCE],
                'help'       => $this->translator->trans('prop.img.licence.help') .
                    ' - ' . $this->translator->trans(
                        'bo.length.helper.max',
                        ['%len%' => DatasetTranslation::LEN_IMG_LICENCE]) . '</br>' .
                        $this->translator->trans('prop.img.licence.help.symbol'
                    ),
            ],
            'imgLegend'  => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('prop.img.legend')),
                'attr'       => ['maxlength' => DatasetTranslation::LEN_IMG_LEGEND],
                'help'       => $this->translator->trans('prop.img.legend.help') . 
                    ' - ' . $this->translator->trans(
                        'bo.length.helper.max',
                        ['%len%' => DatasetTranslation::LEN_IMG_LEGEND]
                    ),
            ]
        ];
        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true);
            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'))
                ->setHelp($this->translator->trans('prop.group.additional.help'));
            $fields[] = UrlField::new('linkDataverse', ucfirst($this->translator->trans('dataset.prop.link')));
            $fields[] = TextareaField::new('datasetQuote', ucfirst($this->translator->trans('dataset.prop.quote')));
            $fields[] = AssociationField::new('actuality', ucfirst($this->translator->trans('dataset.prop.actuality')));
            $fields[] = AssociationField::new('taxonomies', ucfirst($this->translator->trans('content.taxonomy')));
        }  elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('dataset.prop.title')));
            $fields[] = ImageField::new('imageLocale', ucfirst($this->translator->trans('dataset.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = TextField::new('imgLegend', ucfirst($this->translator->trans('prop.img.legend')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = TextField::new(
                'dataset',
                ucfirst(ucfirst($this->translator->trans('datasetdraft.prop.dataset')))
            );
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * Call a Custom service dedicated to publish ie create/update the associated Page object.
     *
     * @param AdminContext $adminContext
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function publishDraft(AdminContext $adminContext)
    {
        /** @var DatasetDraft $draft */
        $draft = $adminContext->getEntity()->getInstance();
        $message = $this->translator->trans('content.publish.success');
        try {
            $this->datasetService->publish($draft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }
        $urlToReturn = $this->router
            ->setController(DatasetCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($draft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }

        /**
     * Duplicate a Draft
     *
     * @param AdminContext    $context
     * @param DatasetDraft    $originalDraft
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function duplicateDraft(AdminContext $context)
    {
        // Get the current item
        $originalDraft = $context->getEntity()->getInstance();
        $message = $this->translator->trans('content.clone.success');

        try {
            // Create a new instance of the entity
            $this->datasetService->duplicate($originalDraft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }

        // Redirect back to the index page
        $urlToReturn = $this->router
            ->setController(DatasetDraftCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($originalDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }
}
