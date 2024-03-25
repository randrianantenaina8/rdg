<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Logigram;
use App\Entity\LogigramTranslation;
use App\Form\Admin\LogigramStepFormType;
use App\Field\Admin\TranslationField;
use App\Entity\Dataset;
use App\Entity\Page;
use App\Entity\Guide;
use App\Entity\Actuality;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Config;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use App\Service\LogigramService;

/**
 * @IsGranted("ROLE_COORD")
 */
class LogigramCrudController extends AbstractLockableCrudController
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
        return Logigram::class;
    }

    /**
     * @param Logigram $logigram
     * 
     * @return Logigram
     */
    public function cleanLogigram(Logigram $logigram){
        $steps = $logigram->getLogigramSteps();
        $lastStepOK = [];

        if ($steps !== null){
            $lastStepOK = $steps[0];
        
            foreach($steps as $step){
                if($step->getTitle() !== null && $step->getTitle() !== 'Question'){
                    $lastStepOK = $step;
                    break;
                }
            }

            foreach($steps as $step){

                if($step->getTitle() !== null  && $step->getTitle() !== 'Question') {

                    $lastStepOK = $step;

                }else {

                    if ($step->getChoices() !== null){

                        $tempChoices = [];

                        foreach($step->getChoices() as $choice){
                            $tempChoices[]=$choice;
                            $step->removeChoice($choice);
                        }

                        foreach($tempChoices as $choice){
                            $lastStepOK->addChoice($choice);
                        }

                    }

                    if ($step->getLogigramNextSteps() !== null) {
                        
                        $tempNextStepList = [];

                        foreach($step->getLogigramNextSteps() as $stepNextStep){
                            $tempNextStepList[]= $stepNextStep;
                            $step->removeLogigramNextStep($stepNextStep);
                        }

                        foreach($tempNextStepList as $stepNextStep){
                            $lastStepOK->addLogigramNextStep($stepNextStep);
                        }
                    }

                    if ($step->getTitle() !== 'Question'){
                        $logigram->removeLogigramStep($step);
                    }
                }
            }
        }

        return $logigram;
    }

    /**
     * @return string[]
     */
    public function autocompleteSlugs()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $datasets = $entityManager->getRepository(Dataset::class)->findBy([], ['id' => 'ASC']);
        $pages = $entityManager->getRepository(Page::class)->findBy([], ['id' => 'ASC']);
        $guides = $entityManager->getRepository(Guide::class)->findBy([], ['id' => 'ASC']);
        $actualities = $entityManager->getRepository(Actuality::class)->findBy([], ['id' => 'ASC']);

        $slugs = [];

        foreach ($datasets as $dataset) {
            $slugs[] = $dataset->getSlug();
        }

        foreach ($pages as $page) {
            $slugs[] = $page->getSlug();
        }

        foreach ($guides as $guide) {
            $slugs[] = $guide->getSlug();
        }

        foreach ($actualities as $actuality) {
            $slugs[] = $actuality->getSlug();
        }

        $formatedSlugs = array_combine(array_values($slugs), array_values($slugs));

        return $formatedSlugs;
    }

    /**
     *
     * EasyAdmin bugfix for instance creation with multiple choices and/or nextSteps
     * 
     * @param string                 $entityInstance
     */
    public function createEntity(string $entityFqcn)
    {
        $logigram = new Logigram();
        $logigram = $this->cleanLogigram($logigram);

        return $logigram;
    }

    /**
     *
     * EasyAdmin bugfix for instance creation with multiple choices and/or nextSteps
     * 
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $logigram = $entityManager->getRepository(Logigram::class)->find($entityInstance->getId());
        $logigram = $this->cleanLogigram($logigram);

        $entityManager->flush();
        
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
        $logigram = $this->translator->trans('content.logigram');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.logigram')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $logigram)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $logigram)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['translations.title', 'translations.subTitle', 'updatedBy.username']);
    }

     /**
     * By default, sort ASC categories by name in current; locale.
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

    public function configureActions(Actions $actions): Actions
    {
        $previewAction = Action::new('preview', 'Aperçu', 'fa fa-eye')
            ->linkToCrudAction('preview')
            ->setCssClass('action-preview')
            ->displayIf(fn ($entity) => $entity instanceof Logigram);
        $foView = Action::new('foview', ucfirst($this->translator->trans('bo.foview')))
            ->linkToRoute('front.logigram.show', function (Logigram $logigram): array {
                return [
                    'slug' => ($logigram->getSlug()) ? $logigram->getSlug() : Config::ROUTE_ERR_PARAMS,
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);


        return $actions
            ->add(Crud::PAGE_INDEX, $previewAction)
            ->add(Crud::PAGE_INDEX, $foView);

    }

    /**
     * @Route("/logigram/{id}/preview", name="logigram_preview")
     * 
     * @return Response
     */
    public function preview(AdminContext $context, LogigramService $logigramService): Response
    {  
        $id = $context->getRequest()->query->get('entityId');
        $logigramData[] = $this->getDoctrine()->getRepository(Logigram::class)->find($id);
        $logigram = $logigramService->loadLogigram($logigramData);

        return $this->render('bundles/EasyAdminBundle/logigram/_logigram.html.twig', [
            'logigram' => $logigram,
        ]);
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
            'title'    => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('logigram.prop.title')),
                'help'       => $this->translator->trans('logigram.prop.title.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => LogigramTranslation::LEN_TITLE]),
                'attr'       => ['maxlength' => LogigramTranslation::LEN_TITLE],
            ],
            'subTitle' => [
                'field_type' => TextareaType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('logigram.prop.subTitle')),
                'help'       => $this->translator->trans('logigram.prop.subTitle.help'),
            ],
            'slug'    => [
                'field_type' => SlugType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('prop.slug')),
                'attr'       => ['maxlength' => LogigramTranslation::LEN_SLUG],
                'target'     => 'title',
                'help'       => $this->translator->trans('prop.slug.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => LogigramTranslation::LEN_SLUG]),
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
                )
                ->setHelp($this->translator->trans('logigram.help'));
                ;
                   
            $fields[] = ChoiceField::new('routeType', ucfirst($this->translator->trans('introduction.prop.routetype')))
                ->setChoices(Config::PAGE_ROUTES);
            $slugs = $this->autocompleteSlugs();
            $fields[] = ChoiceField::new('targetSlug', ucfirst($this->translator->trans('logigram.slug')))
            ->setChoices($slugs)
            ->autocomplete();;
            $fields[] = BooleanField::new('isPublished', ucfirst($this->translator->trans('logigram.prop.isPublished')));
            $fields[] = CollectionField::new('logigramSteps', ucfirst($this->translator->trans('logigram.prop.steps')))
                ->renderExpanded(false)
                ->allowAdd()
                ->setEntryIsComplex(true)
                ->setEntryType(LogigramStepFormType::class)
                ->setFormTypeOptions([
                    'by_reference' => false
                ])
            ;
            
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('title', ucfirst($this->translator->trans('logigram.prop.title')));
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')));
            // TO DO : createdBy field
        }

        return $fields;
    }

    /**
     * Add custom assets
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('logigram')
            ->addJsFile('build/logigram.js')
        ;
    }
}
