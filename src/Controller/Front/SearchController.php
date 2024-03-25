<?php

namespace App\Controller\Front;

use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SolrSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @codeCoverageIgnore
 * SearchController returns all search results.
 */
class SearchController extends AbstractController
{
    // Number of results per page
    public const LIMIT = 8;

    /**
     * To get links to Page entities and SocialNetwork entities in footer.
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
    protected $alertService;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SolrSearchService
     */
    protected $searchService;

    /**
     * @param TranslatorInterface   $translator
     * @param FooterService         $footerService
     * @param HeaderService         $headerService
     * @param AlertService          $alertService
     * @param SolrSearchService     $searchService
     */
    public function __construct(
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService,
        AlertService $alertService,
        SolrSearchService $searchService
    )
    {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
        $this->searchService = $searchService;
    }

    /**
     * Search resources based on given search string.
     *
     * @Route({
     *     "en": "/en/search",
     *     "fr": "/fr/recherche"
     * }, name="front.search.index")
     *
     * @param AlertService       $alertService 
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @param string             $searchString
     *
     * @return Response
     */
    public function search(
        Request $request,
        string $searchString = null,
        AlertService $alertService,
        PaginatorInterface $paginator
    ): Response
    {
        $locale = $request->getLocale();
        $alerts = $alertService->alert($locale);

        $searchString = $request->query->get('q');

        $solrResponse = $this->searchService->getResults(
            $searchString,
            $request->query->get('page'),
            $request->query->get('results_per_page'),
            $request->getLocale()
        );

        $results = $paginator->paginate(
            $solrResponse,
            $request->query->getInt('page', 1),
            self::LIMIT,
            ['wrap-queries' => true]
        );

        return $this->render('search.html.twig', [
            'results'     => $results,
            'nbResults'   => count($solrResponse),
            'alerts'      => $alerts,
            'introBanner' => $this->headerService->getIntroBanner('front.search.index', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.search.index')
        ]);
    }
}
