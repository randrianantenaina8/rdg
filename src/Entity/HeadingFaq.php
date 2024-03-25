<?php                                      
                                                     
namespace App\Entity;

use App\Repository\HeadingFaqRepository;
use App\Validator as RdgAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Functional entity to link Heading and Faq with a weight property.
 *
 * @ORM\Entity(repositoryClass=HeadingFaqRepository::class)
 *
 * @RdgAssert\Constraint\HeadingFaqConstraint()
 */
class HeadingFaq
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $weight = 10;

    /**
     * @var FaqBlock
     *
     * @ORM\ManyToOne(targetEntity="FaqBlock", inversedBy="headings")
     * @ORM\JoinColumn(name="faq_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $faq;

    /**
     * @var Heading
     *
     * @ORM\ManyToOne(targetEntity="Heading", inversedBy="faqs")
     * @ORM\JoinColumn(name="heading_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $heading;

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
     * @return FaqBlock|null
     */
    public function getFaq(): ?FaqBlock
    {
        return $this->faq;
    }

    /**
     * @param FaqBlock|null $faq
     *
     * @return $this
     */
    public function setFaq(?FaqBlock $faq): self
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * @return Heading|null
     */
    public function getHeading(): ?Heading
    {
        return $this->heading;
    }

    /**
     * @param Heading|null $heading
     *
     * @return $this
     */
    public function setHeading(?Heading $heading): self
    {
        $this->heading = $heading;

        return $this;
    }

    public function __toString()
    {
        return 'item';
    }
}
