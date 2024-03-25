<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\DatasetReused;
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
 * Front Controller that returns all contents relation to DatasetReused entity.
 */
class DatasetReusedController extends AbstractController
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
     * Get last published datasetReuseds' list in the right locale requested.
     *
     * @Route({
     *     "fr": "/fr/jeux-de-donnees/reutilises",
     *     "en": "/en/dataset/reuseds"
     * }, name="front.datasetReused.list")
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
        $datasetReusedQuery = $em->getRepository(DatasetReused::class)->findLastPublished();
        //$datasetReusedQuery = $em->getRepository(DatasetReused::class)->findAll();
        //dd($datasetReusedQuery);
        $datasetReuseds = $paginator->paginate(
            $datasetReusedQuery,
            $request->query->getInt('page', 1),
            self::LIMIT
        );
        $alerts = $alertService->alert($locale);

        return $this->render('datasetReuseds.html.twig', [
            'status' => self::STATUS['all'],
            'datasetReuseds' => $datasetReuseds,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'datasetReuseds',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.datasetReused.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.datasetReused.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }
    
    /**
     * Display an datasetReused identified by its id and in the locale requested.
     *
     * @Route({
     *     "en": "/en/dataset/reused/{id}",
     *     "fr": "/fr/jeu-de-donnee/reutilise/{id}"
     * }, name="front.datasetReused.show")
     *
     * @param Request   $request
     *
     * @return Response
     */
    public function show(Request $request, DatasetReused $datasetReused): Response
    {
        $locale = $request->getLocale();
        if (!$datasetReused instanceof DatasetReused) {
            throw $this->createNotFoundException($this->translator->trans('notfound.datasetReused'));
        }

        return $this->render('datasetReused.html.twig', [
            'entity' => $datasetReused,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'datasetReused',
                '',
                $datasetReused->getpublicationTitle(),
                []
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.datasetReused.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            //'switcherLng' => $this->headerService->getSwitcherSlug('front.datasetReused.show', $datasetReused),
            'metaDescription' => $this->cleanMeta($datasetReused->getDescription()),
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
