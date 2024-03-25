<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Actuality;
use App\Entity\Config;
use App\Entity\Dataset;
use App\Entity\Guide;
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
 * Front Controller restricted to BO members to preview some entities.
 */
class PreviewController extends AbstractController
{
    /**
     * List of entities that can be identified by an unique slug.
     */
    public const TYPES = [
        'actuality' => [
            'entity'   => Actuality::class,
            'template' => 'page.html.twig',
        ],
        'guide'     => [
            'entity'   => Guide::class,
            'template' => 'oneguide.html.twig',
        ],
        'dataset' => [
            'entity' => Dataset::class,
            'template' => 'dataset.html.twig',
        ],
    ];

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
     * @var AlertService
     */
    private $alertService;

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
     * Display an entity identified by its slug and locale as a preview from Back-Office.
     *
     * @Route({
     *     "en": "/en/admin/preview/{type}/{slug}",
     *     "fr": "/fr/admin/preview/{type}/{slug}"
     * }, name="preview.entity.show")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param AlertService           $alertService
     * @param string                 $type
     * @param string                 $slug
     *
     * @return Response
     */
    public function showPreview(Request $request, EntityManagerInterface $em, AlertService $alertService, string $type, string $slug): Response
    {
        $locale = $request->getLocale();

        if ($slug === Config::ROUTE_ERR_PARAMS) {
            return $this->redirectToRoute('front.content.locale.unavailable');
        }
        if (!is_string($type) || !isset(self::TYPES[$type]['entity'])) {
            throw $this->createNotFoundException($this->translator->trans('notfound.entity'));
        }
        $entityClass = self::TYPES[$type]['entity'];
        $entity = $em->getRepository($entityClass)->findBySlugAndLocale($slug, $locale);

        if (!$entity instanceof $entityClass) {
            throw $this->createNotFoundException($this->translator->trans('notfound.entity'));
        }
        $alerts = $alertService->alert($locale);

        return $this->render(self::TYPES[$type]['template'], [
            'preview'     => true,
            'entity'      => $entity,
            'alerts'      => $alerts,
            'introBanner' => $this->headerService->getIntroBanner('preview.entity.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherPreview(),
        ]);
    }
}
