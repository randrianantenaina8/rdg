<?php

namespace App\Controller\Admin;

use App\Entity\ProjectTeam;
use App\Service\ProjectTeamService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class ProjectTeamCrudController extends AbstractLockableCrudController
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
     * @var ProjectTeamService
     */
    private $projectTeamService;

    /**
     * @param TranslatorInterface    $translator
     * @param AdminUrlGenerator      $router
     * @param ProjectTeamService     $projectTeamService
     */
    public function __construct(
        TranslatorInterface $translator,
        AdminUrlGenerator $router,
        ProjectTeamService $projectTeamService
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->projectTeamService = $projectTeamService;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return ProjectTeam::class;
    }

    /**
     * When delete a guide create a draft.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof ProjectTeam) {
            return;
        }
        $projectTeamDraft = $this->projectTeamService->findOrCreateDraft($entityInstance);
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
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.project.members')))
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['name', 'translations.role', 'createdBy.username', 'updatedBy.username']);
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
        // Find or create the ProjectTeamDraft object associated to... To edit the 'member' object through a draft.
        $draftEdit = Action::new('draftEdit', ucfirst($this->translator->trans('bo.draft')))
            ->linkToCrudAction('findOrCreateDraft');

        $actions = parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_CONTRIB',
                Action::EDIT   => 'ROLE_CONTRIB',
                Action::DELETE => 'ROLE_CONTRIB',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) . ' ' . 
                    ucfirst($this->translator->trans('prop.team.member'))
                );
            })
            ->disable(Action::NEW, Action::EDIT, Action::DETAIL)
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

        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('content.member.name')));
            $fields[] = ImageField::new('image', ucfirst($this->translator->trans('actuality.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('content.member.position')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')))
                ->setSortable(false);
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')))
                ->setSortable(false);
        }

        return $fields;
    }

    /**
     * Find or create a Member associated to the current Team Project object,
     * then return to its edit form.
     *
     * @param AdminContext $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function findOrCreateDraft(AdminContext $context)
    {
        $member = $context->getEntity()->getInstance();

        $memberDraft = $this->projectTeamService->findOrCreateDraft($member);

        $urlToDraftEdit = $this->router
            ->setController(ProjectTeamDraftCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($memberDraft->getId())
            ->generateUrl();

        return $this->redirect($urlToDraftEdit);
    }
}
