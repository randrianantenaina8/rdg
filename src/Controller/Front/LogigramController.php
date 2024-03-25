<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Logigram;
use App\Service\FooterService;
use App\Service\HeaderService;
use App\Service\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\LogigramService;

/**
 * Front Controller that returns all contents related to Logigram entity.
 */
class LogigramController extends AbstractController
{
    public const LIMIT = 5;

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
     * To get alerts.
     *
     * @var AlertService
     */
    protected $alertService;

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
    ) {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Display an logigram identified by its slug and in the locale requested.
     *
     * @Route({
     *     "en": "/en/logigram/{slug}",
     *     "fr": "/fr/logigram/{slug}"
     * }, name="front.logigram.show")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $slug
     * @param LogigramService        $logigramService
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, string $slug, LogigramService $logigramService): Response
    {
        $locale = $request->getLocale();
        $logigram = $em->getRepository(Logigram::class)->findBySlugAndLocale($slug, $locale);

        $logigramData = $logigramService->loadLogigram($logigram);

        return $this->render('logigram.html.twig', [
            'logigram' => $logigramData,
            'introBanner' => $this->headerService->getIntroBanner('front.logigram.show', $locale),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }
}
