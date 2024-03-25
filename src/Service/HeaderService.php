<?php                                      
                                                     
namespace App\Service;

use App\Entity\Actuality;
use App\Entity\AlertMsg;
use App\Entity\Dataset;
use App\Entity\DataWorkshop;
use App\Entity\Event;
use App\Entity\Guide;
use App\Entity\Institution;
use App\Entity\ProjectTeam;
use App\Entity\Introduction;
use App\Entity\MenuBasic;
use App\Entity\MenuMultiple;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Find all informations needed to header.
 */
class HeaderService
{
    public const DEFAULT_ROUTE = 'front.homepage';

    public const ENTITY_TYPE = [
        Actuality::class => 'content.actuality',
        Guide::class => 'content.guide',
        Dataset::class => 'content.dataset',
        Institution::class => 'content.institution',
        DataWorkshop::class => 'content.dataworkshop',
        Event::class => 'content.event',
        ProjectTeam::class => 'content.team'
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * @var BreadcrumbsService
     */
    private $breadcrumbsService;

    /**
     * @var string[]
     */
    private $locales;


    /**
     * @param                        $locales
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface  $router
     * @param UrlService             $urlService
     * @param BreadcrumbsService     $breadcrumbsService
     */
    public function __construct(
        $locales,
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        UrlService $urlService,
        BreadcrumbsService $breadcrumbsService
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->urlService = $urlService;
        $this->breadcrumbsService = $breadcrumbsService;
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
    }

    /**
     * @param string $locale
     *
     * @return array|null
     */
    public function getAlertMsg($locale = 'fr')
    {
        $messages = [];
        $alerts = $this->em->getRepository(AlertMsg::class)->findActiveMessages($locale);

        if (!count($alerts)) {
            return null;
        }
        foreach ($alerts as $alert) {
            $messages[] = [
                'message' => $alert->getMessage(),
                'type'    => $this->getColorType($alert->getType()),
            ];
        }

        return $messages;
    }

    /**
     * @param int $type
     *
     * @return string
     */
    protected function getColorType($type)
    {
        switch ($type) {
            case AlertMsg::INFO:
                $color = 'info';
                break;
            case AlertMsg::WARNING:
                $color = 'warning';
                break;
            case AlertMsg::ALERT:
                $color = 'error';
                break;
            default:
                $color = '';
                break;
        }
        return $color;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getMainMenu($locale = 'fr')
    {
        $mainLinks = [];
        $menus = $this->em->getRepository(MenuMultiple::class)->findRootByLocaleOrderByWeight($locale);
        /** @var MenuMultiple $menu */
        foreach ($menus as $menu) {
            $mainLinks[] = $this->formatMenu($menu, $locale);
        }

        return $mainLinks;
    }

    /**
     * @param MenuMultiple $menu
     * @param string       $locale
     *
     * @return array
     */
    protected function formatMenu($menu, $locale)
    {
        $children = [];
        foreach ($menu->getChilds() as $child) {
            $children[] = $this->formatMenu($child, $locale);
        }

        return [
            'label' => $menu->getLabel(),
            'link' => $this->urlService->getUrl($menu, $locale),
            'weight' => $menu->getWeight(),
            'childs' => $children,
        ];
    }

    /**
     * @param string $systemName
     * @param array  $args
     *
     * @return array
     */
    public function getSwitcherSystem(string $systemName, array $args = [])
    {
        $routes = [];

        foreach ($this->locales as $locale) {
            $args['_locale'] = $locale;
            $routes[$locale] = $this->router->generate(
                $systemName,
                $args,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        return $routes;
    }

    /**
     * @param string $routeName
     * @param object $entity
     * @param array  $args
     *
     * @return array
     */
    public function getSwitcherSlug($routeName, $entity, $args = [])
    {
        $routes = [];
        $className = get_class($entity);
        $repository = $this->em->getRepository($className . 'Translation');

        foreach ($this->locales as $locale) {
            $data = $repository->findSlugByIdAndLocale($entity->getId(), $locale);

            if (isset($data['slug'])) {
                $args['_locale'] = $locale;
                $args['slug'] = $data['slug'];
                $url = $this->router->generate(
                    $routeName,
                    $args,
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            } else {
                $url = $this->router->generate(
                    'front.content.locale.unavailable',
                    ['_locale' => $locale,],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
            $routes[$locale] = $url;
        }

        return $routes;
    }

    /**
     * @param string $routeName
     * @param array  $args
     *
     * @return array
     */
    public function getErrorSwitcher($routeName, $args = [])
    {
        $routes = [];

        foreach ($this->locales as $locale) {
            $args['_locale'] = $locale;
            $routes[$locale] = $this->router->generate(
                $routeName,
                $args,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        return $routes;
    }

    /**
     * @return array
     */
    public function getSwitcherPreview()
    {
        $routes = [];

        foreach ($this->locales as $locale) {
            $routes[$locale] = '#';
        }

        return $routes;
    }

    /**
     * Returns available types to generate a breadcrumb.
     *
     * @return string[]
     */
    public function getBreadcrumbsTypes()
    {
        return $this->breadcrumbsService->getTypes();
    }

    /**
     * @param string      $type
     * @param string      $locale
     * @param string|null $title
     * @param array       $params
     *
     * @return array
     */
    public function generateBreadcrumbs(string $type, string $locale, ?string $title = null, array $params = [])
    {
        return $this->breadcrumbsService->generate($type, $locale, $title, $params);
    }

    /**
     * @param string $routeName
     * @param string $locale
     *
     * @return Introduction
     */
    public function getIntroBanner(string $routeName, string $locale)
    {
        $introFound = $this->em->getRepository(Introduction::class)->findOneByRouteIfTranslated($routeName, $locale);

        if (!$introFound instanceof Introduction) {
            return new Introduction();
        }
        return $introFound;
    }
}
