<?php                                      
                                                     
namespace App\Entity;

use App\Repository\AdditionalHelpGuideDraftRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Functional entity to link additionalHelp and guideDraft with a weight property.
 *
 * @ORM\Entity(repositoryClass=AdditionalHelpGuideDraftRepository::class)
 */
class AdditionalHelpGuideDraft
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
     * @ORM\Column(type="integer")
     */
    private $weight = 10;

    /**
     * @var GuideDraft
     *
     * @ORM\ManyToOne(targetEntity="GuideDraft", inversedBy="additionalHelps")
     * @ORM\JoinColumn(name="guide_draft_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $guide;

    /**
     * @var AdditionalHelp
     *
     * @ORM\ManyToOne(targetEntity="AdditionalHelp", inversedBy="relatedGuides")
     * @ORM\JoinColumn(name="additional_help_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $additionalHelp;


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
    public function getGuide(): ?Guide
    {
        return $this->guide;
    }

    /**
     * @param GuideDraft|null $guide
     *
     * @return $this
     */
    public function setGuide(?GuideDraft $guide): self
    {
        $this->guide = $guide;

        return $this;
    }

    /**
     * @return AdditionalHelp|null
     */
    public function getAdditionalHelp(): ?AdditionalHelp
    {
        return $this->additionalHelp;
    }

    /**
     * @param AdditionalHelp|null $additionalHelp
     *
     * @return $this
     */
    public function setAdditionalHelp(?AdditionalHelp $additionalHelp): self
    {
        $this->additionalHelp = $additionalHelp;

        return $this;
    }

    public function __toString()
    {
        return 'item';
    }
}
