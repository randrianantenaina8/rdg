<?php                                      
                                                     
namespace App\Service;

use App\Entity\Config;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generate array with keys 'name' & 'url' to display breadcrumbs front-end site.
 */
class BreadcrumbsService
{
    /**
     * Keywords defined to generate breadcrumbs.
     */
    protected const MAP_ROUTES = [
        'root'          => Config::ROUTES['Homepage'],
        'contact'       => Config::ROUTES['Contact'],
        'glossary'      => Config::ROUTES['Glossary'],
        'institutions'  => Config::ROUTES['Institutions'],
        'institution'   => Config::ROUTES['Institutions'],
        'dataworkshops' => Config::ROUTES['Data workshops'],
        'dataworkshop'  => Config::ROUTES['Data workshops'],
        'events'        => Config::ROUTES['Events'],
        'event'         => Config::ENTITY_ROUTES['event'],
        'pastevents'    => Config::ROUTES['Past events'],
        'nextevents'    => Config::ROUTES['Future events'],
        'actualities'   => Config::ROUTES['Actualities'],
        'actuality'     => Config::ENTITY_ROUTES['actuality'], // Need slug param
        'datasets'      => Config::ROUTES['Datasets'],
        'dataset'       => Config::ENTITY_ROUTES['dataset'],
        'support'       => Config::ROUTES['Be accompanied'],
        'faq'           => Config::ROUTES['Faq'],
        'team'          => Config::ROUTES['Team'],
        'guide'         => Config::ENTITY_ROUTES['guide'], // Need slug params
        'page'          => Config::ENTITY_ROUTES['page'], // Need slug params
        'disciplines'   => Config::ROUTES['Disciplines'],
        'repository'    => Config::ENTITY_ROUTES['repository']
    ];

    /**
     * @var array
     */
    private $sitemap;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param UrlGeneratorInterface $router
     * @param TranslatorInterface   $translator
     */
    public function __construct(UrlGeneratorInterface $router, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->sitemap = [
            'root' => [
                'datasets'      => 'dataset',
                'institutions'  => 'institution',
                'dataworkshops' => 'dataworkshop',
                'events'        => 'event',
                'pastevents'    => null,
                'nextevents'    => null,
                'actualities'   => 'actuality',
                'guide'         => null,
                'faq'           => null,
                'page'          => null,
                'contact'       => null,
                'glossary'      => null,
                'team'          => null,
                'disciplines'   => 'repository'
            ]
        ];
    }

    /**
     * Usefull to know which types exists... But the simpliest idea is just to open this files. ;)
     *
     * @return string[]
     */
    public function getTypes()
    {
        return array_keys(self::MAP_ROUTES);
    }

    /**
     * Generate an array where the first element is the first element in breadcrumbs and etc...
     *
     * @param string      $type
     *   The type should one of the key used in self::MAP_ROUTES.
     * @param string      $locale
     *   The current locale.
     * @param string|null $title
     *   In case of entity displaying, its title.
     * @param array       $params
     *   For example, in case of entity displaying, its slug.
     *
     * @return array
     */
    public function generate(string $type, string $locale, ?string $title = null, $params = [])
    {
        $path = [];
        if (!isset($params['_locale'])) {
            $params['_locale'] = $locale;
        }

        $path[] = $this->getItem('root', $params);

        if ('root' === $type) {
            return $path;
        }
        foreach ($this->sitemap['root'] as $key => $value) {
            if ($key === $type) {
                $path[] = $this->getItem($key, $params, $title);
                return $path;
            }
            if ($value === $type) {
                $path[] = $this->getItem($key, ['_locale' => $locale]);
                $path[] = $this->getItem($value, $params, $title);
                return $path;
            }
        }
        return $path;
    }

    /**
     * Generate the array with name and url for one item.
     *
     * @param string      $type
     * @param array       $params
     * @param string|null $title
     *
     * @return array
     */
    protected function getItem(string $type, array $params, ?string $title = null)
    {
        $name = ucfirst($this->translator->trans('breadcrumbs.' . $type));
        if (is_string($title) && mb_strlen($title) > 0) {
            $name = $title;
        }

        return [
            'name' => $name,
            'url'  => $this->router->generate(
                self::MAP_ROUTES[$type],
                $params,
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];
    }
}
