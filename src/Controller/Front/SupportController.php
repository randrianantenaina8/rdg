<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\AdditionalHelp;
use App\Entity\AdditionalHelpGuide;
use App\Entity\CategoryGuide;
use App\Entity\Config;
use App\Entity\FaqHighlighted;
use App\Entity\Guide;
use App\Entity\Category;
use App\Service\FaqService;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\LogigramService;


/**
 * Front Controller that returns all contents related to Guide entity.
 */
class SupportController extends AbstractController
{
    /**
     * To get links to Page entities and SocialNetwork entities in footer.
     *
     * @var FooterService
     */
    protected $footerService;

    /**
     * @var HeaderService
     */
    protected $headerService;

    /**
     * @var AlertService
     */
    private $alertService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface  $router
     * @param TranslatorInterface    $translator
     * @param FooterService          $footerService
     * @param HeaderService          $headerService
     * @param AlertService           $alertService
     */
    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService,
        AlertService $alertService
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * @Route({
     *     "fr" : "/fr/aide-en-ligne",
     *     "en" : "/en/online-help"
     * }, name="front.guide.homepage"
     *  , methods="GET")
     *
     * @param Request    $request
     * @param FaqService $faqService
     * @param AlertService  $alertService
     * @param string     $slug
     *
     * @return Response
     */
    public function index(Request $request, FaqService $faqService, AlertService $alertService, string $slug = ''): Response
    {
        $locale = $request->getLocale();
        $faqService->generate($locale);
        $guides = $this->em->getRepository(Guide::class)->findAllByLocaleAndOrderedByWeight($locale, 1, $order = 'ASC');
        $mainCategory = $this->em->getRepository(Category::class)->findAllByLocaleAndWeight($locale);
        $guideMenu = $this->getGuideMenu($locale, $slug);
        $alerts = $alertService->alert($locale);

        return $this->render('partials/guides/_guides.html.twig', [
            'guides'      => $guides,
            'mainCategory'=> $mainCategory,
            'alerts'      => $alerts,
            'guideMenu'   => $guideMenu,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs('be accompanied', $locale),
            'introBanner' => $this->headerService->getIntroBanner('front.guide.homepage', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.guide.homepage')
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }

    private function isFaqAuto()
    {
        $configFaqHighligted = $this->em
            ->getRepository(Config::class)
            ->findOneBy(['name' => FaqHighlighted::NAME_AUTO]);

        if (!$configFaqHighligted instanceof Config) {
            return false;
        }
        if (isset($configFaqHighligted->getData()['auto'])) {
            return (bool)$configFaqHighligted->getData()['auto'];
        }

        return false;
    }

    /**
     * @Route({
     *     "fr" : "/fr/guide/{slug}",
     *     "en" : "/en/guide/{slug}"
     * }, name="front.guide.show", methods="GET")
     *
     * @param Request                $request
     * @param string                 $slug
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function show(Request $request, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $guide = $this->em->getRepository(Guide::class)->findBySlugAndPublishedAndLocale($slug, $locale);

        $logigram = $logigramService->logigramBySlug($slug);

        if (!$guide instanceof Guide) {
            throw $this->createNotFoundException($this->translator->trans('notfound.guide'));
        }
        // Associated Additional Helps to this guide.
        $seeMoreRaw = $this->em->getRepository(AdditionalHelpGuide::class)->findByGuideAndLocaleOrdered(
            $guide,
            $locale
        );
        $seeMore = $this->formatSeeMore($seeMoreRaw);
        // Side menu (categories with their guides).
        $guideMenu = $this->getGuideMenu($locale, $slug);
        return $this->render('oneguide.html.twig', [
            'seeMore' => $seeMore,
            'entity' => $guide,
            'guideMenu' => $guideMenu,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'guide',
                $locale,
                $guide->getTitle(),
                ['slug' => $slug]
            ),
            'logigram' => $logigram,
            'introBanner' => $this->headerService->getIntroBanner('front.guide.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.guide.show', $guide),
            'metaDescription' => $this->cleanMeta($guide->getContent()),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }

    /**
     * @Route({
     *     "fr" : "/fr/categorie/{id}/guide/{slug}",
     *     "en" : "/en/category/{id}/guide/{slug}"
     * }, name="front.category.guide.show", methods="GET")
     *
     * @param Request  $request
     * @param Category $category
     * @param string   $slug
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function showWithCategory(Request $request, Category $category, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $guide = $this->em->getRepository(Guide::class)->findBySlugAndPublishedAndLocale($slug, $locale);

        $logigram = $logigramService->logigramBySlug($slug);

        if (!$guide instanceof Guide) {
            throw $this->createNotFoundException($this->translator->trans('notfound.guide'));
        }
        // Associated Additional Helps to this guide.
        $seeMoreRaw = $this->em->getRepository(AdditionalHelpGuide::class)->findByGuideAndLocaleOrdered(
            $guide,
            $locale
        );
        $seeMore = $this->formatSeeMore($seeMoreRaw);
        // Side menu (categories with their guides).
        $guideMenu = $this->getGuideMenu($locale, $slug, $category->getId());

        return $this->render('oneguide.html.twig', [
            'metaDescription' => $this->cleanMeta($guide->getContent()),
            'seeMore' => $seeMore,
            'entity' => $guide,
            'guideMenu' => $guideMenu,
            'currentCategoryId' => $category->getId(),
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'guide',
                $locale,
                $guide->getTitle(),
                ['slug' => $slug]
            ),
            'logigram' => $logigram,
            'introBanner' => $this->headerService->getIntroBanner('front.guide.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug(
                'front.category.guide.show',
                $guide,
                ['id' => $category->getId()]
            ),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }

    /**
     * @param AdditionalHelp[] $additionalHelps
     *
     * @return array
     */
    protected function formatAdditionalHelps($additionalHelps)
    {
        $helpBlocks = [];

        foreach ($additionalHelps as $additionalHelp) {
            $helpBlocks[] = $this->getAidBlock($additionalHelp);
        }

        return $helpBlocks;
    }

    /**
     * @param AdditionalHelpGuide[] $additionalHelpGuides
     *
     * @return array
     */
    protected function formatSeeMore($additionalHelpGuides)
    {
        $seeMore = [];

        foreach ($additionalHelpGuides as $block) {
            $seeMore[] = $this->getAidBlock($block->getAdditionalHelp());
        }
        return $seeMore;
    }

    /**
     * @param AdditionalHelp $additionalHelp
     *
     * @return array
     */
    protected function getAidBlock($additionalHelp)
    {
        $block = [];
        $link = $additionalHelp->getLink();

        if (!$link && $additionalHelp->getGuide() instanceof Guide && $additionalHelp->getGuide()->getSlug()) {
            $link = $this->generateUrl(
                'front.guide.show',
                ['slug' => $additionalHelp->getGuide()->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        if ($link) {
            $linkBase = parse_url($link, PHP_URL_SCHEME) . "://" . parse_url($link, PHP_URL_HOST);

            $block = [
                'name' => $additionalHelp->getName(),
                'description' => $additionalHelp->getDescription(),
                'link' => $link,
                'baseLink' => $linkBase,
            ];
        }

        return $block;
    }

    /**
     * Format guides property in categories element, and identified the selected guide
     * to let FO finding categories associated to that guide.
     *
     * @param string   $locale
     * @param string   $currentSlug
     * @param int|null $currentCategId
     * @param string   $order
     *
     * @return array
     */
    protected function getGuideMenu(string $locale, string $currentSlug, ?int $currentCategId = null, $order = 'ASC')
    {
        $categories = $this->em->getRepository(Category::class)->findAllByPublishedGuidesRestrictedByLocale(
            $locale,
            $order
        );
        $menu = [];

        foreach ($categories as $category) {
            $categSelected = ($category->getId() == $currentCategId);
            $guideList = [];
            $guidesInCateg = $category->getGuides();

            foreach ($guidesInCateg as $guideCateg) {
                $guideSelected = false;
                $guide = $guideCateg->getGuide();
                $slug = $guide->getSlug();
                if ($currentSlug === $slug && !$guideSelected) {
                    if (!$categSelected && !$currentCategId) {
                        $categSelected = true;
                    }
                    $guideSelected = true;
                }
                $guideList[] = [
                    'title' => $guide->getTitle(),
                    'url'   => $this->router->generate(
                        'front.category.guide.show',
                        [
                            '_locale' => $locale,
                            'id' => $category->getId(),
                            'slug' => $guide->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'selected' => $guideSelected,
                    'weight' => $this->getGuideWeightInCategory($guide, $category),
                ];
            }
            $menu[] = [
                'selected' => $categSelected,
                'category' => $category->getName(),
                'categoryId' => $category->getId(),
                'guides' => $guideList,
            ];
        }
        return $menu;
    }

    /**
     * Send weight (CategoryGuide's property) to let FE ordering guides into a category.
     *
     * @param Guide    $guide
     * @param Category $category
     */
    protected function getGuideWeightInCategory($guide, $category)
    {
        /** @var CategoryGuide */
        foreach ($guide->getCategories() as $guideCategory) {
            if ($category === $guideCategory->getCategory()) {
                return $guideCategory->getWeight();
            }
        }
        return 10;
    }

    /**
     * @param $meta
     *
     * @return array|string|string[]
     */
    private function cleanMeta($meta)
    {
        $cleanMeta = strip_tags($meta);
        $cleanMeta = str_replace(array("\r", "\n"), '', $cleanMeta);
        $cleanMeta = str_replace('&amp;', '', $cleanMeta);
        $cleanMeta = str_replace('&nbsp;', ' ', $cleanMeta);

        return $cleanMeta;
    }
}
