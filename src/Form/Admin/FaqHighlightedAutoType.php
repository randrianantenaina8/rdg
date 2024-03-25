<?php                                      
                                                     
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FaqHighlightedAutoType extends AbstractType
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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('faqAuto', CheckboxType::class, [
                'data' => $options['data']['auto'],
                'mapped' => false,
                'label' => ucfirst($this->translator->trans('config.faq_highlighted.auto')),
                'help' => $this->translator->trans('config.faq_highlighted.auto.help'),
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('button.save')
            ]);
    }
}
