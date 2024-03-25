<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\Dataset;
use App\Entity\DatasetTranslation;
use App\Entity\Lame\HighlightedLame;
use App\Entity\Lame\SpotLightLame;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
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

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class DatasetCrudController extends AbstractLockableCrudController
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
    private $router;

    /**
     * @var DatasetService
     */
    private $datasetService;

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     * @param AdminUrlGenerator      $router
     * @param DatasetService         $datasetService
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        AdminUrlGenerator $router,
        DatasetService $datasetService
    ) {
        $this->translator = $translator;
        $this->em = $em;
        $this->router = $router;
        $this->router->setDashboard(DashboardController::class);
        $this->datasetService = $datasetService;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Dataset::class;
    }

    /**
     * When delete a dataset create a draft.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Dataset) {
            return;
        }
        $datasetDraft = $this->datasetService->findOrCreateDraft($entityInstance);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
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
        $dataset = $this->translator->trans('content.dataset');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.datasets')))
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $dataset)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.slug', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * By default, sort ASC datasets by title in current locale.
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
        // Find or create the DatasetDraft object associated to... To edit the 'dataset' object through a draft.
        $draftEdit = Action::new('draftEdit', ucfirst($this->translator->trans('bo.draft')))
            ->linkToCrudAction('findOrCreateDraft');
        // See Dataset on Front Office
        $foView = Action::new('foview', ucfirst($this->translator->trans('bo.foview')))
            ->linkToRoute('front.dataset.show', function (Dataset $dataset): array {
                return [
                    'slug' => ($dataset->getSlug()) ? $dataset->getSlug() : Config::ROUTE_ERR_PARAMS,
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
                    ucfirst($this->translator->trans('content.dataset'))
                );
            })
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $draftEdit)
            ->add(Crud::PAGE_INDEX, $foView)
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

        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('dataset.prop.title')));
            $fields[] = ImageField::new('imageLocale', ucfirst($this->translator->trans('dataset.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = TextField::new('imgLegend', ucfirst($this->translator->trans('prop.img.legend')));
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
     * Find or create an ActualityDraft associated to the current Actuality object,
     * then return to its edit form.
     *
     * @param AdminContext $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function findOrCreateDraft(AdminContext $context)
    {
        $dataset = $context->getEntity()->getInstance();

        $datasetDraft = $this->datasetService->findOrCreateDraft($dataset);

        $urlToDraftEdit = $this->router
            ->setController(DatasetDraftCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($datasetDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToDraftEdit);
    }
}
