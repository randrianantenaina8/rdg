<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\FaqBlock;
use App\Entity\FaqBlockTranslation;
use App\Entity\HeadingFaq;
use App\Field\Admin\TranslationField;
use App\Form\Admin\FaqHeadingFormType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @IsGranted("ROLE_CONTRIB")
 */
class FaqBlockCrudController extends AbstractLockableCrudController
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
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return FaqBlock::class;
    }

    /**
     * Set translation template field.
     * Customize pages'name.
     * Added template to use wysiwyg.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $faq = $this->translator->trans('content.faqblock');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.faqblocks')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $faq)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $faq)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields([
                'translations.title',
                'translations.description',
                'createdBy.username',
                'updatedBy.username'
            ]);
    }

    /**
     * By default, sort ASC FaqBlocks by title in current locale.
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
            ->addOrderBy('tt.title', 'ASC');

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
                    ucfirst($this->translator->trans('content.faqblock'))
                );
            });
    }

    /**
     * Added a first link to a heading (with default value [weight = 10])
     *
     * @param string $entityFqcn
     *
     * @return FaqBlock
     */
    public function createEntity(string $entityFqcn)
    {
        $faq = new FaqBlock();
        $faq->addHeading(new HeadingFaq());

        return $faq;
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
            'title'   => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('faqblock.prop.title')),
                'attr'       => ['maxlength' => FaqBlockTranslation::LEN_TITLE],
                'help'       => $this->translator->trans(
                    'bo.length.helper.max',
                    ['%len%' => FaqBlockTranslation::LEN_TITLE]
                ),
            ],
            'content' => [
                'field_type' => CKEditorType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('faqblock.prop.content')),
            ],
        ];

        // We define slug field everytime according to its position in differents forms.
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
                                $this->translator->trans('prop.translations.help.one'),
                            ]
                        ]
                    ]
                );
            $fields[] = CollectionField::new('headings', ucfirst($this->translator->trans('faqblock.prop.headings')))
                ->renderExpanded()
                ->setEntryIsComplex(true)
                ->setEntryType(FaqHeadingFormType::class)
                ->setRequired(true)
                ->setHelp($this->translator->trans('faqblock.prop.headings.help'));
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('faqblock.prop.title')));
            $fields[] = BooleanField::new(
                'faqHighlighted',
                ucfirst($this->translator->trans('faqblock.prop.faqHighlighted'))
            )->setDisabled(true);
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
        }
        return $fields;
    }
}
