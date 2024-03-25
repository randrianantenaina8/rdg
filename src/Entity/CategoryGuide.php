<?php                                      
                                                     
namespace App\Entity;

use App\Repository\CategoryGuideRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Functional entity to link category and guide with a weight property.
 *
 * @ORM\Entity(repositoryClass=CategoryGuideRepository::class)
 *
 * @RdgAssert\Constraint\CategoryGuideConstraint()
 */
class CategoryGuide
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"default": 10})
     */
    private $weight;

    /**
     * @var Guide
     *
     * @ORM\ManyToOne(targetEntity="Guide", inversedBy="categories")
     * @ORM\JoinColumn(name="guide_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $guide;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="guides")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $category;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Guide|null
     */
    public function getGuide(): ?Guide
    {
        return $this->guide;
    }

    /**
     * @param Guide|null $guide
     *
     * @return $this
     */
    public function setGuide(?Guide $guide): self
    {
        $this->guide = $guide;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     *
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return 'item';
    }
}
