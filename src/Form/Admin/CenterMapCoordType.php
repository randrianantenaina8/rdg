<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\CenterMapCoord;
use App\Entity\DataWorkshop;
use App\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CenterMapCoordType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class, [
                'label' => ucfirst($this->translator->trans('centermapcoord.prop.name')),
                'help' => $this->translator->trans('centermapcoord.prop.name.help'),
                'required' => true,
                'trim' => true,
                'attr'       => ['maxlength' => CenterMapCoord::LEN_NAME],
            ])
            ->add('x', TextType::class, [
                'label' => ucfirst($this->translator->trans('centermapcoord.prop.x')),
                'required' => true,
                'trim' => true,
                'attr'       => ['maxlength' => CenterMapCoord::LEN_X],
            ])
            ->add('y', TextType::class, [
                'label' => ucfirst($this->translator->trans('centermapcoord.prop.y')),
                'required' => true,
                'trim' => true,
                'attr'       => ['maxlength' => CenterMapCoord::LEN_Y],
            ])
            ->add('dataworkshop', EntityType::class, [
                'label' => ucfirst($this->translator->trans('centermapcoord.prop.dataworkshop')),
                'help' => $this->translator->trans('centermapcoord.prop.dataworkshop.help'),
                'class' => DataWorkshop::class,
                'placeholder' => '',
                'required' => false,
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
                    'data_class' => CenterMapCoord::class,
                ]
            );
    }
}
