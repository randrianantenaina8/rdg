<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Heading;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Front Controller that returns all contents relative to FaqBlock entity.
 */
class FaqController extends AbstractController
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
     * @Route({
     *     "fr" : "/fr/faq",
     *     "en" : "/en/faq"
     * }, name="front.faq.list", methods="GET")
     *
     * @param Request $request
     * @param AlertService  $alertService
     * 
     * @return Response
     */
    public function index(Request $request, AlertService $alertService): Response
    {
        $locale = $request->getLocale();
        $headings = $this->em->getRepository(Heading::class)->findAllByFaqRestrictedByLocale($locale);
        $faq = [];
        /** @var Heading $heading */
        foreach ($headings as $heading) {
            $faqItems = [];
            $faqItemsInHeading = $heading->getFaqs();

            foreach ($faqItemsInHeading as $faqItem) {
                $faqItems[] = [
                    'faqItem' => $faqItem->getFaq(),
                    'weight' => $faqItem->getWeight(),
                ];
            }

            $faq[$heading->getName()] = $faqItems;
        }
        $alerts = $alertService->alert($locale);

        return $this->render('faqs.html.twig', [
            'headings' => $headings,
            'faq' => $faq,
            'alerts'  => $alerts,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'faq',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.faq.list', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.faq.list'),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }
}
