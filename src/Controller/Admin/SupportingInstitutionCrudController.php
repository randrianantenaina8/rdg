<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\SupportingInstitution;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Field\Admin\TranslationField;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class SupportingInstitutionCrudController extends AbstractCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface    $translator
     */
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }


    public static function getEntityFqcn(): string
    {
        return SupportingInstitution::class;
    }

    /**
     * Add Customize wysiwyg them to forms.
     * Customize pages'name.
     *
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $supportingInstitution = $this->translator->trans('content.dataRepository.supportingInstitution');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.dataRepository.supportingInstitution')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('content.dataRepository.supportingInstitution.add')))
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')))
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['createdBy.username', 'updatedBy.username', 'translations.name']);
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
        return parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_COORD',
                Action::NEW    => 'ROLE_COORD',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add'))
                );
            });
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];

        $fieldsConfig = [
            'name' => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.name'))
            ],
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            
            $fields[] = TranslationField::new(
                'translations', ucfirst($this->translator->trans('prop.translations')), $fieldsConfig
            )
            ->setRequired(true)
            ->setColumns(6);

        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('content.dataRepository.name')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')))
                ->setSortable(false);
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')))
                ->setSortable(false);
        }

        return $fields;
    }
}
