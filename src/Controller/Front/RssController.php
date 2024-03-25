<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Actuality;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Front controller to retrieve RSS flow.
 */
class RssController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;


    /**
     * @param TranslatorInterface   $translator
     * @param UrlGeneratorInterface $router
     */
    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @Route({
     *     "fr" : "/fr/rss",
     *     "en" : "/en/rss"
     * }, name="front.rss.all", priority="2")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $actualities = $em->getRepository(Actuality::class)->findLastPublishedByLocaleLimited($request->getLocale());
        $response = $this->render('rss/all.xml.twig', [
            'url' => $this->router->generate(
                'front.rss.all',
                ['_locale' => $request->getLocale()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'actualities' => $actualities,
        ]);
        $response->headers->set("Content-Type", "text/xml");

        return $response->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_SHORT);
    }
}
