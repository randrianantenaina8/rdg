<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Institution;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Front controller that returns Institution.
 */
class InstitutionController extends AbstractController
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
        AlertService $alertService
    )
    {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Get all institutions in the right locale requested.
     *
     * @Route({
     *     "fr" : "/fr/etablissements",
     *     "en" : "/en/institutions"
     * }, name="front.institutions.list")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface     $paginator
     *
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $data = $em->getRepository(Institution::class)->getQueryAllOrderedWithDataWorkshops($locale);

        $institutions = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );
        $alerts = $alertService->alert($locale);


        return $this->render('institution.html.twig', [
            'entity' => $institutions,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'institutions',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.institutions.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.institutions.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_MEDIUM);
    }
}
