<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\AlertMsg;
use App\Entity\AlertMsgTranslation;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_COORD")
 */
class AlertMsgCrudController extends AbstractLockableCrudController
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
        return AlertMsg::class;
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
        $alert = $this->translator->trans('content.alert');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.alerts')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $alert)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $alert)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.name', 'translations.message', 'updatedBy.username']);
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
                    ucfirst($this->translator->trans('content.alert'))
                );
            });
    }

    /**
     * By default, sort ASC categories by name in current locale.
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
            ->addOrderBy('tt.name', 'ASC');

        return $qb;
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
            'name'    => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('alert.prop.name')),
                'help'       => $this->translator->trans('alert.prop.name.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => AlertMsgTranslation::LEN_NAME]),
                'attr'       => ['maxlength' => AlertMsgTranslation::LEN_NAME],
            ],
            'message' => [
                'field_type' => CKEditorType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('alert.prop.msg')),
                'attr'       => ['maxlength' => AlertMsgTranslation::LEN_MESSAGE],
            ],
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_DETAIL) {
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
            $fields[] = ChoiceField::new('type', $this->translator->trans('alert.prop.type'))
                ->setChoices($this->getTypes())
                ->setHelp($this->translator->trans('alert.prop.type.help'));
            $fields[] = DateTimeField::new('dateStart', ucfirst($this->translator->trans('alert.prop.begin')))
                ->setHelp($this->translator->trans('alert.prop.begin.help'));
            $fields[] = DateTimeField::new('dateEnd', ucfirst($this->translator->trans('alert.prop.end')))
                ->setHelp($this->translator->trans('alert.prop.end.help'));
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('alert.prop.name')));
            $fields[] = ChoiceField::new('type', $this->translator->trans('alert.prop.type'))
                ->setChoices($this->getTypes());
            $fields[] = DateTimeField::new('dateStart', ucfirst($this->translator->trans('alert.prop.begin')));
            $fields[] = DateTimeField::new('dateEnd', ucfirst($this->translator->trans('alert.prop.end')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        $types = [];

        foreach (AlertMsg::TYPE as $label => $type) {
            $types[ucfirst($this->translator->trans($label))] = $type;
        }
        return $types;
    }
}
