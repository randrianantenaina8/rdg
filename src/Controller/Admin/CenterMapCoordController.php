<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\CenterMapCoord;
use App\Entity\Lame\CenterMapLame;
use App\Form\Admin\CenterMapCoordType;
use App\Service\CenterMapCoordService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_COORD")
 */
class CenterMapCoordController extends AbstractDashboardController
{

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param AdminUrlGenerator      $router
     * @param EntityManagerInterface $em
     */
    public function __construct(AdminUrlGenerator $router, EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @Route(
     *     "/{_locale}/admin/lamina/{laminaId}/map/edit",
     *     name="admin.centermapcoord.add",
     *     requirements={"_locale" : "%app_locales%", "id": "\d+"}
     * )
     *
     * @param Request               $request
     * @param CenterMapCoordService $centerMapCoordService
     * @param int                   $laminaId
     *
     * @return Response
     */
    public function addPoint(Request $request, CenterMapCoordService $centerMapCoordService, int $laminaId): Response
    {
        $lamina = $this->em->getRepository(CenterMapLame::class)->findOneBy(['id' => $laminaId]);
        if (!$lamina instanceof CenterMapLame) {
            throw $this->createNotFoundException('Do not recognize the lamina linked to.');
        }

        $coordInit = new CenterMapCoord();
        $coordInit->setId(null);
        $coordInit->setCenterLamina($lamina);
        $form = $this->createForm(CenterMapCoordType::class, $coordInit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // If no id or (id but not linked to our laminaId), this is a new CenterMapCoord.
            if (
                !$coordInit->getId() ||
                (
                    $coordInit->getId() &&
                    !$this->em->getRepository(CenterMapCoord::class)
                        ->findOneByIdAndLamina($coordInit->getId(), $lamina) instanceof CenterMapCoord
                )
            ) {
                $coordInit->setId(null);
                $this->em->persist($coordInit);
                // Else we fetch original object to let him to be updated.
            } else {
                /** @var CenterMapCoord $originalCoord */
                $originalCoord = $this->em->getRepository(CenterMapCoord::class)
                    ->findOneByIdAndLamina($coordInit->getId(), $lamina);
                $originalCoord->setName($coordInit->getName());
                $originalCoord->setX($coordInit->getX());
                $originalCoord->setY($coordInit->getY());
                $originalCoord->setDataworkshop($coordInit->getDataworkshop());
                $originalCoord->setInstitution($coordInit->getInstitution());
            }
            $this->em->flush();
            return $this->redirect(
                $this->router
                    ->setController(self::class)
                    ->setRoute('admin.centermapcoord.add', ['laminaId' => $laminaId])
                    ->generateUrl()
            );
        }

        return $this->render(
            'bundles/EasyAdminBundle/lame/centermap/_map_form.html.twig',
            [
                'backUrl' => $this->router
                    ->setController(CenterMapLameCrudController::class)
                    ->setAction(Action::EDIT)
                    ->setEntityId($laminaId)
                    ->generateUrl(),
                'form' => $form->createView(),
                'points' => $centerMapCoordService->getPoints($lamina, true),
                'laminaId' => $laminaId,
            ]
        );
    }

    /**
     * @Route("/{_locale}/admin/lamina/{laminaId}/map/remove/{id}", name="admin.centermapcoord.remove")
     *
     * @param Request $request
     * @param int     $laminaId
     * @param int     $id
     *
     * @return Response
     */
    public function removePoint(Request $request, int $laminaId, int $id): Response
    {
        $lamina = $this->em->getRepository(CenterMapLame::class)->findOneBy(['id' => $laminaId]);
        $centerMapCoord = $this->em->getRepository(CenterMapCoord::class)->findOneByIdAndLamina($id, $lamina);

        if ($centerMapCoord instanceof CenterMapCoord) {
            $this->em->remove($centerMapCoord);
            $this->em->flush();
        }
        return $this->redirect(
            $this->router
                ->setController(self::class)
                ->setRoute('admin.centermapcoord.add', ['laminaId' => $laminaId, '_locale' => $request->getLocale()])
                ->generateUrl()
        );
    }
}
