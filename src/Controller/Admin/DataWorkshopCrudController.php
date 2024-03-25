<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\DataWorkshop;
use App\Entity\DataWorkshopTranslation;
use App\Field\Admin\TranslationField;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @IsGranted("ROLE_COORD")
 */
class DataWorkshopCrudController extends AbstractLockableCrudController
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

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return DataWorkshop::class;
    }

    /**
     * Add Customize wysiwyg them to forms.
     * Set permissions on CRUD actions.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $workshop = $this->translator->trans('content.dataworkshop');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.dataworkshops')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $workshop)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $workshop)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields([
                'translations.acronym',
                'translations.extendedName',
                'createdBy.username',
                'updatedBy.username'
            ]);
    }

    /**
     * By default, sort ASC dataworkshops by acronym in current locale.
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
            ->addOrderBy('tt.acronym', 'ASC');

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
        return parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_COORD',
                Action::NEW    => 'ROLE_COORD',
                Action::EDIT   => 'ROLE_COORD',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.dataworkshop'))
                );
            });
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
            'acronym'      => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('dataworkshop.prop.acronym')),
                'attr'       => ['maxlength' => DataWorkshopTranslation::LEN_ACRONYM],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => DataWorkshopTranslation::LEN_ACRONYM]
                ),
            ],
            'extendedName' => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('dataworkshop.prop.extended')),
                'attr'       => ['maxlength' => DataWorkshopTranslation::LEN_EXT_NAME],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => DataWorkshopTranslation::LEN_EXT_NAME]
                ),
            ],
            'description'  => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('dataworkshop.prop.desc')),
            ],
        ];
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = TranslationField::new(
                'translations',
                ucfirst($this->translator->trans('prop.translations')),
                $fieldsConfig
            )
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
            $fields[] = Field::new('image', ucfirst($this->translator->trans('dataworkshop.prop.img')))
                ->setFormType(ElFinderType::class)
                ->setFormTypeOptions([
                    'instance' => 'image',
                    'enable' => true
                ])
                ->setHelp($this->translator->trans('prop.img.help'));
            $fields[] = ChoiceField::new('workshopType',  ucfirst($this->translator->trans('prop.workshopType')))
                ->setChoices([
                    $this->translator->trans('dataworkshop.prop.labeled') => 'Labellisé',
                    $this->translator->trans('dataworkshop.prop.trajectory') => 'Sur la trajectoire',
                ])
                ->setRequired(true);
            $fields[] = AssociationField::new(
                'institutions',
                ucfirst($this->translator->trans('dataworkshop.prop.institutions'))
            )
                ->setRequired(false)
                ->setHelp($this->translator->trans('dataworkshop.prop.institutions.help'));
            $fields[] = UrlField::new(
                'urlDataWorkshop',
                ucfirst($this->translator->trans('dataworkshop.prop.urlDataWorkshop'))
            )
                ->setRequired(false);
            
           
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('acronym', ucfirst($this->translator->trans('dataworkshop.prop.acronym')));
            $fields[] = TextField::new('workshopType', ucfirst($this->translator->trans('prop.workshopType')));
            $fields[] = ImageField::new('image', ucfirst($this->translator->trans('dataworkshop.prop.img')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }
        return $fields;
    }
}
