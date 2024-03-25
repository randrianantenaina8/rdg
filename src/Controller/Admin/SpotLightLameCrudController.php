<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Dataset;
use App\Entity\Lame\Lame;
use App\Entity\Lame\SpotLightLame;
use App\Field\Admin\TranslationField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", requirements={"_locale" : "%app_locales%"})
 *
 * @IsGranted("ROLE_COORD")
 */
class SpotLightLameCrudController extends AbstractLockableCrudController
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
    protected $em;


    /**
     * @param TranslatorInterface    $translator
     * @param AdminUrlGenerator      $router
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $router, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return SpotLightLame::class;
    }

    /**
     * Redirect to LameController::list after SAVE_AND_RETURN action.
     *
     * @param AdminContext $context
     * @param string       $action
     *
     * @return RedirectResponse
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];

        if (Action::SAVE_AND_RETURN === $submitButtonName) {
            $url = $context->getReferrer()
                ?? $this->container->get(AdminUrlGenerator::class)->setRoute('admin.lame.list')->generateUrl();

            return $this->redirect($url);
        }
        return parent::getRedirectResponseAfterSave($context, $action);
    }

    /**
     * Add redirect action to LameController list after create/edit.
     * Add restricted permissions.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        $lameList = Action::new('lameList', $this->translator->trans('bo.lame.back'))
            ->linkToRoute('admin.lame.list');

        return parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_ADMIN',
                Action::NEW    => 'ROLE_COORD',
                Action::EDIT   => 'ROLE_COORD',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->remove(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, $lameList)
            ->add(Crud::PAGE_EDIT, $lameList);
    }

    /**
     * Add translation field them.
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $spotlight = $this->translator->trans('content.spotlight');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $spotlight)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $spotlight);
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        $fieldsConfig = [
            'title' => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('lame.prop.title')),
                'attr'       => ['maxlength' => Lame::LEN_TITLE],
                'help'       => $this->translator->trans('bo.length.helper.max', ['%len%' => Lame::LEN_TITLE]),
            ],
        ];

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = FormField::addPanel(ucfirst($this->translator->trans('prop.translations')))
            ->setHelp($this->translator->trans('prop.translations.help.one'));
            $fields[] = BooleanField::new(
                'isPublished',
                ucfirst($this->translator->trans('prop.published'))
            );
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')))
                ->setHelp($this->translator->trans('prop.weight.help'));
            $fields[] = TranslationField::new(
                'translations',
                ucfirst($this->translator->trans('prop.translations')),
                $fieldsConfig
            )
                ->setRequired(true)
                ->setFormTypeOptions(
                    [
                        'attr' => [
                            'helper' => [
                                $this->translator->trans('prop.translations.help.one'),
                            ]
                        ]
                    ]
                );

            $locale = $this->getContext()->getRequest()->getLocale();

            // Last 6 Datasets select form option
            $fields[] = FormField::addPanel($this->translator->trans('spotlightLame.group.spotlights'))
                ->setHelp($this->translator->trans('spotlightLame.group.spotlights.help'));
            $fields[] = BooleanField::new('autoDataset', ucfirst($this->translator->trans('spotlightLame.prop.autoSpotlight')))
                ->setHelp($this->translator->trans('spotlightLame.prop.autoSpotlight.help'));
            
            // Custom Datasets select form option
            $fields[] = FormField::addPanel($this->translator->trans('spotlightLame.group.spotlights2'))
                ->setHelp($this->translator->trans('spotlightLame.group.spotlights.help2'));
            $fields[] = AssociationField::new('datasetFirst', ucfirst($this->translator->trans('spotlightLame.prop.spotlight1')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale)
                ]);
            $fields[] = AssociationField::new('datasetSecond', ucfirst($this->translator->trans('spotlightLame.prop.spotlight2')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('datasetThird', ucfirst($this->translator->trans('spotlightLame.prop.spotlight3')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('datasetFourth', ucfirst($this->translator->trans('spotlightLame.prop.spotlight4')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('datasetFifth', ucfirst($this->translator->trans('spotlightLame.prop.spotlight5')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('datasetSixth', ucfirst($this->translator->trans('spotlightLame.prop.spotlight6')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Dataset::class)->findLastPublishedByLocale($locale),
            ]);
        }
        return $fields;
    }
}
