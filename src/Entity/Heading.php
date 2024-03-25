<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\HeadingRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=HeadingRepository::class)
 *
 * @RdgAssert\Constraint\HeadingConstraint()
 */
class Heading implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight = 10;

    /**
     * @var ArrayCollection|HeadingFaq[]
     *
     * @ORM\OneToMany(targetEntity="HeadingFaq", mappedBy="heading", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $faqs;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $createdBy;

    /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", onDelete="SET NULL")
     */
    private $updatedBy;


    public function __construct()
    {
        $this->faqs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return $this
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return HeadingFaq[]|ArrayCollection
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    public function addFaq(HeadingFaq $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setHeading($this);
        }

        return $this;
    }

    public function removeFaq(HeadingFaq $faq): self
    {
        if ($this->faqs->removeElement($faq)) {
            // set the owning side to null (unless already changed)
            if ($faq->getHeading() === $this) {
                $faq->setHeading(null);
            }
        }

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $createdBy
     *
     * @return self
     */
    public function setCreatedBy($createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param $updatedBy
     *
     * @return self
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    /**
     * Magic method used in EasyAdmin BO list to get the translated properties.
     * Ex: when you list actualities, it will give you title, slug and locale.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
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
        return (string) $this->__get('name');
    }
}
