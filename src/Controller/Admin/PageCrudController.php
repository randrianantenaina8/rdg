<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\Page;
use App\Service\PageService;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class PageCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @param TranslatorInterface $translator
     * @param AdminUrlGenerator   $router
     * @param PageService         $pageService
     */
    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $router, PageService $pageService)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->router->setDashboard(DashboardController::class);
        $this->pageService = $pageService;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    /**
     * When delete a page create a draft.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Page) {
            return;
        }
        $pageDraft = $this->pageService->findOrCreateDraft($entityInstance);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
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
        return $crud
            ->setFormOptions(
            // New form options
                ['validation_groups' => ['new']],
                // Edit form options
                ['validation_groups' => ['edit']],
            )
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.published.pages')))
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
        // Find or create the PageDraft object associated to... To edit the 'page' object through a draft.
        $draftEdit = Action::new('draftEdit', ucfirst($this->translator->trans('bo.draft')))
            ->linkToCrudAction('findOrCreateDraft');
        // See Page on Front Office
        $foView = Action::new('foview', ucfirst($this->translator->trans('bo.foview')))
            ->linkToRoute('front.page.show', function (Page $page): array {
                return [
                    'slug' => ($page->getSlug()) ? $page->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);

        $actions = parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_ADMIN',
                Action::EDIT   => 'ROLE_ADMIN',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $foView)
            ->add(Crud::PAGE_INDEX, $draftEdit);
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

        // We define slug field everytime according to its position in differents forms.
        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('page.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * Find or create a PageDraft associated to the current Page object,
     * then return to its edit form.
     *
     * @param AdminContext $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function findOrCreateDraft(AdminContext $context)
    {
        $page = $context->getEntity()->getInstance();
        $pageDraft = $this->pageService->findOrCreateDraft($page);

        $urlToDraftEdit = $this->router
            ->setController(PageDraftCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($pageDraft->getId())
            ->generateUrl();
        return $this->redirect($urlToDraftEdit);
    }
}
