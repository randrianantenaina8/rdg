<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\MenuBasic;
use App\Entity\MenuBasicTranslation;
use App\Entity\Page;
use App\Field\Admin\TranslationField;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_COORD")
 */
class MenuBasicCrudController extends AbstractLockableCrudController
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
     * Customize pages'name.
     * Set form themes to used to get TranslationField.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $menu = $this->translator->trans('content.menuBasic');

        return parent::configureCrud($crud)
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setFormOptions(
                ['validation_groups' => 'links']
            )
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.menuBasics')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $menu)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $menu)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.label']);
    }

    /**
     * By default, sort ASC MenuBasic by labels in current locale.
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
            ->addOrderBy('tt.label', 'ASC');

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
                    ucfirst($this->translator->trans('content.menuBasic'))
                );
            });
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return MenuBasic::class;
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
            'label' => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('menuBasic.prop.label')),
                'attr'       => ['maxlength' => MenuBasicTranslation::LEN_LABEL],
                'help'       => $this->translator->trans('menuBasic.prop.label.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => MenuBasicTranslation::LEN_LABEL]),
            ]
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
                                $this->translator->trans('prop.translations.help.all'),
                            ]
                        ]
                    ]
                );
            $fields[] = BooleanField::new(
                'isActivated',
                ucfirst($this->translator->trans('menuBasic.prop.isactivated'))
            );
            $fields[] = AssociationField::new('pageLink', ucfirst($this->translator->trans('menuBasic.prop.page')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Page::class)->findByPublishedOrdered(
                        $this->requestStack->getCurrentRequest()->getLocale()
                    ),
                ])
                ->setHelp(ucfirst($this->translator->trans('menuBasic.prop.link.help')));
            $fields[] = ChoiceField::new('systemLink', ucfirst($this->translator->trans('menuBasic.prop.system')))
                ->setChoices($this->getSystemChoices())
                ->setHelp(ucfirst($this->translator->trans('menuBasic.prop.link.help')));
            $fields[] = UrlField::new('externalLink', ucfirst($this->translator->trans('menuBasic.prop.external')))
                ->setHelp(ucfirst($this->translator->trans('menuBasic.prop.link.help')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')))
                ->setHelp($this->translator->trans('prop.weight.help'));
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('label', ucfirst($this->translator->trans('menuBasic.prop.label')));
            $fields[] = BooleanField::new(
                'isActivated',
                ucfirst($this->translator->trans('menuBasic.prop.isactivated'))
            );
            $fields[] = TextField::new('pageLink', ucfirst($this->translator->trans('menuBasic.prop.page')));
            $fields[] = ChoiceField::new('systemLink', ucfirst($this->translator->trans('menuBasic.prop.system')))
                ->setChoices($this->getSystemChoices());
            $fields[] = UrlField::new('externalLink', ucfirst($this->translator->trans('menuBasic.prop.external')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')));
        }
        return $fields;
    }

    /**
     * Get an array of system routes available in Config.
     * The Config object to find is defined by its name that must be at Config::ROUTE.
     *
     * @return array
     */
    public function getSystemChoices()
    {
        $choices = [];
        $configRoute = $this->em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
        $systemRoutes = $configRoute->getData();

        foreach ($systemRoutes as $name => $systemRoute) {
            $choices[$name] = $systemRoute;
        }

        return $choices;
    }
}
