<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Lame\Lame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class SelectLameType extends AbstractType
{
    public const LENGHT_MIN = 1;
    public const LENGHT_MAX = 255;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;


    /**
     * @param TranslatorInterface   $translator
     * @param UrlGeneratorInterface $router
     */
    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => $this->translator->trans('lame.prop.type.placeholder'),
                'label' => ucfirst($this->translator->trans('form.lame.add')),
                'required' => true,
                'choices' => $this->getChoices()
            ])
        ;
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $choices = [];

        foreach (Lame::TYPE as $label => $class) {
            $choices[$this->translator->trans($label)] = $class;
        }
        return $choices;
    }
}
