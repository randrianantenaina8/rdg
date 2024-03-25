<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\AdditionalHelp;
use App\Entity\AdditionalHelpGuide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class GuideAdditionalHelpFormType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     * @param RequestStack           $requestStack
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('additionalHelp', EntityType::class, [
                'label' => ucfirst($this->translator->trans('content.additionalHelp')),
                'class' => AdditionalHelp::class,
                'choices' => $this->em->getRepository(AdditionalHelp::class)->findAll(),
                'placeholder' => '',
                'required' => true,
                'help' => $this->translator->trans('guideAdditionalHelp.prop.additionalHelp.help')
            ])
            ->add('weight', IntegerType::class, [
                'label' => ucfirst($this->translator->trans('prop.weight')),
                'empty_data' => 10,
                'required' => true,
                'help' => $this->translator->trans('prop.weight.help'),
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => AdditionalHelpGuide::class,
                ]
            );
    }
}
