<?php                                      
                                                     
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserPasswordType;
use App\Form\Admin\UserType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}", requirements={"_locale" : "%app_locales%"})
 *
 * @IsGranted("ROLE_CONTRIB")
 */
class UserController extends AbstractDashboardController
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var AdminUrlGenerator
     */
    protected $router;

    /**
     * @param TranslatorInterface $translator
     * @param Security            $security
     * @param AdminUrlGenerator   $router
     */
    public function __construct(TranslatorInterface $translator, Security $security, AdminUrlGenerator $router)
    {
        $this->translator = $translator;
        $this->security = $security;
        $this->router = $router;
    }

    /**
     * @Route("/admin/profile", name="admin.profile")
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $em
     * @param UserPasswordHasherInterface $passwordHasher
     *
     * @return Response
     */
    public function editProfile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $userId = $this->security->getUser()->getId();
        $user = $em->getRepository(User::class)->find($userId);
        $formProfile = $this->createForm(UserType::class, $user);
        $formPassword = $this->createForm(UserPasswordType::class, $user);
        $redirectUrl = $this->router
            ->setController(self::class)
            ->setRoute('admin.profile')
            ->generateUrl();

        $formProfile->handleRequest($request);
        if (
            $formProfile->isSubmitted() &&
            $formProfile->isValid() &&
            $this->isCsrfTokenValid('profile' . $userId, $request->get('_token'))
        ) {
            $em->flush();

            return $this->redirect($redirectUrl);
        }
        $formPassword->handleRequest($request);
        if (
            $formPassword->isSubmitted() &&
            $formPassword->isValid() &&
            $this->isCsrfTokenValid('password' . $userId, $request->get('_token'))
        ) {
            $formData = $request->request->get('user_password');

            if ($passwordHasher->isPasswordValid($user, $formData['oldPassword'])) {
                $newPassword = $passwordHasher->hashPassword($user, $formData['newPassword']['first']);
                $user->setPassword($newPassword);
                $em->flush();

                $this->addFlash('success', $this->translator->trans('flash.success.password.created'));
                return $this->redirect($redirectUrl);
            }
            $formPassword->addError(new FormError($this->translator->trans('flash.error.oldpassword.incorrect')));
        }

        return $this->render('bundles/EasyAdminBundle/_userProfile.html.twig', [
            'formProfile' => $formProfile->createView(),
            'formPass' => $formPassword->createView(),
            'user' => $user,
        ]);
    }
}
