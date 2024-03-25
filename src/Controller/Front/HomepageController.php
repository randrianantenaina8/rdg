<?php                                      
                                                     
namespace App\Controller\Front;

use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\LaminaService;
use App\Service\AlertService;
use Dipso\LockeditBundle\Lockedit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Front controller to access homepage.
 */
class HomepageController extends AbstractController
{
    /**
     * @var FooterService
     */
    private $footerService;

    /**
     * @var HeaderService
     */
    private $headerService;

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
        AlertService $alertService
    )
    {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * @Route("/", name="front.index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        if (!$locale) {
            $locale = $this->getParameter('locale');
        }
        return $this->redirectToRoute("front.homepage", ['_locale' => $locale]);
    }

    /**
     * @Route({
     *     "fr" : "/fr",
     *     "en" : "/en"
     * }, name="front.homepage")
     *
     * @param Request       $request
     * @param LaminaService $laminaService
     * @param AlertService  $alertService
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function homepage(Request $request, LaminaService $laminaService, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $eventsLimit = $this->getParameter('app.lame.event.number');
        $laminas = $laminaService->getAllLaminasOrdered($request->getLocale(), $eventsLimit);
        $alerts = $alertService->alert($locale);

        return $this->render('base.html.twig', [
            'laminas' => $laminas,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'root',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.homepage', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.homepage'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::NO_CACHE);
    }
}
