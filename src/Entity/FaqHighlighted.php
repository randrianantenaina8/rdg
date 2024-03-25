<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\FaqHighlightedRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FaqHighlightedRepository::class)
 * @ORM\Table(name="faq_highlighted")
 * @ORM\HasLifecycleCallbacks()
 */
class FaqHighlighted implements TimestampableRdgInterface
{
    use TimestampableRdgTrait;

    /**
     * Key to retrieve a boolean config option to let know to FO if it retrieve last FaqBlock
     * or use this FaqHighlighted items in the Most Frequently Questions block.
     */
    public const NAME_AUTO = 'faq_highlighted_auto';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FaqBlock
     *
     * @ORM\OneToOne(targetEntity="FaqBlock", inversedBy="faqHighlighted")
     * @ORM\JoinColumn(name="faqblock_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $faq;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $weight = 10;

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


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return FaqBlock|null
     */
    public function getFaq(): ?FaqBlock
    {
        return $this->faq;
    }

    /**
     * @param FaqBlock $faq
     *
     * @return $this
     */
    public function setFaq(FaqBlock $faq): self
    {
        $this->faq = $faq;

        return $this;
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
}
