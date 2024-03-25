<?php                                      
                                                     
namespace App\Controller\Front\Preview;

use App\Entity\AdditionalHelp;
use App\Entity\AdditionalHelpGuide;
use App\Entity\AdditionalHelpGuideDraft;
use App\Entity\Category;
use App\Entity\CategoryGuide;
use App\Entity\Guide;
use App\Entity\GuideDraft;
use App\Service\FooterService;
use App\Service\HeaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GuideDraftController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * To get links to Page entities and SocialNetwork entities in footer.
     *
     * @var FooterService
     */
    protected $footerService;

    /**
     * To get main menu header side.
     *
     * @var HeaderService
     */
    protected $headerService;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param TranslatorInterface    $translator
     * @param FooterService          $footerService
     * @param HeaderService          $headerService
     * @param UrlGeneratorInterface  $router
     * @param EntityManagerInterface $em
     */
    public function __construct(
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService,
        UrlGeneratorInterface $router,
        EntityManagerInterface $em
    ) {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * Display a GuideDraft preview as a published page.
     *
     * @Route({
     *     "en": "/en/admin/preview/guide/{slug}",
     *     "fr": "/fr/admin/previsualisation/guide/{slug}",
     * }, name="front.preview.guidedraft")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $slug
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, string $slug): Response
    {
        $locale = $request->getLocale();
        $guideDraft = $em->getRepository(GuideDraft::class)->findOneBySlugAndLocale($slug, $locale);

        if (!$guideDraft instanceof GuideDraft) {
            throw $this->createNotFoundException($this->translator->trans('notfound.page'));
        }
        // Associated Additional Helps to this guide.
        $seeMoreRaw = $this->em->getRepository(AdditionalHelpGuideDraft::class)->findByGuideAndLocaleOrdered(
            $guideDraft,
            $locale
        );
        $seeMore = $this->formatSeeMore($seeMoreRaw);
        // Side menu (categories with their guides).
        $guideMenu = $this->getGuideMenu($locale, $slug);

        return $this->render('front/preview/guidedraft.html.twig', [
            'preview' => true,
            'entity' => $guideDraft,
            'guideMenu' => $guideMenu,
            'seeMore' => $seeMore,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'page',
                $locale,
                $guideDraft->getTitle(),
                ['slug' => $slug]
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.guide.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.preview.guidedraft', $guideDraft),
        ]);
    }

    /**
     * @param AdditionalHelpGuideDraft[] $additionalHelpGuidesDraft
     *
     * @return array
     */
    protected function formatSeeMore($additionalHelpGuidesDraft)
    {
        $seeMore = [];

        foreach ($additionalHelpGuidesDraft as $block) {
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
}
