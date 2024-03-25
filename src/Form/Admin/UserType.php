<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form used to EDIT user.
 */
class UserType extends AbstractType
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
            ->add('username', TextType::class, [
                'label' => $this->translator->trans('profile.label.username'),
                'required' => true,
                'help' => $this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_USERNAME]),
                'attr' => ['maxlength' => User::LEN_USERNAME],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('profile.label.email'),
                'required' => true,
                'help' => $this->translator->trans('bo.length.helper.max', ['%len%' => User::LEN_EMAIL]),
                'attr' => ['maxlength' => User::LEN_EMAIL],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => $this->translator->trans('profile.label.roles'),
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'disabled' => true,
                'choices' => $this->getRoles(),
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
            'data_class'        => User::class,
            'validation_groups' => 'edit',
        ]);
    }

    /**
     * @return array
     */
    protected function getRoles()
    {
        $roles = [];

        foreach (User::ROLES as $label => $role) {
            $roles[$this->translator->trans($label)] = $role;
        }
        return $roles;
    }
}
