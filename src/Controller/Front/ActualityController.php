<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Actuality;
use App\Entity\Event;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\LogigramService;

/**
 * Front Controller that returns all contents related to Actuality entity.
 */
class ActualityController extends AbstractController
{
    public const LIMIT = 5;

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
     * To get alerts.
     *
     * @var AlertService
     */
    protected $alertService;

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
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Get last published actualities' list in the right locale requested.
     *
     * @Route({
     *     "fr" : "/fr/actualites",
     *     "en" : "/en/actualities"
     * }, name="front.actuality.list")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface     $paginator
     * @param AlertService           $alertService
     * 
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $eventRepo = $em->getRepository(Event::class);
        $data = $em->getRepository(Actuality::class)->findLastPublishedByLocale($locale);
        $alerts = $alertService->alert($locale);

        $events = $eventRepo->findNextPublishedLimited($locale, 5);
        // Add taxonomies to events.
        $eventIds = [];
        if (is_iterable($events) && $events > 0) {
            foreach ($events as $event) {
                $eventIds[] = $event->getId();
            }
            $events = $eventRepo->findAllByLocaleAndIdsOrdered($locale, $eventIds);
        }

        $actualities = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            self::LIMIT
        );
        return $this->render('actualities.html.twig', [
            'alerts'  => $alerts,
            'actualites' => $actualities,
            'events' => $events,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'actualities',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.actuality.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.actuality.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }

    /**
     * Display an actuality identified by its slug and in the locale requested.
     *
     * @Route({
     *     "en": "/en/actuality/{slug}",
     *     "fr": "/fr/actualite/{slug}"
     * }, name="front.actuality.show")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $slug
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $actuality = $em->getRepository(Actuality::class)->findBySlugAndPublishedAndLocale($slug, $locale);

        $logigram = $logigramService->logigramBySlug($slug);

        if (!$actuality instanceof Actuality) {
            throw $this->createNotFoundException($this->translator->trans('notfound.actuality'));
        }

        return $this->render('page.html.twig', [
            'entity' => $actuality,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'actuality',
                $locale,
                $actuality->getTitle(),
                ['slug' => $slug]
            ),
            'logigram' => $logigram,
            'introBanner' => $this->headerService->getIntroBanner('front.actuality.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.actuality.show', $actuality),
            'metaDescription' => $this->cleanMeta($actuality->getContent()),
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
