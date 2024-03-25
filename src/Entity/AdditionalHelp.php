<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\AdditionalHelpRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=AdditionalHelpRepository::class)
 *
 * @RdgAssert\Constraint\AdditionalHelpConstraint()
 */
class AdditionalHelp implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    /**
     * Length link property.
     * Used by custom validator.
     */
    public const LEN_LINK = 510;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $displayed = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight = 10;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=510, nullable=true)
     */
    private $link = null;

    /**
     * @var Guide|null
     *
     * @ORM\ManyToOne(targetEntity=Guide::class)
     * @ORM\JoinColumn(name="guide_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $guide;

    /**
     * @var AdditionalHelpGuide[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AdditionalHelpGuide",
     *     mappedBy="additionalHelp",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $relatedGuides;


    public function __construct()
    {
        $this->relatedGuides = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isDisplayed(): bool
    {
        return $this->displayed;
    }

    /**
     * @param bool $displayed
     *
     * @return $this
     */
    public function setDisplayed(bool $displayed): self
    {
        $this->displayed = $displayed;

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

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     *
     * @return $this
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;

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
     * @return AdditionalHelpGuide[]|ArrayCollection
     */
    public function getRelatedGuides()
    {
        return $this->relatedGuides;
    }

    public function addRelatedGuide(AdditionalHelpGuide $guide): self
    {
        if (!$this->relatedGuides->contains($guide)) {
            $this->relatedGuides[] = $guide;
            $guide->setAdditionalHelp($this);
        }

        return $this;
    }

    public function removeRelatedGuide(AdditionalHelpGuide $guide): self
    {
        if ($this->relatedGuides->removeElement($guide)) {
            // set the owning side to null (unless already changed)
            if ($guide->getAdditionalHelp() === $this) {
                $guide->setAdditionalHelp(null);
            }
        }

        return $this;
    }

    /* ############################################################## */
    /* ############# SPECIAL GETTERS IN CURRENT LOCALE  ############# */
    /* ############################################################## */

    public function getName()
    {
        return $this->proxyCurrentLocaleTranslation('getName');
    }

    public function getDescription()
    {
        return $this->proxyCurrentLocaleTranslation('getDescription');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }
}
