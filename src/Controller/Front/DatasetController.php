<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Dataset;
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
 * Front Controller that returns all content relative to Dataset entity.
 */
class DatasetController extends AbstractController
{
    public const LIMIT = 5;

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
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param FooterService          $footerService
     * @param HeaderService          $headerService
     * @param AlertService           $alertService
     */
    public function __construct(
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService,
        AlertService $alertService
    ) {
        $this->em = $em;
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Get last published, updated datasets' list in the right locale requested.
     *
     * @Route({
     *     "fr" : "/fr/jeux-de-donnees",
     *     "en" : "/en/datasets"
     * }, name="front.dataset.list", methods="GET")

     *
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param AlertService       $alertService
     *
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $query = $this->em->getRepository(Dataset::class)->getQueryAllByLocaleAndPublishedOrdered($locale);
        $alerts = $alertService->alert($locale);

        $datasets = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::LIMIT,
            ['wrap-queries' => true]
        );
        return $this->render('datasets.html.twig', [
            'datasets' => $datasets,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs('datasets', $locale),
            'introBanner' => $this->headerService->getIntroBanner('', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.dataset.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }

    /**
     * Display a dataset identified by its slug and in the locale requested.
     *
     * @Route({
     *     "en" : "/en/dataset/{slug}",
     *     "fr" : "/fr/jeu-de-donnee/{slug}"
     * }, name="front.dataset.show")
     *
     * @param Request $request
     * @param string  $slug
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function show(Request $request, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $dataset = $this->em->getRepository(Dataset::class)->findBySlugAndLocalePublished($slug, $locale);

        $logigram = $logigramService->logigramBySlug($slug);

        if (!$dataset instanceof Dataset) {
            throw $this->createNotFoundException($this->translator->trans('notfound.dataset'));
        }

        return $this->render('dataset.html.twig', [
            'entity' => $dataset,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'dataset',
                $locale,
                $dataset->getTitle(),
                ['slug' => $slug]
            ),
            'logigram' => $logigram,
            'introBanner' => $this->headerService->getIntroBanner('front.dataset.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.dataset.show', $dataset),
            'metaDescription' => $this->cleanMeta($dataset->getContent()),
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
