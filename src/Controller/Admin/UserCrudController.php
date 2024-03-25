<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_COORD")
 */
class UserCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UserInterface|null
     */
    protected $currentUser;

    /**
     * @var RoleHierarchyInterface
     */
    protected $roleHierarchy;


    /**
     * @param TranslatorInterface    $translator
     * @param Security               $security
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(
        TranslatorInterface $translator,
        Security $security,
        RoleHierarchyInterface $roleHierarchy
    ) {
        $this->translator = $translator;
        $this->currentUser = $security->getUser();
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Add validation on create et edit action.
     * Customize page title.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $user = $this->translator->trans('content.user');

        return parent::configureCrud($crud)
            ->setFormOptions(
        // New form options
                ['validation_groups' => ['new']],
                // Edit form options
                ['validation_groups' => ['edit']],
            )
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.users')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $user)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $user)
        ;
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
        return  parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX => 'ROLE_COORD',
                Action::NEW => 'ROLE_COORD',
                Action::EDIT => 'ROLE_COORD',
                Action::DELETE => 'ROLE_ADMIN',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.user'))
                );
            })
        ;
    }

    /**
     * Filter user list according to current user's role.
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
        $currentRoles = $this->roleHierarchy->getReachableRoleNames($this->currentUser->getRoles());
        $queryIndex = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $nbRoles = count($currentRoles);

        for ($i = 0; $i < $nbRoles; $i++) {
            if (0 === $i) {
                $queryIndex->where(
                    "JSON_SEARCH(" . $queryIndex->getRootAliases()[0] . ".roles, 'all',  :role" . $i .
                    ") IS NOT NULL"
                )
                    ->setParameter('role' . $i, $currentRoles[$i]);
            } else {
                $queryIndex->orWhere(
                    "JSON_SEARCH(" . $queryIndex->getRootAliases()[0] . ".roles, 'all',  :role" . $i . ") IS NOT NULL"
                )
                    ->setParameter('role' . $i, $currentRoles[$i]);
            }
        }
        $queryIndex->addOrderBy('entity.username', 'ASC');

        return $queryIndex;
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('username', ucfirst($this->translator->trans('user.prop.username')))
                ->setHelp($this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_USERNAME])),
            EmailField::new('email', ucfirst($this->translator->trans('user.prop.email')))
                ->setHelp($this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_EMAIL])),
            BooleanField::new('isActivated', ucfirst($this->translator->trans('user.prop.active')))
                ->setHelp($this->translator->trans('user.prop.active.help')),
            ChoiceField::new('roles', ucfirst($this->translator->trans('user.prop.roles')))
                ->allowMultipleChoices()
                ->setChoices($this->getRoles()),
        ];

        $passwordField = TextField::new('password', ucfirst($this->translator->trans('profil.label.password')))
            ->setFormType(PasswordType::class)
            ->setHelp($this->translator->trans('password.pattern.help') . ' - ' .
                $this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_PASSWORD]));
        if ($pageName === Crud::PAGE_EDIT) {
            $passwordField->hideWhenUpdating();
        } elseif ($pageName === Crud::PAGE_NEW) {
            $fields[] = $passwordField;
            $fields[] = TextField::new('clearPassword')
                ->setLabel(ucfirst($this->translator->trans('profile.label.newpass2')))
                ->setFormType(PasswordType::class)
                ->setFormTypeOption('empty_data', '')
                ->setRequired(true);
        }

        return $fields;
    }

    /**
     * @return array
     */
    protected function getRoles()
    {
        $roles = [];

        foreach (User::ROLES as $label => $role) {
            $roles[$this->translator->trans($label)] = $role;
        }
        return $roles;
    }
}
