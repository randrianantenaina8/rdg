<?php                                      
                                                     
namespace App\Controller\Front\Preview;

use App\Entity\PageDraft;
use App\Service\FooterService;
use App\Service\HeaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageDraftController extends AbstractController
{
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
     * @param TranslatorInterface $translator
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     */
    public function __construct(
        TranslatorInterface $translator,
        FooterService $footerService,
        HeaderService $headerService
    ) {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
    }

    /**
     * Display a PageDraft preview as a published page.
     *
     * @Route({
     *     "en": "/en/admin/preview/page/{slug}",
     *     "fr": "/fr/admin/previsualisation/page/{slug}",
     * }, name="front.preview.pagedraft")
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $slug
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, string $slug): Response
    {
        $locale = $request->getLocale();
        $pageDraft = $em->getRepository(PageDraft::class)->findOneBySlugAndLocale($slug, $locale);

        if (!$pageDraft instanceof PageDraft) {
            throw $this->createNotFoundException($this->translator->trans('notfound.page'));
        }

        return $this->render('front/preview/pagedraft.html.twig', [
            'preview'     => true,
            'entity'      => $pageDraft,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'page',
                $locale,
                $pageDraft->getTitle(),
                ['slug' => $slug]
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.page.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSlug('front.preview.pagedraft', $pageDraft),
        ]);
    }
}
