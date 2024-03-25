<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Config;
use App\Entity\FaqHighlighted;
use App\Form\Admin\FaqHighlightedAutoType;
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
 * @IsGranted("ROLE_CONTRIB")
 */
class FaqHighlightedAutoController extends AbstractDashboardController
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param TranslatorInterface    $translator
     * @param AdminUrlGenerator      $router
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $router, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->router->setDashboard(DashboardController::class);
        $this->em = $em;
    }

    /**
     * Enable auto Faq Front Office side or crud manually each FaqBlock we wish to highlight.
     *
     * @Route("/admin/faq-highlighted/auto", name="admin.faq-hightlighted.auto")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function enableAutoFaq(Request $request): Response
    {
        $faqHighlighteds = $this->getFaqHighligtedList($request->getLocale());
        $persist = false;
        $data = [
            'name' => FaqHighlighted::NAME_AUTO,
            'auto' => false,
        ];
        $config = $this->em->getRepository(Config::class)->findOneBy(['name' => $data['name']]);
        if (!$config instanceof Config) {
            $config = new Config();
            $persist = true;
        }
        $config->setName($data['name']);
        if (isset($config->getData()['auto'])) {
            $data['auto'] = $config->getData()['auto'];
        }

        $autoForm = $this->createForm(FaqHighlightedAutoType::class, $data);
        $autoForm->handleRequest($request);

        if ($autoForm->isSubmitted() && $autoForm->isValid()) {
            $formData = $request->request->get('faq_highlighted_auto');
            $auto = isset($formData['faqAuto']);
            $config->setData(['auto' => $auto]);
            if ($persist) {
                $this->em->persist($config);
            }
            $this->em->flush();
        }
        return $this->render('bundles/EasyAdminBundle/_faqHighligtedFormList.html.twig', [
            'autoForm' => $autoForm->createView(),
            'faqItems' => $faqHighlighteds,
            'add'      => $this->router
                ->setController(FaqHighlightedCrudController::class)
                ->setAction(Crud::PAGE_NEW)
                ->generateUrl(),
        ]);
    }

    /**
     * Add URL to edit and remove each object.
     *
     * @param string $locale
     *
     * @return array
     */
    private function getFaqHighligtedList(string $locale)
    {
        $faqHighlighteds = [];
        $rawData = $this->em->getRepository(FaqHighlighted::class)->findAllWithTranslationOrderByWeight();

        /** @var FaqHighlighted $item */
        foreach ($rawData as $item) {
            $faqHighlighted = [
                'faq'       => $item->getFaq(),
                'weight'    => $item->getWeight(),
                'createdAt' => $item->getCreatedAt(),
                'updatedAt' => $item->getUpdatedAt(),
                'createdBy' => $item->getCreatedBy(),
                'updatedBy' => $item->getUpdatedBy(),
                'edit'      => $this->router
                    ->setController(FaqHighlightedCrudController::class)
                    ->setAction(Action::EDIT)
                    ->setEntityId($item->getId())
                    ->generateUrl(),
                'del'       => $this->router
                    ->setController(self::class)
                    ->setRoute('admin.faq-hightlighted.remove', [
                        'objectId' => $item->getId(),
                        '_locale'  => $locale,
                    ])
                    ->generateUrl(),
            ];
            $faqHighlighteds[] = $faqHighlighted;
        }
        return $faqHighlighteds;
    }

    /**
     * Remove a FaqHighlighted object.
     *
     * @Route("/admin/faq-highlighted/remove", name="admin.faq-hightlighted.remove")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request): Response
    {
        $params = $request->query->get('routeParams');

        if (
            !is_array($params) ||
            !isset($params['objectId'])
        ) {
            throw $this->createNotFoundException($this->translator->trans('notfound.faqHighlighted'));
        }

        $faqHighlighted = $this->em->getRepository(FaqHighlighted::class)->findOneBy(['id' => $params['objectId']]);
        if (!$faqHighlighted instanceof FaqHighlighted) {
            throw $this->createNotFoundException($this->translator->trans('notfound.faqHighlighted'));
        }
        $this->em->remove($faqHighlighted);
        $this->em->flush();

        return $this->redirect(
            $this->router
                ->setController(self::class)
                ->setRoute('admin.faq-hightlighted.auto')
                ->generateUrl()
        );
    }
}
