<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Logigram;
use App\Entity\LogigramStep;
use App\Entity\LogigramNextStep;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class LogigramStepFormType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

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
            ->add('id', HiddenType::class, [])
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('logigram.step.title'),
            ])
            ->add('info', TextType::class, [
                'label' => $this->translator->trans('logigram.step.info'),  
            ])
            ->add('choices', CollectionType::class, [
                'entry_type' => TextType::class, 
                'label' => $this->translator->trans('logigram.step.choices'),  
                'allow_delete' => true,
                'allow_add' => true,
                'prototype' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'attr' => ['class' => 'stepChoice'],
            ])
            ->add('logigramNextSteps', CollectionType::class, [
                'entry_type' => LogigramNextStepFormType::class,
                'label' => $this->translator->trans('logigram.step.nextSteps'),
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true,
                'entry_options' => ['label' => false],
                'attr' => ['class' => 'stepNextStep'],
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
                    'data_class' => LogigramStep::class,
                    'locale' =>  $this->requestStack->getCurrentRequest()->getLocale(),
                    'cascade_validation' => false,
                ]
            );
    }
}
