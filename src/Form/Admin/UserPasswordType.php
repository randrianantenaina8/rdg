<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPasswordType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'label' => $this->translator->trans('profile.label.oldpass'),
            ])
            ->add('newPassword', RepeatedType::class, [
                'mapped' => false,
                'first_options' => [
                    'label' => $this->translator->trans('profile.label.newpass'),
                    'help' => $this->translator->trans('password.pattern.help') . ' - ' .
                        $this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_PASSWORD]),
                ],
                'second_options' => ['label' => $this->translator->trans('profile.label.newpass2')],
                'options' => ['attr' => ['maxlength' => User::LEN_PASSWORD]],
                'invalid_message' => $this->translator->trans('profile.invalid.newpass'),
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Regex(
                        User::PATTERN_PASSWORD,
                        $this->translator->trans('password.pattern')
                    )
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('button.save')
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
