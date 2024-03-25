<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\CategoryRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table(name="category")
 *
 * @RdgAssert\Constraint\CategoryConstraint()
 */
class Category implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 10})
     */
    private $weight;

    /**
     * @var CategoryGuide[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="CategoryGuide",
     *     mappedBy="category",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $guides;

    /**
     * @var CategoryGuideDraft[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="CategoryGuideDraft",
     *     mappedBy="category",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $guideDrafts;

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
        $this->guides = new ArrayCollection();
        $this->guideDrafts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return CategoryGuide[]|ArrayCollection
     */
    public function getGuides()
    {
        return $this->guides;
    }

    public function addGuide(CategoryGuide $guide): self
    {
        if (!$this->guides->contains($guide)) {
            $this->guides[] = $guide;
            $guide->setCategory($this);
        }

        return $this;
    }

    public function removeGuide(CategoryGuide $guide): self
    {
        if ($this->guides->removeElement($guide)) {
            // set the owning side to null (unless already changed)
            if ($guide->getCategory() === $this) {
                $guide->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return CategoryGuideDraft[]|ArrayCollection
     */
    public function getGuideDrafts()
    {
        return $this->guideDrafts;
    }

    public function addGuideDraft(CategoryGuideDraft $guideDraft): self
    {
        if (!$this->guideDrafts->contains($guideDraft)) {
            $this->guideDrafts[] = $guideDraft;
            $guideDraft->setCategory($this);
        }

        return $this;
    }

    public function removeGuideDraft(CategoryGuideDraft $guideDraft): self
    {
        if ($this->guideDrafts->removeElement($guideDraft)) {
            // set the owning side to null (unless already changed)
            if ($guideDraft->getCategory() === $this) {
                $guideDraft->setCategory(null);
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
