<?php

namespace App\Controller\Front;

use App\Entity\ProjectTeam;
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
 * Front controller that returns ProjectTeam.
 */
class ProjectTeamController extends AbstractController
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
     * @var LogigramService
     */
    private $logigramService;

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
    )
    {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Get all ProjectTeams in the right locale requested.
     *
     * @Route({
     *     "fr" : "/fr/entrepot-pluridisciplinaire",
     *     "en" : "/en/a-multidisciplinary-repository"
     * }, name="front.repository.team.list")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface     $paginator
     * @param LogigramService       $logigramService
     *
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, AlertService $alertService, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $members = $em->getRepository(ProjectTeam::class)->findAllByLocaleAndOrderedByWeight($locale, 'ASC');
        $alerts = $alertService->alert($locale);

        $routeName = $request->get('_route');
        $logigram = $logigramService->logigramByRoute($routeName);

        return $this->render('repositoryteam.html.twig', [
            'members' => $members,
            'alerts'  => $alerts,
            'logigram' => $logigram,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'team',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.repository.team.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.repository.team.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_MEDIUM);
    }
}
