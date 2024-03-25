<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\AdditionalHelp;
use App\Entity\AdditionalHelpTranslation;
use App\Entity\Guide;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class AdditionalHelpCrudController extends AbstractLockableCrudController
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
    private $requestStack;

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
        return AdditionalHelp::class;
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
        $help = $this->translator->trans('content.additionalHelp');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.additionalHelps')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $help)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $help)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields([
                'translations.name',
                'translations.description',
            ]);
    }

    /**
     * By default, sort ASC additional helpers by name in current locale.
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
                    ucfirst($this->translator->trans('content.additionalHelp'))
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
        $defaultProtocol = 'https';
        $fields = [];
        $fieldsConfig = [
            'name'        => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('additionalHelp.prop.name')),
                'attr'       => ['maxlength' => AdditionalHelpTranslation::LEN_NAME],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => AdditionalHelpTranslation::LEN_NAME]
                ),
            ],
            'description' => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('additionalHelp.prop.description')),
                'attr'       => ['maxlength' => AdditionalHelpTranslation::LEN_DESC],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => AdditionalHelpTranslation::LEN_DESC]
                ),
            ],
        ];

        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
                ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = TranslationField::new('translations', false, $fieldsConfig)
                ->setLabel(false)
                ->setRequired(true);

            $fields[] = FormField::addPanel($this->translator->trans('additionalHelp.group.links'))
                ->setHelp($this->translator->trans('additionalHelp.group.links.help'));
            $fields[] = AssociationField::new('guide', ucfirst($this->translator->trans('additionalHelp.prop.guide')))
                ->setFormTypeOptions(
                    [
                        'choices' => $this->em->getRepository(Guide::class)->findAllByLocaleOrdered(
                            $this->requestStack->getCurrentRequest()->getLocale()
                        ),
                    ]
                );
            $fields[] = UrlField::new('link', ucfirst($this->translator->trans('additionalHelp.prop.link')))
                ->setHelp($this->translator->trans('additionalHelp.prop.link.help') . ' - ' . $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => AdditionalHelp::LEN_LINK - mb_strlen($defaultProtocol . '://')]
                ))
                ->setFormTypeOptions(
                    [
                        'default_protocol' => $defaultProtocol,
                    ]
                );

            $fields[] = FormField::addPanel(' ');
            $fields[] = BooleanField::new(
                'displayed',
                ucfirst($this->translator->trans('additionalHelp.prop.displayed'))
            )
                ->setHelp($this->translator->trans('additionalHelp.prop.displayed.help'));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')))
                ->setHelp($this->translator->trans('additionalHelp.prop.weight.help'));
        } else {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('additionalHelp.prop.name')));
            $fields[] = BooleanField::new(
                'displayed',
                ucfirst($this->translator->trans('additionalHelp.prop.displayed'))
            );
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
        }

        return $fields;
    }
}
