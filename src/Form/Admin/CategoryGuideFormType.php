<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\CategoryGuide;
use App\Entity\Guide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associate a guide to a Category.
 * So, form used Category side.
 */
class CategoryGuideFormType extends AbstractType
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
            ->add('guide', EntityType::class, [
                'label' => ucfirst($this->translator->trans('content.guide')),
                'class' => Guide::class,
                'choices' => $this->em->getRepository(Guide::class)->findAllByLocaleOrdered(
                    $this->requestStack->getCurrentRequest()->getLocale()
                ),
                'placeholder' => '',
                'required' => true,
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
                    'data_class' => CategoryGuide::class,
                ]
            );
    }
}
