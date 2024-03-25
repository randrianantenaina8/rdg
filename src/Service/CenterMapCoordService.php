<?php                                      
                                                     
namespace App\Service;

use App\Controller\Admin\CenterMapCoordController;
use App\Entity\CenterMapCoord;
use App\Entity\Lame\CenterMapLame;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class CenterMapCoordService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param CenterMapLame $lamina
     * @param false         $withUrl
     *
     * @return array
     */
    public function getPoints(CenterMapLame $lamina, $withUrl = false)
    {
        $allPoints = [];
        $centerMaps = $this->em->getRepository(CenterMapCoord::class)->findAllByLamina($lamina);

        if (is_iterable($centerMaps)) {
            /** @var CenterMapCoord $point */
            foreach ($centerMaps as $point) {
                $data = new \stdClass();
                $data->id = $point->getId();
                $data->name = $point->getName();
                $coord = new \stdClass();
                $coord->x = $point->getX();
                $coord->y = $point->getY();
                $data->point = $coord;
                $data->institution = ($point->getInstitution()) ? $point->getInstitution()->getId() : null;
                $data->dataworkshop = ($point->getDataworkshop()) ? $point->getDataworkshop()->getId() : null;

                if ($withUrl) {
                    $data->removeUrl = $this->router
                        ->setController(CenterMapCoordController::class)
                        ->setRoute(
                            'admin.centermapcoord.remove',
                            ['laminaId' => $lamina->getId(), 'id' => $point->getId()]
                        )
                        ->generateUrl();
                }
                $allPoints[] = $data;
            }
        }

        return $allPoints;
    }
}
