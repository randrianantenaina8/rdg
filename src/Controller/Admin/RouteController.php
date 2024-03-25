<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Form\Admin\RouteType;
use Doctrine\ORM\EntityManagerInterface;
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
 * @IsGranted("ROLE_ADMIN")
 */
class RouteController extends AbstractDashboardController
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
     * @Route("/admin/route/add", name="admin.route.add")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $data = [
            'name' => null,
            'route' => null,
        ];
        $ret = [
            'label' => '',
            'url' => $this->router
                ->setController(self::class)
                ->setRoute('admin.route.list')
                ->generateUrl()
        ];

        $routeForm = $this->createForm(RouteType::class, $data);
        $routeForm->handleRequest($request);

        if (
            $routeForm->isSubmitted() &&
            $routeForm->isValid() &&
            $this->isCsrfTokenValid('route', $request->get('_token'))
        ) {
            $formData = $request->request->get('route');
            $config = $em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
            $configRoute = $config->getData();
            if (!isset($configRoute[$formData['name']])) {
                $configRoute[$formData['name']] = $formData['route'];
                $config->setData($configRoute);
                $em->flush();
                $this->addFlash('success', $this->translator->trans('route.flash.success.new'));
                return $this->redirect($ret['url']);
            }
            $this->addFlash('error', $this->translator->trans('route.flash.error'));
        }

        return $this->render('bundles/EasyAdminBundle/_routeForm.html.twig', [
            'formRoute' => $routeForm->createView(),
            'ret'       => $ret,
            'helpList'  => Config::ROUTES,
        ]);
    }

    /**
     * @Route("/admin/route/edit/", name="admin.route.edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $name
     *
     * @return Response
     */
    public function modify(Request $request, EntityManagerInterface $em, string $name)
    {
        $config = $em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
        if (!isset($config->getData()[$name])) {
            throw $this->createNotFoundException('This route does not exist.');
        }
        $data = [
            'name' => $name,
            'route' => $config->getData()[$name],
        ];
        $ret = [
            'label' => '',
            'url' => $this->router
                ->setController(self::class)
                ->setRoute('admin.route.list')
                ->generateUrl()
        ];
        $routeForm = $this->createForm(RouteType::class, $data);
        $routeForm->handleRequest($request);

        if (
            $routeForm->isSubmitted() &&
            $routeForm->isValid() &&
            $this->isCsrfTokenValid('route', $request->get('_token'))
        ) {
            $formData = $request->request->get('route');
            $configRoute = $config->getData();
            unset($configRoute[$name]);
            $configRoute[$formData['name']] = $formData['route'];
            $config->setData($configRoute);
            $em->flush();
            $this->addFlash('success', $this->translator->trans('route.flash.success.edit'));
            return $this->redirect($ret['url']);
        }

        return $this->render('bundles/EasyAdminBundle/_routeForm.html.twig', [
            'data'      => $data,
            'formRoute' => $routeForm->createView(),
            'ret'       => $ret,
            'helpList'  => Config::ROUTES,
        ]);
    }

    /**
     * Remove a route.
     *
     * @Route("/admin/route/remove/{name}", name="admin.route.remove")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param string                 $name
     *
     * @return Response
     */
    public function remove(Request $request, EntityManagerInterface $em, string $name): Response
    {
        $params = $request->query->get('routeParams');
        if (!is_array($params) || !isset($params['name'])) {
            throw $this->createNotFoundException($this->translator->trans('notfound.route'));
        }
        $config = $em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
        if (!isset($config->getData()[$name])) {
            throw $this->createNotFoundException($this->translator->trans('notfound.route'));
        }

        $data = $config->getData();
        unset($data[$name]);
        $config->setData($data);
        $em->flush();
        $listUrl = $this->router
            ->setController(self::class)
            ->setRoute('admin.route.list')
            ->generateUrl();
        $this->addFlash('success', $this->translator->trans('route.flash.success.delete'));

        return $this->redirect($listUrl);
    }

    /**
     * @Route("/admin/route/list", name="admin.route.list")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em): Response
    {
        $routes = [];
        $config = $em->getRepository(Config::class)->findOneBy(['name' => Config::ROUTE]);
        $rawData = $config->getData();
        $ret = [
            'label' => '',
            'url' => $this->router
                ->setController(self::class)
                ->setRoute('admin.config.board')
                ->generateUrl()
        ];

        foreach ($rawData as $name => $value) {
            $route = [
                'name' => $name,
                'route' => $value,
                'edit' => $this->router
                    ->setController(self::class)
                    ->setRoute(
                        'admin.route.edit',
                        ['name' => $name, '_locale' => $request->getLocale()]
                    )
                    ->generateUrl(),
                'del' => $this->router
                    ->setController(self::class)
                    ->setRoute(
                        'admin.route.remove',
                        ['name' => $name, '_locale' => $request->getLocale()]
                    )
                    ->generateUrl(),
            ];
            $routes[] = $route;
        }

        return $this->render('bundles/EasyAdminBundle/_routeList.html.twig', [
            'routes' => $routes,
            'ret' => $ret,
            'addUrl' => $this->router
                ->setController(self::class)
                ->setRoute('admin.route.add')
                ->generateUrl(),
        ]);
    }
}
