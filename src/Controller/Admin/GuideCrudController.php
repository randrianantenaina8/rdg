<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\CategoryGuide;
use App\Entity\Config;
use App\Entity\Guide;
use App\Entity\GuideTranslation;
use App\Field\Admin\TranslationField;
use App\Form\Admin\GuideAdditionalHelpFormType;
use App\Form\Admin\GuideCategoryFormType;
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
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
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

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class GuideCrudController extends AbstractLockableCrudController
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
        return Guide::class;
    }

    /**
     * When delete a guide create a draft.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Guide) {
            return;
        }
        $guideDraft = $this->guideService->findOrCreateDraft($entityInstance);
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
        $guide = $this->translator->trans('content.guide');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.guides')))
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
        // Find or create the GuideDraft object associated to... To edit the 'guide' object through a draft.
        $draftEdit = Action::new('draftEdit', ucfirst($this->translator->trans('bo.draft')))
            ->linkToCrudAction('findOrCreateDraft');
        // See Guide on Front Office
        $foView = Action::new('foview', ucfirst($this->translator->trans('bo.foview')))
            ->linkToRoute('front.guide.show', function (Guide $guide): array {
                return [
                    'slug' => ($guide->getSlug()) ? $guide->getSlug() : Config::ROUTE_ERR_PARAMS,
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
                    ucfirst($this->translator->trans('content.guide'))
                );
            })
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $draftEdit)
            ->add(Crud::PAGE_INDEX, $foView)
            ;

        return $actions;
    }

    /**
     * Added a first link to a category (with default value [weight = 10])
     *
     * @param string $entityFqcn
     *
     * @return Guide
     */
    public function createEntity(string $entityFqcn)
    {
        $guide = new Guide();
        $guide->addCategory(new CategoryGuide());

        return $guide;
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
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('guide.prop.title')));
            $fields[] = SlugField::new('slug', ucfirst($this->translator->trans('prop.slug')))
                ->setTargetFieldName('title');
            $fields[] = ImageField::new('imageLocale', ucfirst($this->translator->trans('guide.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = TextField::new('imgLegend', ucfirst($this->translator->trans('prop.img.legend')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * Find or create a GuideDraft associated to the current Guide object,
     * then return to its edit form.
     *
     * @param AdminContext $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function findOrCreateDraft(AdminContext $context)
    {
        $guide = $context->getEntity()->getInstance();

        $guideDraft = $this->guideService->findOrCreateDraft($guide);

        $urlToDraftEdit = $this->router
            ->setController(GuideDraftCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($guideDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToDraftEdit);
    }
}
