<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Page;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\LogigramService;


/**
 * Front Controller that returns all contents related to Page entity.
 */
class PageController extends AbstractController
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
     * @param TranslatorInterface $translator
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     * @param AlertService        $alertService
     */
    public function __construct(
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService,
        AlertService $alertService
    ) {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->translator = $translator;
        $this->alertService = $alertService;
    }

    /**
     * Display a page identified by its slug and in the locale requested.
     *
     * @Route({
     *     "en": "/en/page/{slug}",
     *     "fr": "/fr/page/{slug}"
     * }, name="front.page.show")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param AlertService           $alertService
     * @param string                 $slug
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, AlertService $alertService, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $page = $em->getRepository(Page::class)->findBySlugAndPublishedAndLocale($slug, $locale);
        $alerts = $alertService->alert($locale);

        $logigram = $logigramService->logigramBySlug($slug);


        if (!$page instanceof Page) {
            throw $this->createNotFoundException($this->translator->trans('notfound.page'));
        }

        return $this->render('pageWithoutImage.html.twig', [
            'entity' => $page,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'page',
                $locale,
                $page->getTitle(),
                ['slug' => $slug]
            ),
            'logigram' => $logigram,
            'introBanner' => $this->headerService->getIntroBanner('front.page.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.page.show', $page),
            'metaDescription' => $this->cleanMeta($page->getContent()),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }

    private function cleanMeta($meta) {
        $cleanMeta = strip_tags($meta);
        $cleanMeta = str_replace(array("\r", "\n"), '', $cleanMeta);
        $cleanMeta = str_replace('&amp;', '', $cleanMeta);
        $cleanMeta = str_replace('&nbsp;', ' ', $cleanMeta);
        return $cleanMeta;
    }
}
