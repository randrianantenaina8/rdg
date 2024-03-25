<?php

namespace App\Controller\Front;

use App\Entity\Discipline;
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
 * Front controller that returns Discipline.
 */
class DisciplineController extends AbstractController
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param FooterService          $footerService
     * @param HeaderService          $headerService
     * @param AlertService           $alertService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        FooterService          $footerService,
        HeaderService          $headerService,
        AlertService           $alertService,
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
     *     "fr" : "/fr/disciplines",
     *     "en" : "/en/disciplines"
     * }, name="front.discipline.list")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $data = $this->em->getRepository(Discipline::class)->findAll();
        
        $disciplines = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );

        $alerts = $alertService->alert($locale);

        return $this->render('discipline.html.twig', [
            'disciplines' => $disciplines,
            'alerts'      => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs('disciplines', $locale),
            'introBanner' => $this->headerService->getIntroBanner('front.discipline.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.discipline.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_MEDIUM);
    }
}
