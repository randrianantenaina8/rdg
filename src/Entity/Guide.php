<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\GuideRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=GuideRepository::class)
 * @ORM\Table(name="guide")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 *
 * @RdgAssert\Constraint\GuideConstraint()
 */
class Guide implements TranslatableInterface, TimestampableRdgInterface
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
     * @Vich\UploadableField(mapping="guides", fileNameProperty="imageLocale")
     *
     * @Assert\File(
     *     maxSize="20M",
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg", "image/x-png", "image/gif"}
     * )
     */
    private $imageFile;

    /**
     * @var CategoryGuide[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CategoryGuide", mappedBy="guide", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $categories;

    /**
     * @var AdditionalHelpGuide[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AdditionalHelpGuide",
     *     mappedBy="guide",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $additionalHelps;

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
        $this->categories = new ArrayCollection();
        $this->additionalHelps = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return CategoryGuide[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function addCategory(CategoryGuide $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setGuide($this);
        }

        return $this;
    }

    public function removeCategory(CategoryGuide $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getGuide() === $this) {
                $category->setGuide(null);
            }
        }

        return $this;
    }

    /**
     * @return AdditionalHelpGuide[]|ArrayCollection
     */
    public function getAdditionalHelps()
    {
        return $this->additionalHelps;
    }

    public function addAdditionalHelp(AdditionalHelpGuide $additionalHelp): self
    {
        if (!$this->additionalHelps->contains($additionalHelp)) {
            $this->additionalHelps[] = $additionalHelp;
            $additionalHelp->setGuide($this);
        }

        return $this;
    }

    public function removeAdditionalHelp(AdditionalHelpGuide $additionalHelp): self
    {
        if ($this->additionalHelps->removeElement($additionalHelp)) {
            // set the owning side to null (unless already changed)
            if ($additionalHelp->getGuide() === $this) {
                $additionalHelp->setGuide(null);
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
     * @return $this
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
     * @return $this
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /* ############################################################## */
    /* ############# SPECIAL GETTERS IN CURRENT LOCALE  ############# */
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
        return (string)$this->getTitle();
    }
}
