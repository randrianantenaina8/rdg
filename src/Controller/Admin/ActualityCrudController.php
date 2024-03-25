<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Actuality;
use App\Entity\Config;
use App\Service\ActualityService;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class ActualityCrudController extends AbstractLockableCrudController
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
     * @var ActualityService
     */
    private $actualityService;

    /**
     * @param TranslatorInterface $translator
     * @param AdminUrlGenerator   $router
     * @param ActualityService    $actualityService
     */
    public function __construct(
        TranslatorInterface $translator,
        AdminUrlGenerator $router,
        ActualityService $actualityService
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->router->setDashboard(DashboardController::class);
        $this->actualityService = $actualityService;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Actuality::class;
    }

    /**
     * When delete an actuality create a draft.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Actuality) {
            return;
        }
        $actualityDraft = $this->actualityService->findOrCreateDraft($entityInstance);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }

    /**
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.actualities')))
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.slug', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * By default, sort ASC actualities by title in current locale.
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
     * Custom actions to view an Actuality AND to create/edit an ActualityDraft.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Find or create the ActualityDraft object associated to... To edit the 'actuality' object through a draft.
        $draftEdit = Action::new('draftEdit', ucfirst($this->translator->trans('bo.draft')))
            ->linkToCrudAction('findOrCreateDraft');
        // See Actuality on Front Office
        $foView = Action::new('foview', ucfirst($this->translator->trans('bo.foview')))
            ->linkToRoute('front.actuality.show', function (Actuality $actuality): array {
                return [
                    'slug' => ($actuality->getSlug()) ? $actuality->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);

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
                    ucfirst($this->translator->trans('content.actuality'))
                );
            })
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $draftEdit)
            ->add(Crud::PAGE_INDEX, $foView);

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

        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('actuality.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = ImageField::new('imageLocale', ucfirst($this->translator->trans('actuality.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = TextField::new('imgLegend', ucfirst($this->translator->trans('prop.img.legend')));
            $fields[] = DateTimeField::new('publishedAt', ucfirst($this->translator->trans('prop.publishedAt')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * Find or create an ActualityDraft associated to the current Actuality object,
     * then return to its edit form.
     *
     * @param AdminContext $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function findOrCreateDraft(AdminContext $context)
    {
        $actuality = $context->getEntity()->getInstance();

        $actualityDraft = $this->actualityService->findOrCreateDraft($actuality);

        $urlToDraftEdit = $this->router
            ->setController(ActualityDraftCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($actualityDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToDraftEdit);
    }
}
