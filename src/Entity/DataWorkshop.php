<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\DataWorkshopRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=DataWorkshopRepository::class)
 * @ORM\Table(name="dataworkshop")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 *
 * @RdgAssert\Constraint\DataWorkshopConstraint()
 */
class DataWorkshop implements TranslatableInterface, TimestampableRdgInterface
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
     * @Vich\UploadableField(mapping="dataworkshops", fileNameProperty="image")
     *
     * @Assert\File(
     *     maxSize="1G",
     *     mimeTypes={"images/png", "image/jpg", "image/jpeg", "image/x-png", "image/gif", "image/webp"}
     * )
     */
    private $imageFile;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @Assert\Length(
     *     max=2048
     * )
     */
    private $urlDataWorkshop;

    /**
     * @var Institution[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Institution", inversedBy="dataWorkshops", cascade={"persist"})
     * @ORM\JoinTable(name="institutions_dataworkshops",
     *     joinColumns={
     *         @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="dataworkshop_id", referencedColumnName="id")
     *    }
     * )
     */
    private $institutions;

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
     * @ORM\Column(type="string", length=255)
     */
    private $workshopType;


    public function __construct()
    {
        $this->institutions = new ArrayCollection();
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
     * @return string|null
     */
    public function getUrlDataWorkshop(): ?string
    {
        return $this->urlDataWorkshop;
    }

    /**
     * @param string|null $urlDataWorkshop
     *
     * @return $this
     */
    public function setUrlDataWorkshop(?string $urlDataWorkshop): self
    {
        $this->urlDataWorkshop = $urlDataWorkshop;
        return $this;
    }

    /**
     * @return Institution[]|ArrayCollection
     */
    public function getInstitutions()
    {
        return $this->institutions;
    }

    /**
     * @param Institution $institution
     *
     * @return $this
     */
    public function addInstitution(Institution $institution)
    {
        if (!$this->institutions->contains($institution)) {
            $this->institutions->add($institution);
            $institution->addDataWorkshop($this);
        }

        return $this;
    }

    /**
     * @param Institution $institution
     *
     * @return $this
     */
    public function removeInstitution(Institution $institution)
    {
        if ($this->institutions->contains($institution)) {
            $this->institutions->removeElement($institution);
            $institution->removeDataWorkshop($this);
        }

        return $this;
    }

    /**
     * @return int|null
     */
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

    public function getWorkshopType()
    {
        return $this->workshopType;
    }

    /**
     * @param $workshopType
     *
     * @return self
     */
    public function setWorkshopType($workshopType): self
    {
        $this->workshopType = $workshopType;

        return $this;
    }

    /* ############################################################## */
    /* ############# MAGIC METHODS USED BY TRANSLATIONS ############# */
    /* ############################################################## */

    /**
     * Magic method used in EasyAdmin BO list to get the translated properties.
     * Ex: when you list actualities, it will give you title, slug and locale.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        $arguments = [];
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * Magic method used when __get is not used.
     *
     * @param string $method
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->__get('acronym');
    }
}
