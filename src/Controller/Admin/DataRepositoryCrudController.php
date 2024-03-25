<?php

namespace App\Controller\Admin;

use App\Entity\DataRepositoryTranslation;
use App\Entity\DataRepository;
use App\Field\Admin\TranslationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Contracts\Translation\TranslatorInterface;


class DataRepositoryCrudController extends AbstractCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface    $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return DataRepository::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $fieldsConfig = [
            'name'   => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('event.prop.title')),
                'attr'       => ['maxlength' => DataRepositoryTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => DataRepositoryTranslation::LEN_TITLE]
                ),
            ],
            'description' => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.description')),
            ],
            'url' => [
                'field_type' => UrlType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.url')),
            ],
            'dataType' => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.dataType')),
            ],
            'repositoryModeration' => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.repository.moderation')),
            ],
            'embargo' => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('content.dataRepository.embargo'))
            ]
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true);
            $fields[] = Field::new('logo', ucfirst($this->translator->trans('content.dataRepository.logo')))
                ->setFormType(ElFinderType::class)
                ->setFormTypeOptions([
                    'instance' => 'image',
                    'enable' => true
                ])
                ->setHelp($this->translator->trans('prop.img.help'));

            // Supporting institutions
            $fields[] = FormField::addPanel( ucfirst($this->translator->trans('content.dataRepository.supportingInstitution')));
            $fields[] = AssociationField::new('supportingInstitutions', ucfirst($this->translator->trans('content.dataRepository.supportingInstitution')))
                ->setRequired(true);
            
            // CatOPIDoR
            $fields[] = FormField::addPanel('CatOPIDoR');
            $fields[] = UrlField::new('catopidorLink', ucfirst($this->translator->trans('content.dataRepository.catopidorLink')))->setColumns(6);
            $fields[] = TextField::new('catopidorIdentifier', ucfirst($this->translator->trans('content.dataRepository.catopidorIdentifier')))->setColumns(4);
            
            // Re3DataLink
            $fields[] = FormField::addPanel('Re3Data');
            $fields[] = UrlField::new('re3dataLink', ucfirst($this->translator->trans('content.dataRepository.re3dataLink')))->setColumns(6);
            $fields[] = TextField::new('re3dataIdentifier', ucfirst($this->translator->trans('content.dataRepository.re3dataIdentifier')))->setColumns(4);

            // Additional informations
            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'));
            $fields[] = TextField::new('repositoryCreationDate', ucfirst($this->translator->trans('content.dataRepository.repository.creation')))->setColumns(2);
            $fields[] = ChoiceField::new('repositoryIdentifier', ucfirst($this->translator->trans('content.dataRepository.repositoryIdentifier')))
                ->setChoices($this->getRepositoryIdentifier())
                ->setColumns(2);
            // Server location and data retention period
            $fields[] = TextField::new('serversLocation', ucfirst($this->translator->trans('content.dataRepository.serversLocation')))->setColumns(6);
            $fields[] = TextField::new('retentionPeriod', ucfirst($this->translator->trans('content.dataRepository.retentionPeriod')))->setColumns(4);    
            
            // Data volume limit
            $fields[] = IntegerField::new('fileVolumeLimit', ucfirst($this->translator->trans('content.dataRepository.file.volumeLimit')))->setColumns(3);
            $fields[] = IntegerField::new('datasetVolumeLimit', ucfirst($this->translator->trans('content.dataRepository.dataset.volumeLimit')))->setColumns(3);
            
            // Certificate
            $fields[] = TextField::new('certificate', ucfirst($this->translator->trans('content.dataRepository.certificate')))->setColumns(6);
            
            // Disciplines & keywords
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('content.dataRepository.disciplines.kewywords')));
            $fields[] = CollectionField::new('disciplinaryAreas', ucfirst($this->translator->trans('content.dataRepository.disciplinary.area')))
                ->renderExpanded()
                ->allowAdd()
                ->setEntryIsComplex(true)
                ->setEntryType(TextType::class, ['label' => false])
                ->setFormTypeOptions([
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'label' => false, // Set to false to hide the label for each text input
                    ],
                    'by_reference' => false
                ]);
            $fields[] = AssociationField::new('disciplines', ucfirst($this->translator->trans('content.dataRepository.discipline')))
                ->setRequired(true)
                ->setColumns(6);
            $fields[] = AssociationField::new('keywords', ucfirst($this->translator->trans('content.dataRepository.keyword')))
                ->setRequired(true)
                ->setColumns(6);

        } else {

            $fields[] = TextField::new('name', ucfirst($this->translator->trans('content.dataRepository.name')));
            $fields[] = FormField::addPanel($this->translator->trans('prop.group.additional'))
                ->setHelp($this->translator->trans('prop.group.additional.help'));
            $fields[] = UrlField::new('url', ucfirst($this->translator->trans('content.dataRepository.url')));
            $fields[] = UrlField::new('catopidorLink', ucfirst($this->translator->trans('content.dataRepository.catopidorLink')));
            $fields[] = UrlField::new('re3dataLink', ucfirst($this->translator->trans('content.dataRepository.re3dataLink')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));

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
        $dataRepository = $this->translator->trans('content.dataRepository');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.dataRepository.dataRepositories')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $dataRepository)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $dataRepository)
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
                    ucfirst($this->translator->trans('content.dataRepository'))
                );
            });
    }

    /**
     * @return int[]
     */
    protected function getRepositoryIdentifier()
    {
        $identifiers = [
            'ark' => 'ark',
            'handle' => 'handle',
            'doi' => 'doi',
            'uri' => 'uri',
            'autre' => 'autre',
        ];

        return $identifiers;
    }
}
