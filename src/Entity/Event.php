<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\EventRepository;
use App\Tool\DateTool;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(name="event")
 * @ORM\HasLifecycleCallbacks()
 *
 * @RdgAssert\Constraint\EventConstraint()
 */
class Event implements TranslatableInterface, TimestampableRdgInterface
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $begin;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * Publication is manually control and can be schedule.
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Taxonomy[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Taxonomy", inversedBy="events")
     * @ORM\JoinTable(name="events_taxonomies")
     */
    private $taxonomies;

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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $intervalle;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $periodicity;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $repetition_end_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number_occurrence;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mass_modification;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $group_id;


    public function __construct()
    {
        $this->taxonomies = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param \DateTime $begin
     *
     * @return $this
     */
    public function setBegin($begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime|null $end
     *
     * @return $this
     */
    public function setEnd(?\DateTime $end): self
    {
        $this->end = $end;

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
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return ($this->publishedAt <= DateTool::datetimeNow());
    }

    /**
     * @return \DateTime|null
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime|null $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Taxonomy[]|ArrayCollection
     */
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }

    /**
     * @param Taxonomy $taxonomy
     *
     * @return $this
     */
    public function addTaxonomy(Taxonomy $taxonomy)
    {
        if (!$this->taxonomies->contains($taxonomy)) {
            $this->taxonomies->add($taxonomy);
        }
        return $this;
    }

    /**
     * @param Taxonomy $taxonomy
     *
     * @return $this
     */
    public function removeTaxonomy(Taxonomy $taxonomy)
    {
        if ($this->taxonomies->contains($taxonomy)) {
            $this->taxonomies->removeElement($taxonomy);
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

    public function getIntervalle(): ?int
    {
        return $this->intervalle;
    }

    public function setIntervalle(?int $intervalle): self
    {
        $this->intervalle = $intervalle;

        return $this;
    }

    public function getPeriodicity(): ?string
    {
        return $this->periodicity;
    }

    public function setPeriodicity(?string $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getRepetitionEndDate(): ?\DateTimeInterface
    {
        return $this->repetition_end_date;
    }

    public function setRepetitionEndDate(?\DateTimeInterface $repetition_end_date): self
    {
        $this->repetition_end_date = $repetition_end_date;

        return $this;
    }

    public function getNumberOccurrence(): ?int
    {
        return $this->number_occurrence;
    }

    public function setNumberOccurrence(?int $number_occurrence): self
    {
        $this->number_occurrence = $number_occurrence;

        return $this;
    }

    public function getMassModification(): ?bool
    {
        return $this->mass_modification;
    }

    public function setMassModification(?bool $mass_modification): self
    {
        $this->mass_modification = $mass_modification;

        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->group_id;
    }

    public function setGroupId(?string $group_id): self
    {
        $this->group_id = $group_id;

        return $this;
    }

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    public function getTitle()
    {
        return $this->proxyCurrentLocaleTranslation('getTitle');
    }

    public function getHook()
    {
        return $this->proxyCurrentLocaleTranslation('getHook');
    }

    public function getContent()
    {
        return $this->proxyCurrentLocaleTranslation('getContent');
    }

    public function getSlug()
    {
        return $this->proxyCurrentLocaleTranslation('getSlug');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $title = (string) $this->getTitle();

        if (empty($title)) {
            switch ($this->getCurrentLocale()) {
                case 'fr':
                    $title = $this->id . ' - Non traduit en français';
                    break;
                case 'en':
                    $title = $this->id . ' - Not translated in english';
                    break;
                default:
                    $title = $this->id . ' - Not translated at all';
                    break;
            }
        }

        return $title;
    }
}
