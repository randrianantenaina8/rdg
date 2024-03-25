<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Actuality;
use App\Entity\ActualityDraft;
use App\Entity\AdditionalHelp;
use App\Entity\AlertMsg;
use App\Entity\DatasetDraft;
use App\Entity\DatasetReused;
use App\Entity\DatasetValuation;
use App\Entity\DataWorkshop;
use App\Entity\Discipline;
use App\Entity\FaqHighlighted;
use App\Entity\GuideDraft;
use App\Entity\Heading;
use App\Entity\SupportingInstitution;
use App\Entity\Institution;
use App\Entity\FaqBlock;
use App\Entity\Introduction;
use App\Entity\Keyword;
use App\Entity\MenuBasic;
use App\Entity\MenuMultiple;
use App\Entity\Page;
use App\Entity\PageDraft;
use App\Entity\ProjectTeam;
use App\Entity\ProjectTeamDraft;
use App\Entity\ReuseType;
use App\Entity\Subject;
use App\Entity\Recipient;
use App\Entity\S3File;
use App\Entity\S3FileCategory;
use App\Entity\Dataset;
use App\Entity\Event;
use App\Entity\Glossary;
use App\Entity\Guide;
use App\Entity\Category;
use App\Entity\SocialNetwork;
use App\Entity\SubjectRecipient;
use App\Entity\Taxonomy;
use App\Entity\User;
use App\Entity\Logigram;
use App\Entity\DataRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", requirements={"_locale" : "%app_locales%"})
 *
 * @IsGranted("ROLE_CONTRIB")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var AdminUrlGenerator
     */
    protected $router;

    /**
     * @param TranslatorInterface $translator
     * @param AdminUrlGenerator   $router
     */
    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Switch language in back-office.
     *
     * @Route("/admin/switch_ln/{lang}", name="switch_ln", requirements={"lang" : "%app_locales%"})
     *
     * @param string  $lang
     * @param Request $request
     *
     * @return Response
     */
    public function changeLocale($lang, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $refererParams = parse_url($request->headers->get('referer'));
        if (false === $this->checkReferer($refererParams)) {
            return $this->redirect($referer);
        }
        $paramLocale = '/' . $lang;
        $refererParams['path'] = substr_replace($refererParams['path'], $paramLocale, 0, strlen($paramLocale));
        $url = $refererParams['scheme'] . '://' . $refererParams['host'] .
            $refererParams['path'] . '?' . $refererParams['query'];
        $request->getSession()->set('_locale', $lang);

        return $this->redirect($url);
    }

    /**
     * Check every params after url parsing to be sure we can rebuild url to redirect.
     *
     * @param array $params
     *
     * @return bool
     */
    protected function checkReferer($params)
    {
        if (
            !isset($params['scheme'])
            || !isset($params['host'])
            || !isset($params['path'])
            || !isset($params['query'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @Route("/admin", name="admin")
     *
     * @return Response
     */
    public function index(): Response
    {
        //return parent::index(); // Uncomment to see examples.
        return $this->redirect($this->router->setController(DatasetCrudController::class)->generateUrl());
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->translator->trans('bo.title'))
            // you can include HTML contents too (e.g. to link to an image)
            //->setTitle('<img src="..."> ACME <span class="text-small">Corp.</span>')
            // the path defined in this method is passed to the Twig asset() function
            //->setFaviconPath('favicon.svg')
            ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addCssFile('build/admin.css')
            ;
    }

    /**
     * Use this method to personnalize options for user menu part.
     *
     * @param UserInterface $user
     *
     * @return UserMenu
     *
     * @see https://symfony.com/bundles/EasyAdminBundle/current/dashboards.html#user-menu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);

        $userMenu
            ->setGravatarEmail($user->getEmail())
            ->addMenuItems([
                MenuItem::linkToRoute(
                    ucfirst($this->translator->trans('menu.profile')),
                    'fa fa-id-card',
                    'admin.profile'
                ),
            ]);
        return $userMenu;
    }

    /**
     * Set custom template to overide easyAdmin layout template.
     *
     * @return Crud
     */
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('layout', 'bundles/EasyAdminBundle/layout.html.twig');
    }

    /**
     * @return Actions
     */
    public function configureActions(): Actions
    {
        $actions = parent::configureActions();

        $actions
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
        ;
        return $actions;
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        // SECTION CONTENTS
        yield MenuItem::section($this->translator->trans('menu.entities'), 'fa fa-file-text')
            // Set lowest Permission available for this section menu.
            ->setPermission('ROLE_CONTRIB');
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.datasets')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.published.list')),
                    'fa fa-th-list',
                    Dataset::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.draft.list')),
                    'fa fa-th-list',
                    DatasetDraft::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.create.draft')),
                    'fa fa-plus',
                    DatasetDraft::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.actualities')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.published.list')),
                    'fa fa-th-list',
                    Actuality::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.draft.list')),
                    'fa fa-th-list',
                    ActualityDraft::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.create.draft')),
                    'fa fa-plus',
                    ActualityDraft::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.events')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    Event::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    Event::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.pages')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.published.list')),
                    'fa fa-th-list',
                    Page::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.draft.list')),
                    'fa fa-th-list',
                    PageDraft::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.create.draft')),
                    'fa fa-plus',
                    PageDraft::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);

        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.dataRepository')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.dataRepository.dataRepositories')),
                    'fa fa-th-list',
                    DataRepository::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.dataRepository.supportingInstitution')),
                    'fa fa-university',
                    SupportingInstitution::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.dataRepository.disciplines')),
                    'fa fa-plus',
                    Discipline::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.dataRepository.keywords')),
                    'fa fa-plus',
                    Keyword::class
                )->setPermission('ROLE_CONTRIB'),
            ]);

        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.datasetReused')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.datasetReused.datasetReuseds')),
                    'fa fa-th-list',
                    DatasetReused::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.datasetReused.reuseTypes')),
                    'fa fa-plus',
                    ReuseType::class
                )->setPermission('ROLE_CONTRIB'),
            ]);

        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.datasetValuation')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.datasetValuation.datasetValuations')),
                    'fa fa-th-list',
                    DatasetValuation::class
                )->setPermission('ROLE_CONTRIB'),
            ]);
        
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('bo.media.library')),
            'fas fa-photo-video'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.media.library.list')),
                    'fa fa-th-list',
                    S3File::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.media.library.add')),
                    'fa fa-plus',
                    S3File::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.media.category.add')),
                    'fa fa-plus',
                    S3FileCategory::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);

        // SECTION SUPPORT
        yield MenuItem::section($this->translator->trans('menu.support'), 'fa fa-info-circle')
            // Set lowest Permission available for this section menu.
            ->setPermission('ROLE_CONTRIB');
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.guides')),
            'fa fa-folder-open'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.published.list')),
                    'fa fa-th-list',
                    Guide::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.draft.list')),
                    'fa fa-th-list',
                    GuideDraft::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.create.draft')),
                    'fa fa-plus',
                    GuideDraft::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('menu.label.categories')),
            'fa fa-sitemap'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    Category::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    Category::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.additionalHelps')),
            'fa fa-question-circle'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    AdditionalHelp::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    AdditionalHelp::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.faqblocks')),
            'fa fa-question-circle'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    FaqBlock::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    FaqBlock::class
                )->setAction(Action::NEW)->setPermission('ROLE_COORD'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('menu.label.headings')),
            'fa fa-sitemap'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    Heading::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    Heading::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('menu.label.faqhighlighted')),
            'fa fa-question-circle'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToRoute(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    'admin.faq-hightlighted.auto'
                    //FaqHighlighted::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    FaqHighlighted::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.contact.page')),
            'fa fa-envelope'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.contact.subjects')),
                    'fa fa-envelope',
                    Subject::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('content.contact.recipients')),
                    'fa fa-user-o',
                    Recipient::class
                )->setPermission('ROLE_CONTRIB'),
            ]);
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.logigram')),
            'fa fa-clipboard-list',
            Logigram::class
        )
            ->setPermission("ROLE_ADMIN");

        // SECTION CENTER
        yield MenuItem::section(ucfirst($this->translator->trans('menu.centers')), 'fa fa-globe')
            // Set lowest Permission available for this section menu.
            ->setPermission('ROLE_COORD');
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.institution')),
            'fa fa-university'
        )
            ->setPermission('ROLE_COORD')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    Institution::class
                )->setPermission('ROLE_COORD'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    Institution::class
                )->setAction(Action::NEW)->setPermission('ROLE_COORD'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.dataworkshop')),
            'fa fa-database'
        )
            ->setPermission('ROLE_COORD')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    DataWorkshop::class
                )->setPermission('ROLE_COORD'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    DataWorkshop::class
                )->setAction(Action::NEW)->setPermission('ROLE_COORD'),
            ]);

        // SECTION PROJECT TEAM
        yield MenuItem::section(ucfirst($this->translator->trans('menu.project.team')), 'fa fa-users')
            // Set lowest Permission available for this section menu.
            ->setPermission('ROLE_CONTRIB');
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.members')),
            'fa fa-user-circle-o'
        )
            ->setPermission('ROLE_CONTRIB')
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    ProjectTeam::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('bo.draft.list')),
                    'fa fa-th-list',
                    ProjectTeamDraft::class
                )->setPermission('ROLE_CONTRIB'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-user-plus',
                    ProjectTeamDraft::class
                )->setAction(Action::NEW)->setPermission('ROLE_CONTRIB'),
            ]);

        // SECTION ANNEXE
        yield MenuItem::section(ucfirst($this->translator->trans('menu.annex')), 'fa fa-sticky-note')
            // Set lowest Permission available for this section menu.
            ->setPermission('ROLE_CONTRIB');
        yield MenuItem::linkToRoute(
            ucfirst($this->translator->trans('content.lames')),
            'fa fa-layer-group',
            'admin.lame.list'
        )
            ->setPermission('ROLE_COORD');
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.glossary')),
            'fa fa-book',
            Glossary::class
        )
            ->setPermission('ROLE_CONTRIB');
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.introduction')),
            'fa fa-header',
            Introduction::class
        )
            ->setPermission('ROLE_COORD');
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.taxonomy')),
            'fa fa-filter',
            Taxonomy::class
        )
            ->setPermission('ROLE_CONTRIB');

        // SECTION SETTINGS
        yield MenuItem::section($this->translator->trans('menu.settings'), 'fa fa-cogs')
            ->setPermission('ROLE_COORD');
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.label.menuMultiple')),
            'fa fa-bars'
        )
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    MenuMultiple::class
                )->setPermission('ROLE_COORD'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    MenuMultiple::class
                )->setAction(Action::NEW)->setPermission('ROLE_COORD'),
            ]);
        yield MenuItem::subMenu(
            ucfirst($this->translator->trans('content.label.menuBasic')),
            'fa fa-bars'
        )
            ->setSubItems([
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.list')),
                    'fa fa-th-list',
                    MenuBasic::class
                )->setPermission('ROLE_COORD'),
                MenuItem::linkToCrud(
                    ucfirst($this->translator->trans('menu.label.add')),
                    'fa fa-plus',
                    MenuBasic::class
                )->setAction(Action::NEW)->setPermission('ROLE_COORD'),
            ]);
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('footer.networks')),
            'fa fa-share-alt',
            SocialNetwork::class
        )
            ->setPermission('ROLE_COORD');
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.users')),
            'fa fa-users',
            User::class
        )->setPermission('ROLE_COORD');
        yield MenuItem::linkToCrud(
            ucfirst($this->translator->trans('content.alert')),
            'fa fa-exclamation-triangle',
            AlertMsg::class
        )
            ->setPermission('ROLE_COORD');
        yield MenuItem::linkToRoute(
            ucfirst($this->translator->trans('content.config')),
            'fa fa-clipboard-list',
            'admin.config.board'
        )
            ->setPermission("ROLE_ADMIN");
    }
}
