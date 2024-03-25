<?php                                      
                                                     
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", requirements={"_locale" : "%app_locales%"})
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ConfigController extends AbstractDashboardController
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
     * @Route("/admin/config/board", name="admin.config.board")
     *
     * @return Response
     */
    public function index(): Response
    {
        $config = [
            'route',
            'gdpr',
        ];

        // Routes
        $route = [
            'list' => [
                'label' => ucfirst($this->translator->trans('bo.list')),
                'url'   => $this->router
                    ->setController(RouteController::class)
                    ->setRoute('admin.route.list')
                    ->generateUrl(),
            ],
            'add'  => [
                'label' => ucfirst($this->translator->trans('bo.add')),
                'url'   => $this->router
                    ->setController(RouteController::class)
                    ->setRoute('admin.route.add')
                    ->generateUrl(),
            ]
        ];
        $config['route'] = $route;

        // GDPR
        $gdpr = [];
        $config['gdpr'] = $gdpr;
        return $this->render('bundles/EasyAdminBundle/_configurationBoard.html.twig', [
            'config' => $config,
        ]);
    }

    /**
     *
     * @codeCoverageIgnore
     *
     * Execute the Symfony command for Solr indexation
     * @Route("/admin/config/board/solr", name="bo.solr.index")
     * 
     * @return Response
     */
    public function indexCommand(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'rdg:index'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();
        $response = new Response();

        return $this->render('solr/_solrIndexed.html.twig', [
            'content'  => $content,
            'response' => $response
        ]); 
    }
}
