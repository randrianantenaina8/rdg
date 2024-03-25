<?php                                      
                                                     
namespace App\Entity;

use App\Repository\LogigramStepRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=LogigramStepRepository::class)
 * @ORM\Table(name="logigramStep")
 *
 */
class LogigramStep
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
     * @ORM\ManyToOne(targetEntity="Logigram", inversedBy="logigramSteps")
     */
    public $logigram;

     /**
     * @var LogigramNextStep[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LogigramNextStep", mappedBy="logigramStep", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    public $logigramNextSteps;

     /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    public $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    public $info;

     /**
     * @var string[]|Array
     *
     * @ORM\Column(type="array")
     */
    public $choices;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param LogigramNextStep[]|ArrayCollection $logigramNextSteps
     *
     * @return $this
     */
    public function setLogigramNextSteps(ArrayCollection $logigramNextSteps): void
    {
        $this->logigramNextSteps = $logigramNextSteps;
    }

     /**
     * @return LogigramNextStep[]|ArrayCollection
     */
    public function getLogigramNextSteps()
    {
        return $this->logigramNextSteps;
    }

    public function addLogigramNextStep(LogigramNextStep $logigramNextStep): self
    {
        if( $this->logigramNextSteps===null ){
            $this->logigramNextSteps = new ArrayCollection();
        }
        $this->logigramNextSteps->add($logigramNextStep);
        $logigramNextStep->setLogigramStep($this);
        return $this;
    }

    public function removeLogigramNextStep(LogigramNextStep $logigramNextStep): self
    {
        $this->logigramNextSteps->removeElement($logigramNextStep);
        $logigramNextStep->setLogigramStep(null);
        return $this;
    }

     /**
     * @return string|null
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
     * @param Logigram $logigram
     *
     * @return $this
     */
    public function setLogigram($logigram): self
    {
        $this->logigram = $logigram;

        return $this;
    }

      /**
     * @return Logigram
     */
    public function getLogigram()
    {
        return $this->logigram;
    }

    /**
     * @return string[]|ArrayCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     *
     * @return $this
     */
    public function setChoices(array $choices): void
    {
        $this->choices = $choices;
    }

    public function addChoice(string $choice): void
    {
        $this->choices[] = $choice;
    }

    public function removeChoice(string $choice): void
    {
        $key = array_search($choice, $this->choices);

        if ($key !== false) {
            unset($this->choices[$key]);
            $this->choices = array_values($this->choices);
        }
    }
}
