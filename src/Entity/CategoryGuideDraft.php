<?php                                      
                                                     
namespace App\Entity;

use App\Repository\CategoryGuideDraftRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Functional entity to link category and guideDraft with a weight property.
 * It must to get same properties' and methods' name than CategeoryGuide to use same Custom Validator.
 *
 * @ORM\Entity(repositoryClass=CategoryGuideDraftRepository::class)
 *
 * @RdgAssert\Constraint\CategoryGuideConstraint()
 */
class CategoryGuideDraft
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default": 10})
     */
    private $weight;

    /**
     * @var GuideDraft
     *
     * @ORM\ManyToOne(targetEntity="GuideDraft", inversedBy="categories")
     * @ORM\JoinColumn(name="guide_draft_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $guide;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="guideDrafts")
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
     * @return GuideDraft|null
     */
    public function getGuide(): ?GuideDraft
    {
        return $this->guide;
    }

    /**
     * @param GuideDraft|null $guideDraft
     *
     * @return $this
     */
    public function setGuide(?GuideDraft $guideDraft): self
    {
        $this->guide = $guideDraft;

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
