<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\ActualityDraftRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ActualityDraftRepository::class)
 * @ORM\Table(name="actuality_draft")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 *
 * @RdgAssert\Constraint\ActualityConstraint()
 */
class ActualityDraft implements TranslatableInterface, TimestampableRdgInterface
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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="actu_images", fileNameProperty="imageLocale")
     *
     * @Assert\File(
     *     maxSize="20M",
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg", "image/x-png", "image/gif"}
     * )
     */
    private $imageFile;

    /**
     * @var Taxonomy[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Taxonomy", inversedBy="actualities")
     * @ORM\JoinTable(name="actualities_draft_taxonomies")
     */
    private $taxonomies;

    /**
     * @var Dataset[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dataset", mappedBy="actuality")
     */
    private $datasets;

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
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="Actuality")
     * @ORM\JoinColumn(name="actuality_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuality;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;


    public function __construct()
    {
        $this->taxonomies = new ArrayCollection();
        $this->datasets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param $image
     *
     * @return $this
     */
    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param $imageFile
     *
     * @return $this
     */
    public function setImageFile($imageFile): self
    {
        $this->imageFile = $imageFile;

        // To force doctrine something changed on this entity.
        if ($imageFile) {
            $this->updatedAt = new \DateTime();
        }
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

    /**
     * @return Dataset[]|ArrayCollection
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
     * @param Dataset $dataset
     *
     * @return $this
     */
    public function addDataset(Dataset $dataset): self
    {
        if (!$this->datasets->contains($dataset)) {
            $this->datasets->add($dataset);
        }

        return $this;
    }

    public function removeDataset(Dataset $dataset): self
    {
        if ($this->datasets->contains($dataset)) {
            $this->datasets->removeElement($dataset);
            // Set the owning to null (unless already changed)
            if ($dataset->getActuality() === $this) {
                $dataset->setActuality(null);
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

    /**
     * @return Actuality|null
     */
    public function getActuality(): ?Actuality
    {
        return $this->actuality;
    }

    /**
     * @param Actuality|null $actuality
     *
     * @return $this
     */
    public function setActuality(?Actuality $actuality): self
    {
        $this->actuality = $actuality;

        return $this;
    }

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    public function getTitle()
    {
        return $this->proxyCurrentLocaleTranslation('getTitle');
    }

    public function getContent()
    {
        return $this->proxyCurrentLocaleTranslation('getContent');
    }

    public function getSlug()
    {
        return $this->proxyCurrentLocaleTranslation('getSlug');
    }

    public function getImageLocale()
    {
        return $this->proxyCurrentLocaleTranslation('getImageLocale');
    }

    public function getimgLicence()
    {
        return $this->proxyCurrentLocaleTranslation('getImgLicence');
    }

    public function getImgLegend()
    {
        return $this->proxyCurrentLocaleTranslation('getImgLegend');
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
