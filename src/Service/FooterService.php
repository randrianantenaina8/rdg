<?php                                      
                                                     
namespace App\Service;

use App\Entity\MenuBasic;
use App\Entity\SocialNetwork;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Find all informations needed to footer.
 */
class FooterService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlService
     */
    private $urlService;


    /**
     * @param EntityManagerInterface $em
     * @param UrlService             $urlService
     */
    public function __construct(
        EntityManagerInterface $em,
        UrlService $urlService
    ) {
        $this->em = $em;
        $this->urlService = $urlService;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getFooterMenu(string $locale)
    {
        $linksEssential = [];
        $menus = $this->em->getRepository(MenuBasic::class)->findActivatedOrdered($locale);
        foreach ($menus as $menu) {
            $url = $this->urlService->getUrl($menu, $locale);
            $linksEssential[] = [
                'wording' => $menu->getLabel(),
                'link' => $url,
            ];
        }

        return $linksEssential;
    }

    /**
     * @return array
     */
    public function getSocialNetwork()
    {
        $networksEssential = [];
        $networks = $this->em->getRepository(SocialNetwork::class)->findAllOrderByWeight();
        foreach ($networks as $network) {
            $networksEssential[] = [
                'name' => $network->getName(),
                'link' => $network->getLink(),
                'imgCss' => $network->getImage(),
            ];
        }

        return $networksEssential;
    }

    /**
     * @return array
     */
    public function getLinksAndNetworks(string $locale)
    {
        return [
            'links' => $this->getFooterMenu($locale),
            'networks' => $this->getSocialNetwork(),
        ];
    }
}
