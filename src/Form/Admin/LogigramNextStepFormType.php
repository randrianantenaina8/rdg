<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Logigram;
use App\Entity\LogigramNextStep;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LogigramNextStepFormType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('id', HiddenType::class,  [
        ])
        ->add('title', TextType::class, 
        [
            'label' => $this->translator->trans('logigram.nextStep.title'),
        ])
        ->add('info', TextType::class,
            [
            'label' => $this->translator->trans('logigram.nextStep.info'),  
        ])
        ->add('nextStep', IntegerType::class, 
           [
            'label' =>  $this->translator->trans('logigram.nextStep.nextStep'), 
            'help' => $this->translator->trans('logigram.nextStep.help'),
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
                    'data_class' => LogigramNextStep::class,
                    'locale' =>  $this->requestStack->getCurrentRequest()->getLocale(),
                    'cascade_validation' => false,
                ]
            );
    }
}
