<?php

namespace App\Controller\Front;

use App\Entity\DataRepository;
use App\Repository\DataRepositoryRepository;
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

/**
 * Front Controller that returns all contents relation to DataRepository entity.
 */
class DataRepositoryController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
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
     * @param TranslatorInterface $translator
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     * @param AlertService        $alertService
     */
    public function __construct(
        TranslatorInterface $translator,
        FooterService       $footerService,
        HeaderService       $headerService,
        AlertService        $alertService
    )
    {
        $this->translator = $translator;
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->alertService = $alertService;
    }

    /**
     * Display an dataRepository identified by its id and in the locale requested.
     *
     * @Route({
     *     "en": "/en/repository/{id}",
     *     "fr": "/fr/entrepot/{id}"
     * }, name="front.dataRepository.show")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param string $id
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, string $id): Response
    {
        $locale = $request->getLocale();
        $dataRepository = $em->getRepository(DataRepository::class)->findOneByIdWithLocale($id, $locale);

        if (!$dataRepository instanceof DataRepository) {
            throw $this->createNotFoundException($this->translator->trans('notfound.dataRepository'));
        }

        return $this->render('dataRepository.html.twig', [
            'entity' => $dataRepository,
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'repository',
                $locale,
                $dataRepository,
                ['id' => $id]
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.dataRepository.show', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'metaDescription' => $this->cleanMeta($dataRepository->getDescription()),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.dataRepository.show', ['id' => $id]),
        ])->setPublic()->setMaxAge(FrontControllerInterface::CACHE_MAX_AGE_LONG);
    }

    private function cleanMeta($meta)
    {
        $cleanMeta = strip_tags($meta);
        $cleanMeta = str_replace(array("\r", "\n"), '', $cleanMeta);
        $cleanMeta = str_replace('&amp;', '', $cleanMeta);
        $cleanMeta = str_replace('&nbsp;', ' ', $cleanMeta);
        return $cleanMeta;
    }
}
