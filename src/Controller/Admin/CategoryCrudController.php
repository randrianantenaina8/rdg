<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Field\Admin\TranslationField;
use App\Form\Admin\CategoryGuideFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class CategoryCrudController extends AbstractLockableCrudController
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
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     * @param RequestStack           $requestStack
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    /**
     * Set permissions on CRUD actions.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $category = $this->translator->trans('content.category');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.categories')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $category)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $category)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields([
                'translations.name',
                'weight',
                'createdBy.username',
                'updatedBy.username'
            ]);
    }

    /**
     * Set permissions on CRUD actions.
     * Customize label on create button.
     * Do not display delete action if Category is linked to Guides.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions)
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
                    ucfirst($this->translator->trans('content.category'))
                );
            })
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ;

        $deleteAction = parent::configureActions($actions)
            ->getAsDto(Crud::PAGE_INDEX)
            ->getAction(Crud::PAGE_INDEX, Action::DELETE);
        if (!is_null($deleteAction)) {
            $deleteAction->setDisplayCallable(function (Category $category) {
                if (count($category->getGuides())) {
                    return false;
                }
                return true;
            });
        }

        return $actions;
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
            'name'        => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('category.prop.name')),
                'attr'       => ['maxlength' => CategoryTranslation::LEN_NAME],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => CategoryTranslation::LEN_NAME]
                ),
            ],
        ];
        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
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
                                $this->translator->trans('prop.translations.help.all'),
                            ]
                        ]
                    ]
                );
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')))
                ->setHelp($this->translator->trans('prop.weight.help'));
            $fields[] = CollectionField::new('guides', ucfirst($this->translator->trans('category.prop.guides')))
                ->renderExpanded()
                ->setEntryIsComplex(true)
                ->setEntryType(CategoryGuideFormType::class)
                ->setHelp($this->translator->trans('category.prop.guides.help'));
        } else {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('category.prop.name')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }

        return $fields;
    }
}
