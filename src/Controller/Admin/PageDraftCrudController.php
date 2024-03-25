<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\PageDraft;
use App\Entity\PageTranslation;
use App\Field\Admin\TranslationField;
use App\Service\PageService;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class PageDraftCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param PageService         $pageService
     * @param AdminUrlGenerator   $router
     */
    public function __construct(
        TranslatorInterface $translator,
        PageService $pageService,
        AdminUrlGenerator $router
    ) {
        $this->translator = $translator;
        $this->pageService = $pageService;
        $this->router = $router;
    }

    public static function getEntityFqcn(): string
    {
        return PageDraft::class;
    }

    /**
     * Add Customize wysiwyg them to forms and others to get Translated field.
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $page = $this->translator->trans('content.draft.page');

        return $crud
            ->setFormOptions(
            // New form options
                ['validation_groups' => ['new']],
                // Edit form options
                ['validation_groups' => ['edit']],
            )
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.draft.pages')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $page)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $page)
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
        // Preview a PageDraft on Front Office like a Page.
        $previewOne = Action::new('previewOne', $this->translator->trans('bo.preview'))
            ->linkToRoute('front.preview.pagedraft', function (PageDraft $page): array {
                return [
                    'slug' => ($page->getSlug()) ? $page->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);
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
                Action::DELETE => 'ROLE_COORD',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.draft.page'))
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
                'label'      => ucfirst($this->translator->trans('page.prop.title')),
                'attr'       => ['maxlength' => PageTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => PageTranslation::LEN_TITLE]
                ),
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('page.prop.content')),
            ],
            'slug'    => [
                'field_type' => SlugType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('prop.slug')),
                'attr'       => ['maxlength' => PageTranslation::LEN_SLUG],
                'target'     => 'title',
                'help'       => $this->translator->trans('prop.slug.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => PageTranslation::LEN_SLUG]),
            ],
        ];

        // We define slug field everytime according to its position in differents forms.
        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true)
                ->setFormTypeOptions(
                    [
                        'attr' => [
                            'helper' => [
                                $this->translator->trans('prop.translations.help.one'),
                            ]
                        ]
                    ]
                );
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('page.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = TextField::new('page', ucfirst($this->translator->trans('pagedraft.prop.page')));
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
        /** @var PageDraft $draft */
        $draft = $adminContext->getEntity()->getInstance();
        $message = $this->translator->trans('content.publish.success');
        try {
            $this->pageService->publish($draft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }
        $urlToReturn = $this->router
            ->setController(PageCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($draft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }

    /**
     * Duplicate a Draft
     *
     * @param AdminContext    $context
     * @param ActualityDraft  $originalDraft
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
            $this->pageService->duplicate($originalDraft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }

        // Redirect back to the index page
        $urlToReturn = $this->router
            ->setController(PageDraftCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($originalDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }
}
