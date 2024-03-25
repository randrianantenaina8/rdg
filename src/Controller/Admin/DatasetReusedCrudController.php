<?php

namespace App\Controller\Admin;

use App\Entity\DatasetReused;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Contracts\Translation\TranslatorInterface;

class DatasetReusedCrudController extends AbstractCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return DatasetReused::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $fieldsConfig = [
            'description' => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.datasetReused.description')),
            ],
        ];
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = BooleanField::new('enable', ucfirst($this->translator->trans('content.datasetReused.enable')));
            $fields[] = TextField::new('publicationTitle', ucfirst($this->translator->trans('content.datasetReused.publicationTitle')));
            $fields[] = AssociationField::new('reuseType', ucfirst($this->translator->trans('content.datasetReused.reuseType')))
                ->setRequired(true);
            $fields[] = TextareaField::new('description', ucfirst($this->translator->trans('content.datasetReused.description')))
                ->setFormType(CKEditorType::class)
                ->setColumns(12);
            $fields[] = TextField::new('author', ucfirst($this->translator->trans('content.datasetReused.author')));
            $fields[] = TextField::new('authorAffiliation', ucfirst($this->translator->trans('content.datasetReused.authorAffiliation')));
            $fields[] = DateTimeField::new('publicationDate', ucfirst($this->translator->trans('content.datasetReused.publicationDate')));
            $fields[] = UrlField::new('datasetReusedDoi', ucfirst($this->translator->trans('content.datasetReused.datasetReusedDoi')));
            $fields[] = UrlField::new('newDatasetDoi', ucfirst($this->translator->trans('content.datasetReused.newDatasetDoi')));
            $fields[] = UrlField::new('newDatasetUrl', ucfirst($this->translator->trans('content.datasetReused.newDatasetUrl')));
            $fields[] = Field::new('image', ucfirst($this->translator->trans('content.datasetReused.image')))
                ->setFormType(ElFinderType::class)
                ->setFormTypeOptions([
                    'instance' => 'image',
                    'enable' => true
                ])
                ->setHelp($this->translator->trans('prop.img.help'));
        } else {
            $fields[] = TextField::new('publicationTitle', ucfirst($this->translator->trans('content.datasetReused.publicationTitle')));
            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'))
                ->setHelp($this->translator->trans('prop.group.additional.help'));
            $fields[] = UrlField::new('newDatasetUrl', ucfirst($this->translator->trans('content.datasetReused.newDatasetUrl')));
            $fields[] = UrlField::new('newDatasetDoi', ucfirst($this->translator->trans('content.datasetReused.newDatasetDoi')));
            $fields[] = UrlField::new('authorAffiliation', ucfirst($this->translator->trans('content.datasetReused.authorAffiliation')));
        }
        return $fields;
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
        $datasetReused = $this->translator->trans('content.datasetReused');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.datasetReused.datasetReuseds')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $datasetReused)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $datasetReused)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.name', 'translations.description']);
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
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_CONTRIB',
                Action::EDIT   => 'ROLE_CONTRIB',
                Action::DELETE => 'ROLE_CONTRIB',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.datasetReused'))
                );
            });
    }
}
