<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\Actuality;
use App\Entity\Event;
use App\Entity\Lame\Lame;
use App\Entity\Lame\NewsLame;
use App\Field\Admin\TranslationField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
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
class NewsLameCrudController extends AbstractLockableCrudController
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
        return NewsLame::class;
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
        $news = $this->translator->trans('content.newsLame');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
            ])
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $news)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $news);
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        $locale = $this->getContext()->getRequest()->getLocale();
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
            $fields[] = TranslationField::new('translations', '', $fieldsConfig)
                ->setLabel(false)
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

            $fields[] = FormField::addPanel($this->translator->trans('prop.group.published'))
                ->setHelp($this->translator->trans('prop.group.published.help'));
            $fields[] = BooleanField::new('isPublished', ucfirst($this->translator->trans('prop.published')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('prop.weight')))
                ->setHelp($this->translator->trans('prop.weight.help'));

            $fields[] = FormField::addPanel($this->translator->trans('newsLame.group.actualities'))
                ->setHelp($this->translator->trans('newsLame.group.actualities.help'));
            $fields[] = BooleanField::new('autoActu', ucfirst($this->translator->trans('newsLame.prop.autoActu')))
                ->setHelp($this->translator->trans('newsLame.prop.autoActu.help'));
            $fields[] = AssociationField::new('actuFirst', ucfirst($this->translator->trans('newsLame.prop.actu1')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Actuality::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('actuSecond', ucfirst($this->translator->trans('newsLame.prop.actu2')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Actuality::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('actuThird', ucfirst($this->translator->trans('newsLame.prop.actu3')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Actuality::class)->findLastPublishedByLocale($locale),
                ]);
            $fields[] = AssociationField::new('actuFourth', ucfirst($this->translator->trans('newsLame.prop.actu4')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Actuality::class)->findLastPublishedByLocale($locale),
                ]);

            $fields[] = FormField::addPanel($this->translator->trans('newsLame.group.events'))
                ->setHelp($this->translator->trans('newsLame.group.events.help'));
            $fields[] = BooleanField::new('autoEvent', ucfirst($this->translator->trans('newsLame.prop.autoEvent')))
                ->setHelp($this->translator->trans('newsLame.prop.autoEvent.help'));
            $fields[] = AssociationField::new('eventFirst', ucfirst($this->translator->trans('newsLame.prop.event1')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Event::class)->findNextPublished($locale),
                ]);
            $fields[] = AssociationField::new('eventSecond', ucfirst($this->translator->trans('newsLame.prop.event2')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Event::class)->findNextPublished($locale),
                ]);
            $fields[] = AssociationField::new('eventThird', ucfirst($this->translator->trans('newsLame.prop.event3')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Event::class)->findNextPublished($locale),
                ]);
            $fields[] = AssociationField::new('eventFourth', ucfirst($this->translator->trans('newsLame.prop.event4')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Event::class)->findNextPublished($locale),
                ]);
            $fields[] = AssociationField::new('eventFifth', ucfirst($this->translator->trans('newsLame.prop.event5')))
                ->setFormTypeOptions([
                    'choices' => $this->em->getRepository(Event::class)->findNextPublished($locale),
                ]);

        }
        return $fields;
    }
}
