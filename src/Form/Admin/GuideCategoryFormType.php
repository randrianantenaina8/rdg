<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\CategoryGuide;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Associate a Category to a Guide.
 * So, form used Guide side.
 */
class GuideCategoryFormType extends AbstractType
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
            ->add('category', EntityType::class, [
                'label' => ucfirst($this->translator->trans('content.category')),
                'class' => Category::class,
                'choices' => $this->em->getRepository(Category::class)->findByLocaleOrdered(
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
