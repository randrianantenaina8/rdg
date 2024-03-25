<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\DataWorkshop;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Front controller that returns DataWorkshop.
 */
class DataWorkshopController extends AbstractController
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
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     * @param AlertService        $alertService
     */
    public function __construct(
        FooterService $footerService,
        HeaderService $headerService,
        AlertService $alertService,
        EntityManagerInterface $em
    )
    {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
        $this->em = $em;
    }

    /**
     * Get all data workshops in the right locale requested.
     *
     * @Route({
     *     "fr" : "/fr/ateliers-de-la-donnee",
     *     "en" : "/en/data-workshops"
     * }, name="front.dataworkshop.list")
     *
     * @param Request                $request
     * @param PaginatorInterface     $paginator
     *
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $data = $this->em->getRepository(DataWorkshop::class)->getQueryOrderedWithInstitutions($locale);
        $institutions = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );
        $alerts = $alertService->alert($locale);

        return $this->render('dataworkshop.html.twig', [
            'entity' => $institutions,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'dataworkshops',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.dataworkshop.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.dataworkshop.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_MEDIUM);
    }

    /**
     * Return Dataworkshop by its type
     * 
     * @Route("/dataworkshop", name="dataworkshop_id", methods="GET")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function show(Request $request): Response
    {
        $data = $this->em->getRepository(DataWorkshop::class)->find($request->query->get('id'));
        
        $type = [
            'type' => $data->getWorkshopType(),
            'url' => $data->getUrlDataWorkshop()
        ];
        $response = new Response();
       
        $response->setContent(json_encode($type));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
