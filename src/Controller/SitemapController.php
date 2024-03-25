<?php                                      
                                                     
namespace App\Controller;

use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActualityRepository;
use App\Repository\DatasetRepository;
use App\Repository\DataWorkshopRepository;
use App\Repository\EventRepository;
use App\Repository\FaqBlockRepository;
use App\Repository\GlossaryRepository;
use App\Repository\GuideRepository;
use App\Repository\InstitutionRepository;
use App\Repository\PageRepository;

class SitemapController extends AbstractController
{
    /**
     * Generate sitemap.
     * 
     * @Route({
     *     "fr" : "/fr/sitemap.xml",
     *     "en" : "/en/sitemap.xml",
     *     "_format" : "xml",
     * }, name="sitemap")
     *
     */
    public function index(
        Request $request,
        ActualityRepository $actualityRepository,
        DatasetRepository $datasetRepository,
        DataWorkShopRepository $dataWorkshopRepository,
        EventRepository $eventRepository,
        FaqBlockRepository $faqBlockRepository,
        GlossaryRepository $glossaryRepository,
        GuideRepository $guideRepository,
        InstitutionRepository $institutionsRepository,
        PageRepository $pageRepository
        ): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = [
            'loc' => $this->generateUrl('front.homepage'),
            'changefreq' => 'monthly',
            'priority' => 1.0 
        ];

        $urls[] = [
            'loc' => $this->generateUrl('front.faq.list'),
            'changefreq' => 'monthly',
            'priority' => 0.5 
        ];

        $urls[] = [
            'loc' => $this->generateUrl('front.contact'),
            'changefreq' => 'yearly',
            'priority' => 0.5 
        ];

        $urls[] = [
            'loc' => $this->generateUrl('front.glossary'),
            'changefreq' => 'weekly',
            'priority' => 0.5 ];

        $urls[] = [
            'loc' => $this->generateUrl('front.dataset.logigram'),
            'changefreq' => 'weekly',
            'priority' => 0.5 ];

        $urls[] = [
            'loc' => $this->generateUrl('front.rss.all'),
            'changefreq' => 'daily',
            'priority' => 0.5
        ];

        


        foreach ($actualityRepository->findAll() as $actuality) {
            $images = [
                'loc' => $actuality->getImage()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('front.actuality.list', ['slug' => $actuality->getSlug()]),
                'lastmod' => $actuality->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'image' => $images,
                'changefreq' => 'weekly',
                'priority' => 0.7
            ];
        }

        foreach ($dataWorkshopRepository->findAll() as $dataWorkshop) {
            $images = [
                'loc' => $dataWorkshop->getImage()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('front.dataworkshop.list'),
                'lastmod' => $dataWorkshop->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'image' => $images,
                'changefreq' => 'weekly',
                'priority' => 0.8
            ];
        }

        foreach ($datasetRepository->findAll() as $dataset) {
            $images = [
                'loc' => $dataset->getImage()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('front.dataset.list', ['slug' => $dataset->getSlug()]),
                'lastmod' => $dataset->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'image' => $images,
                'changefreq' => 'weekly',
                'priority' => 0.9
            ];
        }

        foreach ($institutionsRepository->findAll() as $institution) {
            $images = [
                'loc' => $institution->getImage()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('front.institutions.list'),
                'title' => $institution->getUrlInstitution(),
                'lastmod' => $institution->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'image' => $images,
                'changefreq' => 'weekly',
                'priority' => 0.6
            ];
        }

        foreach ($guideRepository->findAll() as $guide) {
            $images = [
                'loc' => $guide->getImage()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('front.guide.homepage', ['slug' => $guide->getSlug()]),
                'lastmod' => $guide->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'image' => $images,
                'changefreq' => 'monthly',
                'priority' => 0.5
            ];
        }

        foreach ($eventRepository->findAll() as $event) {
            $urls[] = [
                'loc' => $this->generateUrl('front.event.list', ['title' => $event->getTitle()]),
                'lastmod' => $event->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'changefreq' => 'weekly',
                'priority' => 0.9
            ];
        }

        foreach ($pageRepository->findAll() as $page) {
            $urls[] = [
                'loc' => $this->generateUrl('front.page.show', ['slug' => $page->getSlug()]),
                'lastmod' => $page->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'changefreq' => 'weekly',
                'priority' => 0.6
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls'     => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }
}
