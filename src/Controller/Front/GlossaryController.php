<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Glossary;
use App\Entity\GlossaryTranslation;
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
 * Front controller to display glossary pages.
 */
class GlossaryController extends AbstractController
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
     * @param FooterService $footerService
     * @param HeaderService $headerService
     * @param AlertService  $alertService
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
     * @Route({
     *     "fr" : "/fr/glossaire",
     *     "en" : "/en/glossary"
     * }, name="front.glossary", methods="GET", priority=2)
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
        $initial = $request->query->get('initial') ?? '';
        $data = $em->getRepository(Glossary::class)->getQueryByLocaleAndOrderByTerm($locale, $initial);
        $allTerm = $em->getRepository(GlossaryTranslation::class)->findAllTermByLocale($locale);
        $alphabets = range('A', 'Z');
        $lexicals = [];
        foreach ($allTerm as $term) {
            $char = substr(ucfirst($term->getTerm()), 0, 1);
            if (!in_array($char, $lexicals)) {
                $lexicals[] = $char;
            }
        }
        $initials = array_merge($lexicals, $alphabets);
        $initials = array_unique($initials);
        asort($initials);
        $anchor = [];
        
        foreach ($initials as $initial) {
            if (in_array($initial, $lexicals)) {
                $anchor[$initial] = 1;
            } else {
                $anchor[$initial] = 0;
            }
        }
        $glossaries = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );
        $alerts = $alertService->alert($locale);

        return $this->render('glossary.html.twig', [
            'glossaries' => $glossaries,
            'alerts'  => $alerts,
            'lexicals' => $lexicals,
            'anchor' => $anchor,
            'initials' => $initials,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'glossary',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.glossary', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.glossary'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_MEDIUM);
    }

    /**
     * Return term by definition
     * 
     * @Route({
     *     "fr" : "/fr/glossaire/definition",
     *     "en" : "/en/glossary/definition"
     * }, name="get_term_definition", methods="GET|POST")
     * 
     */
    public function getTermDefinition(Request $request, EntityManagerInterface $em): Response
    {
        $locale = $request->getLocale();
        $term = $request->request->get('term');
        $repositoryGlossary = $em->getRepository(GlossaryTranslation::class);
        $definition = $repositoryGlossary->getDefinitionByTerm($term, $locale);
        $definition = $definition[0]->getDefinition();
        $definition = $this->cleanTag($definition);
        
        if (!$definition) { 
            return new NotFoundHttpException(); 
        }
        return $this->json([
            'success' => true,
            'data' => $definition
        ]);
    }

    /**
     * Return terms' list
     * 
     * @Route({
     *     "fr" : "/fr/glossaire/liste",
     *     "en" : "/en/glossary/list"
     * }, name="get_term_list", methods="GET")
     * 
     */
    public function getTermList(Request $request, EntityManagerInterface $em): Response
    {
        $locale = $request->getLocale();
        $allTerm = $em->getRepository(GlossaryTranslation::class)->findAllTermByLocale($locale);
        $lexicals = [];
        $plurals = [];
        foreach ($allTerm as $term) {
            $char = strtolower($term->getTerm());
            if (!in_array($char, $lexicals)) {
                $lexicals[] = $char;
            }
            $plural = strtolower($term->getPlural());
            if (strlen(trim($plural)) > 0 && !in_array($plural, $plurals)) {
                $plurals[] = $plural;
            }
        }
        $termList = array_merge($lexicals, $plurals);

        return $this->json([
            'success' => true,
            'data' => $termList
        ]);
    }

    private function cleanTag($content) {
        $cleanTag = strip_tags($content);
        $cleanTag = str_replace(array("\r", "\n"), '', $cleanTag);
        $cleanTag = str_replace('&amp;', '', $cleanTag);
        $cleanTag = str_replace('&nbsp;', ' ', $cleanTag);
        return $cleanTag;
    }
}
