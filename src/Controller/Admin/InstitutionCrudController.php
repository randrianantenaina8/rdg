<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Institution;
use App\Entity\InstitutionTranslation;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @IsGranted("ROLE_COORD")
 */
class InstitutionCrudController extends AbstractLockableCrudController
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
        return Institution::class;
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
        $institution = $this->translator->trans('content.institution');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.institutions')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $institution)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $institution)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(
                [
                    'translations.acronym',
                    'translations.extendedName',
                    'createdBy.username',
                    'updatedBy.username'
                ]
            )
            ;
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
                Action::INDEX => 'ROLE_COORD',
                Action::NEW => 'ROLE_COORD',
                Action::EDIT => 'ROLE_COORD',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('content.institution'))
                );
            })
            ;
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
            'acronym' => [
                'field_type' => TextType::class,
                'required' => true,
                'label' => ucfirst($this->translator->trans('institution.prop.acronym')),
                'attr' => ['maxlength' => InstitutionTranslation::LEN_ACRONYM],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => InstitutionTranslation::LEN_ACRONYM]
                ),
            ],
            'extendedName' => [
                'field_type' => TextType::class,
                'required' => true,
                'label' => ucfirst($this->translator->trans('institution.prop.extended')),
                'attr' => ['maxlength' => InstitutionTranslation::LEN_EXT_NAME],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => InstitutionTranslation::LEN_EXT_NAME]
                ),
            ],
            'description' => [
                'field_type' => CKEditorType::class,
                'required' => false,
                'label' => ucfirst($this->translator->trans('institution.prop.desc')),
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
            $fields[] = Field::new('image', ucfirst($this->translator->trans('actuality.prop.img')))
                ->setFormType(ElFinderType::class)
                ->setFormTypeOptions([
                    'instance' => 'image',
                    'enable' => true
                ])
                ->setHelp($this->translator->trans('prop.img.help'));
            $fields[] = AssociationField::new(
                'dataWorkshops',
                ucfirst($this->translator->trans('institution.prop.dataworkshops'))
            )
                ->setRequired(false)
                ->setFormTypeOptionIfNotSet('by_reference', false)
                ->setHelp($this->translator->trans('institution.prop.dataworkshops.help'));
            $fields[] = UrlField::new(
                'urlInstitution',
                ucfirst($this->translator->trans('institution.prop.urlInstitution'))
            )
                ->setRequired(false);
            $fields[] = UrlField::new(
                'urlCollection',
                ucfirst($this->translator->trans('institution.prop.urlcollection'))
            )
                ->setRequired(false);
            $fields[] = UrlField::new(
                'urlCollectionContact',
                ucfirst($this->translator->trans('institution.prop.urlcontact'))
            )
                ->setRequired(false)
                ->setHelp($this->translator->trans('institution.prop.urlcontact.help'));
            $fields[] = UrlField::new(
                'urlOpenScience',
                ucfirst($this->translator->trans('institution.prop.urlscience'))
            )
                ->setRequired(false)
                ->setHelp($this->translator->trans('institution.prop.urlscience.help'));
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('acronym', ucfirst($this->translator->trans('institution.prop.acronym')));
            $fields[] = ImageField::new('image', ucfirst($this->translator->trans('institution.prop.img')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }
        return $fields;
    }
}
