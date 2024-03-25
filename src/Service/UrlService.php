<?php                                      
                                                     
namespace App\Service;

use App\Entity\MenuBasic;
use App\Entity\MenuMultiple;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Retrieve url string from menu link properties.
 */
class UrlService
{
    public const NULL_URL = null;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var array
     */
    private $locales;

    /**
     * @param string                $locales
     * @param UrlGeneratorInterface $router
     */
    public function __construct($locales, UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->locales = explode('|', $locales);
        if (false === $this->locales) {
            $this->locales = [];
        }
    }

    public function getUrl($menu, string $locale): ?string
    {
        $url = self::NULL_URL;

        if ($menu->getExternalLink()) {
            $url = $menu->getExternalLink();
        } elseif ($menu->getSystemLink()) {
            try {
                $url = $this->router->generate(
                    $menu->getSystemLink(),
                    ['_locale' => $locale],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            } catch (RouteNotFoundException $e) {
                $url = self::NULL_URL;
            }
        } elseif ($menu->getPageLink()) {
            $slug = $menu->getPageLink()->getSlug();
            if (!$slug) {
                $url = self::NULL_URL;
            }
            try {
                $url = $this->router->generate(
                    'front.page.show',
                    [
                        '_locale' => $locale,
                        'slug' => $slug,
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            } catch (RouteNotFoundException $e) {
                $url = self::NULL_URL;
            }
        }

        return $url;
    }
}
