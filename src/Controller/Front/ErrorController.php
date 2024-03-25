<?php                                      
                                                     
namespace App\Controller\Front;

use App\Service\FooterService;
use App\Service\HeaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
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
     * @param FooterService $footerService
     * @param HeaderService $headerService
     */
    public function __construct(FooterService $footerService, HeaderService $headerService)
    {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
    }

    /**
     * Return a fake 404 in case a content is not available in a locale after switching locale.
     *
     * @Route({
     *     "en": "/en/content-language-unavailable",
     *     "fr": "/fr/contenu-langue-indisponible"
     * }, name="front.content.locale.unavailable")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function localeNotAvailable(Request $request): Response
    {
        $locale = $request->getLocale();

        return $this->render('error404.html.twig', [
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.content.locale.unavailable'),
        ]);
    }
}
