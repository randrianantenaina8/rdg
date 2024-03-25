<?php                                      
                                                     
namespace App\Entity;

use App\Repository\LogigramNextStepRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogigramNextStepRepository::class)
 * @ORM\Table(name="logigramNextStep")
 *
 */
class LogigramNextStep 
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="LogigramStep", inversedBy="logigramNextSteps",cascade={"persist"})
     */
    public $logigramStep;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    public $info;

     /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    public $nextStep;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param LogigramStep $logigramStep
     *
     * @return $this
     */
    public function setLogigramStep($logigramStep): self
    {
        $this->logigramStep = $logigramStep;
        return $this;
    }

    /**
     * @return LogigramStep
     */
    public function getLogigramStep()
    {
        return $this->logigramStep;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     *
     * @return $this
     */
    public function setInfo($info): self
    {
        $this->info = $info;

        return $this;
    }

     /**
     * @return int
     */
    public function getNextStep()
    {
        return $this->nextStep;
    }

    /**
     * @param int $nextStep
     *
     * @return $this
     */
    public function setNextStep($nextStep): self
    {
        $this->nextStep = $nextStep;

        return $this;
    }
}
