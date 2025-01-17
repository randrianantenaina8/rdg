<?php

namespace App\Controller\Admin;

use App\Entity\ProjectTeamDraft;
use App\Service\ProjectTeamService;
use App\Field\Admin\TranslationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class ProjectTeamDraftCrudController extends AbstractLockableCrudController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ProjectTeamService
     */
    private $projectTeamService;

    /**
     * @var AdminUrlGenerator
     */
    private $router;

    /**
     * @param TranslatorInterface    $translator
     * @param ProjectTeamService     $projectTeamService
     * @param AdminUrlGenerator      $router
     */
    public function __construct(
        TranslatorInterface $translator,
        ProjectTeamService $projectTeamService,
        AdminUrlGenerator $router
    ) {
        $this->translator = $translator;
        $this->projectTeamService = $projectTeamService;
        $this->router = $router;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return ProjectTeamDraft::class;
    }

    /**
     * Add Customize wysiwyg them to forms.
     * Customize pages'name.
     *
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        $member = $this->translator->trans('prop.team.member');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig',
                '@FOSCKEditor/Form/ckeditor_widget.html.twig',
                '@FMElfinder/Form/elfinder_widget.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.draft')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('bo.add')) . ' ' . $member)
            ->setPageTitle(Crud::PAGE_EDIT, ucfirst($this->translator->trans('bo.edit')) . ' ' . $member)
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['name', 'translations.role', 'createdBy.username', 'updatedBy.username']);
    }

    /**
     * Set permissions on CRUD actions.
     * Customize label on create button.
     *
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Publish the draft ie. create a copy as TeamProject object.
        $toPublish = Action::new('toPublish', ucfirst($this->translator->trans('bo.topublish')))
            // Callable method implemented in this class.
            ->linkToCrudAction('publishDraft')
            //  "action-toPublish" is the autogenerated classname if no specify.
            ->setCssClass('action-toPublish btn btn-success');

        $actions = parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_CONTRIB',
                Action::NEW    => 'ROLE_CONTRIB',
                Action::EDIT   => 'ROLE_CONTRIB',
                Action::DELETE => 'ROLE_CONTRIB',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('prop.team.member'))
                );
            })
            ->add(Crud::PAGE_EDIT, $toPublish); // Publish action only if draft has been saved.

        return $actions;
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
            'role'   => [
                'field_type' => TextType::class,
                'required'   => true,
                'label'      => ucfirst($this->translator->trans('content.member.role'))
            ],
            'description' => [
                'field_type' => CKEditorType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('introduction.prop.descr'))
            ],
            'imgLicence'   => [
                'field_type' => TextType::class,
                'required'   => false,
                'label'      => ucfirst($this->translator->trans('prop.img.licence')),
                'label_attr'    => ['columns' => 4]
            ]
        ];

        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields[] = Field::new('image', ucfirst($this->translator->trans('actuality.prop.img')))
                ->setFormType(ElFinderType::class)
                ->setFormTypeOptions([
                    'instance' => 'image',
                    'enable' => true
                ])
                ->setHelp($this->translator->trans('prop.img.help'));

            $fields[] = TextField::new('name', ucfirst($this->translator->trans('content.member.name')))->setColumns('col-sm-12 col-lg-3');

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

            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('Poids')))->setColumns('col-sm-12 col-lg-2');

        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = TextField::new('name', ucfirst($this->translator->trans('content.member.name')));
            $fields[] = ImageField::new('image', ucfirst($this->translator->trans('actuality.prop.img')));
            $fields[] = TextField::new('imgLicence', ucfirst($this->translator->trans('prop.img.licence')));
            $fields[] = IntegerField::new('weight', ucfirst($this->translator->trans('content.member.position')));
            $fields[] = DateTimeField::new('createdAt', ucfirst($this->translator->trans('prop.createdAt')));
            $fields[] = DateTimeField::new('updatedAt', ucfirst($this->translator->trans('prop.updatedAt')));
            $fields[] = TextField::new('createdBy', ucfirst($this->translator->trans('prop.createdBy')))
                ->setSortable(false);
            $fields[] = TextField::new('updatedBy', ucfirst($this->translator->trans('prop.updatedBy')))
                ->setSortable(false);
        }

        return $fields;
    }

    /**
     * Call a Custom service dedicated to publish ie create/update the associated Project Team object.
     *
     * @param AdminContext $adminContext
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function publishDraft(AdminContext $adminContext)
    {
        /** @var ProjectTeamDraft $draft */
        $draft = $adminContext->getEntity()->getInstance();
        $message = $this->translator->trans('content.publish.success');
        try {
            $this->projectTeamService->publish($draft);
            $this->addFlash('success', $message);
        } catch (\Exception $e) {
            $this->addFlash('error', 'erreur');
            throw $e;
        }
        $urlToReturn = $this->router
            ->setController(ProjectTeamCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId($draft->getId())
            ->generateUrl();

        return $this->redirect($urlToReturn);
    }
}
