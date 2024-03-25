<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Validator as RdgAssert;
use App\Repository\DatasetDraftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=DatasetDraftRepository::class)
 * @ORM\Table(name="dataset_draft")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 *
 * @RdgAssert\Constraint\DatasetConstraint()
 */
class DatasetDraft implements TranslatableInterface, TimestampableRdgInterface
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
     * @ORM\Column(type="text")
     */
    protected $datasetQuote;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="dataset_img", fileNameProperty="imageLocale")
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
     * @ORM\ManyToMany(targetEntity="Taxonomy", inversedBy="datasets")
     * @ORM\JoinTable(name="datasets_draft_taxonomies")
     */
    private $taxonomies;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     max=255
     * )
     */
    private $persistentId = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=512, nullable=true)
     *
     * @Assert\Length(
     *     max=512
     * )
     */
    private $linkDataverse = null;

    /**
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="Actuality", inversedBy="datasets")
     * @ORM\JoinColumn(name="actuality_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuality = null;

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
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="Dataset")
     * @ORM\JoinColumn(name="dataset_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $dataset;


    public function __construct()
    {
        $this->taxonomies = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getDatasetQuote()
    {
        return $this->datasetQuote;
    }

    /**
     * @param string|null $datasetQuote
     *
     * @return $this
     */
    public function setDatasetQuote($datasetQuote): self
    {
        $this->datasetQuote = $datasetQuote;

        return $this;
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
     * @return string|null
     */
    public function getPersistentId(): ?string
    {
        return $this->persistentId;
    }

    /**
     * @param string|null $persistentId
     *
     * @return $this
     */
    public function setPersistentId(?string $persistentId): self
    {
        $this->persistentId = $persistentId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkDataverse(): ?string
    {
        return $this->linkDataverse;
    }

    /**
     * @param string|null $linkDataverse
     *
     * @return self
     */
    public function setLinkDataverse(?string $linkDataverse): self
    {
        $this->linkDataverse = $linkDataverse;

        return $this;
    }

    /**
     * @return Actuality|null
     */
    public function getActuality()
    {
        return $this->actuality;
    }

    /**
     * @param Actuality|null $actuality
     *
     * @return $this
     */
    public function setActuality($actuality): self
    {
        $this->actuality = $actuality;

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
     * @return Dataset|null
     */
    public function getDataset(): ?Dataset
    {
        return $this->dataset;
    }

    /**
     * @param Dataset|null $dataset
     *
     * @return $this
     */
    public function setDataset(?Dataset $dataset): self
    {
        $this->dataset = $dataset;

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

    public function getHook()
    {
        return $this->proxyCurrentLocaleTranslation('getHook');
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
}
