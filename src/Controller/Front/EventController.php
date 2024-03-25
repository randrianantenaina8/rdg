<?php                                      
                                                     
namespace App\Controller\Front;

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


/**
 * Front Controller that returns all contents relation to Event entity.
 */
class EventController extends AbstractController
{
    public const LIMIT = 10;

    public const STATUS = [
        'all'  => 1,
        'past' => 2,
        'next' => 3,
    ];

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
     * @var AlertService
     */
    private $alertService;

    /**
     * @param TranslatorInterface $translator
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     * @param AlertService      $alertService
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
     * Get last published events' list in the right locale requested.
     *
     * @Route({
     *     "fr": "/fr/evenements",
     *     "en": "/en/events"
     * }, name="front.event.list")
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
        $eventQuery = $em->getRepository(Event::class)->getQueryLastPublishedByLocaleOrderedByBegin($locale);
        $events = $paginator->paginate(
            $eventQuery,
            $request->query->getInt('page', 1),
            self::LIMIT
        );
        $alerts = $alertService->alert($locale);

        return $this->render('events.html.twig', [
            'status' => self::STATUS['all'],
            'events' => $events,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'events',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.event.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.event.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }

    /**
     * Get last published past events' list in the right locale requested.
     *
     * @Route({
     *     "fr": "/fr/evenements/passe",
     *     "en": "/en/events/past"
     * }, name="front.event.list.past")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface     $paginator
     *
     * @return Response
     */
    public function listPast(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $locale = $request->getLocale();
        $eventQuery = $em->getRepository(Event::class)->getQueryPastLastPublishedByLocale($locale);
        $events = $paginator->paginate(
            $eventQuery,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return $this->render('events.html.twig', [
            'status' => self::STATUS['past'],
            'events' => $events,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'pastevents',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.event.list.past', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.event.list.past'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }

    /**
     * Get last published next events' list in the right locale requested.
     *
     * @Route({
     *     "fr": "/fr/evenements/futur",
     *     "en": "/en/events/next"
     * }, name="front.event.list.next")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface     $paginator
     * @param AlertService           $alertService
     *
     * @return Response
     */
    public function listNext(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $eventQuery = $em->getRepository(Event::class)->getQueryNextLastPublishedByLocale($locale);
        $events = $paginator->paginate(
            $eventQuery,
            $request->query->getInt('page', 1),
            self::LIMIT
        );
        $alerts = $alertService->alert($locale);

        return $this->render('events.html.twig', [
            'status' => self::STATUS['next'],
            'events' => $events,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'nextevents',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.event.list.next', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.event.list.next'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }

    /**
     * Display an event identified by its id and in the locale requested.
     *
     * @Route({
     *     "en": "/en/event/{slug}",
     *     "fr": "/fr/evenement/{slug}"
     * }, name="front.event.show")
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
        $event = $em->getRepository(Event::class)->findOnePublishedBySlugAndLocale($slug, $locale);

        if (!$event instanceof Event) {
            throw $this->createNotFoundException($this->translator->trans('notfound.event'));
        }

        return $this->render('event.html.twig', [
            'entity' => $event,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'event',
                $locale,
                $event->getTitle(),
                ['slug' => $slug]
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.event.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.event.show', $event),
            'metaDescription' => $this->cleanMeta($event->getContent()),
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
