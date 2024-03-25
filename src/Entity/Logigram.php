<?php                                      
                                                     
namespace App\Entity;

use App\Repository\LogigramRepository;
use App\Constraint\LogigramConstraint;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=LogigramRepository::class)
 * @ORM\Table(name="logigram")
 *
 * @RdgAssert\Constraint\LogigramConstraint()
 */
class Logigram implements TranslatableInterface
{
    use TranslatableTrait;
 
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

     /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $updatedBy;

   /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     */
    private $routeType;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     */
    private $targetSlug;

     /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $isPublished;

     /**
     * @var LogigramStep[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LogigramStep", mappedBy="logigram", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    public $logigramSteps;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

     /**
     * @return string
     */
    public function getRouteType()
    {
        return $this->routeType;
    }

    /**
     * @param string|null $routeType
     *
     * @return $this
     */
    public function setRouteType(?string $routeType): self
    {
        $this->routeType = $routeType;

        return $this;
    }

     /**
     * @return string
     */
    public function getTargetSlug()
    {
        return $this->targetSlug;
    }

    /**
     * @param string|null $targetSlug
     *
     * @return $this
     */
    public function setTargetSlug(?string $targetSlug): self
    {
        $this->targetSlug = $targetSlug;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * @return int|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param int|null $updatedBy
     *
     * @return $this
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }


     /**
     * @param bool $isPublished
     *
     * @return $this
     */
    public function setIsPublished($isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return LogigramStep[]|ArrayCollection
     */
    public function getLogigramSteps()
    {
        return $this->logigramSteps;
    }

    public function addLogigramStep(LogigramStep $logigramStep): self
    {
        if( $this->logigramSteps===null ){
            $this->logigramSteps = new ArrayCollection();
        }
        $this->logigramSteps->add($logigramStep) ;
        $logigramStep->setLogigram($this);               
        return $this;
    }

    public function removeLogigramStep(LogigramStep $logigramStep): self
    {
        $this->logigramSteps->removeElement($logigramStep);
        $logigramStep->setLogigram(null) ; 
        
        return $this;
    }

   

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    /**
     * Magic method used in EasyAdmin BO list to get the translated properties.
     * Ex: when you list actualities, it will give you title, slug and locale.
     *
     * @param string $title
     *
     * @return mixed
     */
    public function __get($title)
    {
        $method = 'get' . ucfirst($title);
        $arguments = [];
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * Magic method used when __get is not used.
     *
     * @param string $method
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->__get('title');
    }
}
