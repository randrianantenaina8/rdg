<?php                                      
                                                     
namespace App\Form\Admin;

use App\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RouteType extends AbstractType
{
    public const LENGHT_MIN = 2;
    public const LENGHT_MAX = 100;

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
        $translator = $this->translator;
        $router = $this->router;
        $builder
            ->add('name', TextType::class, [
                'data' => $options['data']['name'],
                'mapped' => false,
                'label' => ucfirst($this->translator->trans('config.route.name')),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length('', self::LENGHT_MIN, self::LENGHT_MAX),
                ],
                'help' => $this->translator->trans('route.form.name.help') . ' - ' .
                    $this->translator->trans('bo.length.helper.max', ['%len%' => self::LENGHT_MAX]),
            ])
            ->add('route', TextType::class, [
                'data' => $options['data']['route'],
                'mapped' => false,
                'label' => ucfirst($this->translator->trans('config.route.route')),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length('', self::LENGHT_MIN, self::LENGHT_MAX),
                    new Callback(
                        ['callback' => function ($name, ExecutionContextInterface $context) use ($translator, $router) {
                            try {
                                $router->generate($name);
                            } catch (RouteNotFoundException $e) {
                                $context
                                    ->buildViolation($translator->trans('route.notexist'))
                                    ->addViolation();
                            }
                        }]
                    )
                ],
                'help' => $this->translator->trans('route.form.route.help'),
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('button.save')
            ]);
    }
}
