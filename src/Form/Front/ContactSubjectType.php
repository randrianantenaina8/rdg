<?php                                      
                                                     
namespace App\Form\Front;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactSubjectType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * variable temporaire vouée à finir dans un fichier de configuration de l'appli
     * enum ['captcha','honeypot']
     * @const string $captchatype;
     */
    protected const CAPTCHA_TYPE = 'honeypot';

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->locale = $requestStack->getCurrentRequest()->getLocale();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('contact.label.email'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('subject', ChoiceType::class, [
                'label' => $this->translator->trans('contact.label.subject'),
                'placeholder' => $this->translator->trans('contact.placeholder.subject'),
                'required' => true,
                'empty_data' => $this->translator->trans('contact.placeholder.subject'),
                'choices' => $this->getSubjects(),
            ])
            ->add('message', TextareaType::class, [
                'label' => $this->translator->trans('contact.label.message'),
                'attr' => ['row' => 10],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('other', TextType::class, [
                'label' => $this->translator->trans('contact.label.other'),
                'required' => false,
                'mapped' => true
            ]);
        ;

        if (self::CAPTCHA_TYPE === 'captcha') {
            $builder->add('captcha', CaptchaType::class, [
                'reload' => true,
                'as_url' => true,
                'invalid_message' => $this->translator->trans('contact.captcha.error'),
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ]);
        } elseif (self::CAPTCHA_TYPE === 'honeypot') {
            $builder->add('ref', TextType::class, [
                'required' => false
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    /**
     * Get subjects from database to populate the choices list.
     *
     * @return array
     */
    protected function getSubjects()
    {
        $subjects = [];

        $contactSubjects = $this->em->getRepository(Subject::class)->findOrderByWeight($this->locale);

        foreach ($contactSubjects as $contactSubject) {
            if (isset($contactSubject['subject'])) {
                $subjects[$contactSubject['subject']] = $contactSubject['subject'];
            }
        }

        return $subjects;
    }
}
