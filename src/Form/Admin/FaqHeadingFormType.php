<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Heading;
use App\Entity\HeadingFaq;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associate a Heading to a FaqBlock.
 * So, form used FaqBlock side.
 */
class FaqHeadingFormType extends AbstractType
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

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heading', EntityType::class, [
                'label' => ucfirst($this->translator->trans('content.heading')),
                'class' => Heading::class,
                'choices' => $this->em->getRepository(Heading::class)->findByLocaleOrdered(
                    $this->requestStack->getCurrentRequest()->getLocale()
                ),
                'placeholder' => '',
                'required' => true,
            ])
            ->add('weight', IntegerType::class, [
                'label' => ucfirst($this->translator->trans('prop.weight')),
                'empty_data' => 10,
                'required' => true,
                'help' => $this->translator->trans('headingFaq.prop.weight.help'),
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
                    'data_class' => HeadingFaq::class,
                ]
            );
    }
}
