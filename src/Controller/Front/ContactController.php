<?php                                      
                                                     
namespace App\Controller\Front;

use App\Entity\Subject;
use App\Entity\SubjectTranslation;
use App\Form\Front\ContactSubjectType;
use App\Service\FooterService;
use App\Service\HeaderService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Front controller to manage contact forms.
 */
class ContactController extends AbstractController
{
    /**
     * Default subject to use if none has been chosen.
     *
     */
    public const NONE_SUBJECT = "Message RDG Contact";

    /**
     * To get links to Page entities and SocialNetwork entities in footer.
     *
     * @var FooterService
     */
    protected FooterService $footerService;

    /**
     * @var HeaderService
     */
    protected HeaderService $headerService;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param FooterService       $footerService
     * @param HeaderService       $headerService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FooterService $footerService,
        HeaderService $headerService,
        TranslatorInterface $translator
    ) {
        $this->footerService = $footerService;
        $this->headerService = $headerService;
        $this->translator = $translator;
    }

    /**
     * Get data from Contact Form then send it.
     *
     * @Route({
     *     "fr" : "/fr/contact",
     *     "en" : "/en/contact"
     * }, name="front.contact")
     *
     * @param Request         $request
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer, LoggerInterface $logger, EntityManagerInterface $em): Response
    {
        $locale = $request->getLocale();
        $form = $this->createForm(ContactSubjectType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            if (isset($contactFormData['ref']) && !empty($contactFormData['ref'])) {
                $logger->alert(
                    sprintf(
                        'Spam tried with %s from %s',
                        $contactFormData['email'],
                        $request->getClientIp()
                    )
                );

                return $this->returnForm($form, $locale);
            }
            $subject = (string) $contactFormData['subject'];
            $recipients = $this->getRecipients($request, $subject, $em);
            if (empty($subject)) {
                $subject = self::NONE_SUBJECT;
            }

            $text = 'Un message a été envoyé de ' . $contactFormData['email']
                . PHP_EOL . PHP_EOL .
                    $this->translator->trans('contact.label.other') . ':' . PHP_EOL . $contactFormData['other'] 
                . PHP_EOL . PHP_EOL . 'Votre message :' . PHP_EOL . $contactFormData['message'];
            $message = (new Email())
                ->replyTo($contactFormData['email'])
                ->from($this->getParameter('email_from'))
                //->to($this->getParameter('email_to'))
                ->to(...$recipients)
                ->subject($subject)
                ->text($text, 'text/plain');
            try {
                $mailer->send($message);
                $this->addFlash('success', $this->translator->trans('contact.sending.success'));
            } catch (\Exception $e) {
                $logger->critical('Issue with email sending...' . PHP_EOL . $e->getMessage());
                $this->addFlash('error', $this->translator->trans('contact.sending.error'));
            }

            return $this->redirectToRoute('front.contact');
        }

        return $this->returnForm($form, $locale);
    }

    /**
     * @param FormInterface $form
     * @param string        $locale
     *
     * @return Response
     */
    private function returnForm(Forminterface $form, string $locale): Response
    {
        return $this->render('contact.html.twig', [
            'contactForm' => $form->createView(),
            'breadcrumbs' => $this->headerService->generateBreadcrumbs(
                'contact',
                $locale
            ),
            'introBanner' => $this->headerService->getIntroBanner('front.contact', $locale),
            'headerDatas' => $this->headerService->getMainMenu($locale),
            'footerDatas' => $this->footerService->getLinksAndNetworks($locale),
            'switcherLng' => $this->headerService->getSwitcherSystem('front.contact'),
        ]);
    }

    private function getRecipients($request, $subject, $em) {
        $locale = $request->getLocale();
        $repository = $em->getRepository(SubjectTranslation::class);
        $subjectTranslation = $repository->findOneBy(['subject' => $subject]);
        $oSubject = $subjectTranslation->getTranslatable();
        $recipients = $oSubject->getRecipients();
        $tRecipient = [];
        foreach($recipients as $recipient){
            $tRecipient[] = $recipient->getEmail();
        }
        return $tRecipient;
    }
}
