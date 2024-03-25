<?php                                      
                                                     
namespace App\Controller\Admin;

use DateTimeZone;
use App\Entity\S3File;
use App\Entity\S3FileCategory;
use App\Form\Admin\S3FileEditType;
use Aws\S3\S3ClientInterface;
use Aws\Exception\AwsException;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\Security\Core\Security;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;

/**
 * @IsGranted("ROLE_CONTRIB")
 */
class S3FileCrudController extends AbstractCrudController
{
    use TimestampableRdgTrait;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var S3File
     */
    protected $s3File;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AdminUrlGenerator
     */
    protected $router;

    /**
     * @var S3ClientInterface $s3Client
     */
    public $s3Client;

    /**
     * @var Security $security
     */
    protected $security;

    /**
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        TranslatorInterface    $translator,
        EntityManagerInterface $em,
        AdminUrlGenerator      $router,
        S3ClientInterface      $s3Client,
        Security               $security
    ) {
        $this->translator = $translator;
        $this->em = $em;
        $this->router = $router;
        $this->s3Client = $s3Client;
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return S3File::class;
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
        $mediaLibrary = $this->translator->trans('content.media.library');

        return $crud
            ->setFormThemes([
                'bundles/a2lix/admin_translations_field.html.twig',
                '@EasyAdmin/crud/form_theme.html.twig'
            ])
            ->setPageTitle(Crud::PAGE_INDEX, ucfirst($this->translator->trans('content.media.library')))
            ->setPageTitle(Crud::PAGE_NEW, ucfirst($this->translator->trans('content.media.library.file')))
            ->setDefaultSort(['s3FileCategory.name' => 'ASC'])
            ->setPaginatorUseOutputWalkers(true)
            ->setSearchFields(['s3FileCategory.name', 'createdBy.username', 'updatedBy.username']);
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
        // Edit S3 File Category
        $edit = Action::new('edit')->linkToCrudAction('edit');

        return parent::configureActions($actions)
            ->setPermissions([
                Action::INDEX  => 'ROLE_COORD',
                Action::NEW    => 'ROLE_COORD',
                Action::EDIT   => 'ROLE_COORD',
                Action::DELETE => 'ROLE_COORD',
            ])
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel(
                    ucfirst($this->translator->trans('bo.add')) .
                    ' ' .
                    ucfirst($this->translator->trans('prop.file'))
                );
            })
            ->add(Crud::PAGE_EDIT, $edit); // Set S3 File edit action
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = AssociationField::new('s3FileCategory', ucfirst($this->translator->trans('bo.media.category.select')))
                ->setRequired(true);
            $fields[] = TextField::new('imageFile', ucfirst($this->translator->trans('prop.file.upload')))
                ->setFormType(VichImageType::class)
                ->setRequired(true)
                ->onlyOnForms()
                ->setHelp(ucfirst($this->translator->trans('media.library.file.maxsize.help')));
        } elseif ($pageName === Crud::PAGE_INDEX) {
            $fields[] = ImageField::new('imageName', ucfirst($this->translator->trans('prop.file')))
                ->setTemplatePath('/image.html.twig');
            $fields[] = AssociationField::new('s3FileCategory', ucfirst($this->translator->trans('bo.media.category')))
                ->setSortable(false);
            $fields[] = IntegerField::new('imageSize', ucfirst($this->translator->trans('prop.weight') .' '. '(en Ko)'));
            $fields[] = TextField::new('mimeType', ucfirst($this->translator->trans('prop.mime.type')));
            $fields[] = TextField::new('originalName', ucfirst($this->translator->trans('prop.origin.name')));
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
     * Custom Edit CRUD Action
     *
     * @param AdminContext $context
     * @return Response
     */
    public function edit(AdminContext $context): Response
    {
        // Get current S3 file
        $s3File = $context->getEntity()->getInstance();
        $currentCategory = $s3File->getS3FileCategory()->name;
        $categories = $this->em->getRepository(S3FileCategory::class)->findAll();

        // Retrieve custom Form
        $form = $this->createForm(S3FileEditType::class, $s3File, [
            'categories' => $categories
        ]);

        $form->handleRequest($context->getRequest());
        
        if ($form->isSubmitted() && $form->isValid()) {

            $newCategory = $form->get('s3FileCategory')->getData();
            $s3File->setS3FileCategory($newCategory);
            
            // Parameters for the move operation
            $sourcePath = $currentCategory .'/'. $s3File->getImageName();
            $destinationPath = $newCategory . '/' . $s3File->getImageName();
            $bucket = $this->getParameter('bucket_name');

            try {
                // Perform AWS copy and delete operations
                $this->s3Client->copyObject([
                    'Bucket'     => $bucket,
                    'CopySource' => $bucket .'/'. $sourcePath,
                    'Key'        => $destinationPath,
                ]);

                $this->s3Client->deleteObject([
                    'Bucket' => $bucket,
                    'Key'    => $sourcePath,
                ]);

                // Set updated User
                $user = $this->security->getUser();
                $s3File->setUpdatedBy($user);
                // Set updated DateTime
                $s3File->setUpdatedAt(new \DateTime('now', new DateTimeZone('Europe/Paris')));
                // Persist data into the database
                $this->em->persist($s3File);
                $this->em->flush();

                // Launch success message
                $this->addFlash('success', ucfirst($this->translator->trans('bo.media.category.updated')));

                // Redirect back to the index page
                $listRedirection = $this->router
                    ->setController(S3FileCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl();
    
                return $this->redirect($listRedirection);

            } catch (AwsException $e) {
                // Handle exceptions
                $this->addFlash('error', ucfirst($this->translator->trans('bo.media.category.update.fail')) 
                . $e->getMessage());
            }
        }

        // Render the custom template with the form
        return $this->render('/bundles/EasyAdminBundle/_edit_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('s3FileCategory', ucfirst($this->translator->trans('bo.media.category'))))
            ->add(TextFilter::new('originalName', ucfirst($this->translator->trans('prop.origin.name'))))
            ->add(TextFilter::new('mimeType', ucfirst($this->translator->trans('bo.media.type.filter'))))
        ;
    }

    // Adds the CSS and JS assets associated to the given Webpack Encore entry
    // use these generic methods to add any code before </head> or </body>
    // the contents are included "as is" in the rendered page (without escaping them)
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('custom')
            ->addJsFile('build/custom.js')
        ;
    }
}
