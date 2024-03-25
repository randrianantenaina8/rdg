<?php                                      
                                                     
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Access Controllers to Back-Office.
 */
class SecurityController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Switch language in back-office.
     *
     * @Route(
     *     "/{_locale}/login/switch_ln/{lang}",
     *     name="admin.login.switch_ln",
     *     requirements={"lang" : "%app_locales%"}
     * )
     *
     * @param string  $lang
     * @param Request $request
     *
     * @return Response
     */
    public function changeLocale($lang, Request $request): Response
    {
        $redirect = $this->redirectToRoute('admin.login', ['_locale' => $lang]);
        $redirect->headers->set('referer', $request->headers->get('referer'));
        return $redirect;
    }

    /**
     * Login controller adapted to EasyAdminBundle.
     *
     * @Route("/{_locale}/login", name="admin.login", requirements={"_locale" : "%app_locales%"})
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            // OPTIONAL parameters to customize the login form:

            // the translation_domain to use (define this option only if you are
            // rendering the login template in a regular Symfony controller; when
            // rendering it from an EasyAdmin Dashboard this is automatically set to
            // the same domain as the rest of the Dashboard)
            // 'translation_domain' => 'admin',

            // the title visible above the login form (define this option only if you are
            // rendering the login template in a regular Symfony controller; when rendering
            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
            'page_title' => $this->translator->trans('bo.title'),

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('admin', ['_locale' => $request->getLocale()]),

            // the label displayed for the username form field (the |trans filter is applied to it)
            'username_label' => $this->translator->trans('login.label.username'),

            // the label displayed for the password form field (the |trans filter is applied to it)
            'password_label' => $this->translator->trans('login.label.password'),

            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            'sign_in_label' => $this->translator->trans('login.label.login'),

            // whether to enable or not the "forgot password?" link (default: false)
            'forgot_password_enabled' => true,

            // the path to visit when clicking the "forgot password?" link (default: '#')
            'forgot_password_path' => $this->generateUrl(
                'app_forgot_password_request'
            ),

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            'forgot_password_label' => $this->translator->trans('login.label.forgotpass'),

            // whether to enable or not the "remember me" checkbox (default: false)
            //'remember_me_enabled' => true,

            // whether to check by default the "remember me" checkbox (default: false)
            //'remember_me_checked' => true,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            //'remember_me_label' => $this->translator->trans('login.label.rememberme'),
        ]);
    }

    /**
     * Logout Controller.
     *
     * @Route("/logout", name="admin.logout")
     */
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
