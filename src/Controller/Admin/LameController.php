<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Lame\CarouselLame;
use App\Entity\Lame\CenterMapLame;
use App\Entity\Lame\HighlightedLame;
use App\Entity\Lame\Lame;
use App\Entity\Lame\SpotLightLame;
use App\Form\Admin\SelectLameType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", requirements={"_locale" : "%app_locales%"})
 *
 * @IsGranted("ROLE_COORD")
 */
class LameController extends AbstractDashboardController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var AdminUrlGenerator
     */
    protected $router;

    /**
     * @param TranslatorInterface $translator
     * @param AdminUrlGenerator   $router
     */
    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $router)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->router->setDashboard(DashboardController::class);
    }

    /**
     * @Route("/admin/lame/list", name="admin.lame.list")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em): Response
    {
        $locale = $request->getLocale();
        $lameClasses = Lame::TYPE;
        $rawLames = [];

        foreach ($lameClasses as $lameClass) {
            $rawLames = array_merge($rawLames, $em->getRepository($lameClass)->findByLocaleOrdered($locale));
        }
        $lames = $this->prepareLameList($rawLames, $locale);

        $selectForm = $this->createForm(SelectLameType::class);
        $selectForm->handleRequest($request);


        if (
            $selectForm->isSubmitted() && $selectForm->isValid() && $this->isCsrfTokenValid(
                'lame',
                $request->get('_token')
            )
        ) {
            $selectFormData = $request->request->get('select_lame');
            $type = $selectFormData['type'];

            $typeRaw = explode('\\', $type);
            $crud = __NAMESPACE__ . '\\' . array_pop($typeRaw) . 'CrudController';
            $urlEdit = $this->router
                ->setController($crud)
                ->setAction(Crud::PAGE_NEW)
                ->generateUrl();
            return $this->redirect($urlEdit);
        }

        return $this->render('bundles/EasyAdminBundle/lame/list.html.twig', [
            'selectForm' => $selectForm->createView(),
            'lames'      => $lames,
            'ret'        => $this->router
                ->setController(self::class)
                ->setRoute('admin')
                ->generateUrl(),
            'addUrl'     => $this->router
                ->setController(self::class)
                ->setRoute('admin.lame.select')
                ->generateUrl(),
        ]);
    }

    /**
     * @param array  $rawData
     * @param string $locale
     *
     * @return array|null
     */
    protected function prepareLameList($rawData, $locale)
    {
        if (!is_iterable($rawData) || !count($rawData)) {
            return null;
        }

        $lames = [];
        foreach ($rawData as $rawLame) {
            $classNameRaw = explode('\\', get_class($rawLame));
            $className = array_pop($classNameRaw);

            $crud = __NAMESPACE__ . '\\' . $className . 'CrudController';
            $lame = [
                'title'       => $rawLame->getTitle(),
                'type'        => $rawLame->getType(),
                'weight'      => $rawLame->getWeight(),
                'isPublished' => $rawLame->getIsPublished(),
                'publishedAt' => $rawLame->getPublishedAt(),
                'createdAt'   => $rawLame->getCreatedAt(),
                'updatedAt'   => $rawLame->getUpdatedAt(),
                'createdBy'   => $rawLame->getCreatedBy(),
                'updatedBy'   => $rawLame->getUpdatedBy(),
                'edit'        => $this->router
                    ->setController($crud)
                    ->setAction(Action::EDIT)
                    ->setEntityId($rawLame->getId())
                    ->generateUrl(),
                'del'         => $this->router
                    ->setController(self::class)
                    ->setRoute('admin.lame.remove', [
                        'lametype' => get_class($rawLame),
                        'lameid'   => $rawLame->getId(),
                        '_locale'  => $locale,
                    ])
                    ->generateUrl(),
            ];
            $lames[] = $lame;
        }
        return $lames;
    }

    /**
     * Remove a route.
     *
     * @Route("/admin/lame/remove", name="admin.lame.remove")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function remove(Request $request, EntityManagerInterface $em): Response
    {
        $params = $request->query->get('routeParams');

        if (
            !is_array($params) ||
            !isset($params['lametype']) ||
            !isset($params['lameid']) ||
            !in_array($params['lametype'], Lame::TYPE)
        ) {
            throw $this->createNotFoundException($this->translator->trans('notfound.lame'));
        }

        $lame = $em->getRepository($params['lametype'])->findOneBy(['id' => $params['lameid']]);
        if (!$lame instanceof Lame || !$lame->getId()) {
            throw $this->createNotFoundException($this->translator->trans('notfound.lame'));
        }

        $em->remove($lame);
        $em->flush();

        $listUrl = $this->router
            ->setController(self::class)
            ->setRoute('admin.lame.list')
            ->generateUrl();
        return $this->redirect($listUrl);
    }
}
