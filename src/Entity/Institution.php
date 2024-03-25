<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Entity\Lame\CarouselLame;
use App\Repository\InstitutionRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InstitutionRepository::class)
 * @ORM\Table(name="institution")
 * @ORM\HasLifecycleCallbacks()
 *
 * @RdgAssert\Constraint\InstitutionConstraint()
 */
class Institution implements TranslatableInterface, TimestampableRdgInterface
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
    public $image;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @Assert\Length(
     *     max=2048
     * )
     */
    private $urlInstitution;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @Assert\Length(
     *     max=2048
     * )
     */
    public $urlCollection;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @Assert\Length(
     *     max=2048
     * )
     */
    private $urlCollectionContact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @Assert\Length(
     *     max=2048
     * )
     */
    private $urlOpenScience;

    /**
     * @var DataWorkshop[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="DataWorkshop", mappedBy="institutions", cascade={"persist"})
     * @ORM\JoinTable(name="institutions_dataworkshops",
     *     joinColumns={
     *         @ORM\JoinColumn(name="dataworkshop_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     *     }
     * )
     */
    private $dataWorkshops;

    /**
     * @var CarouselLame[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Lame\CarouselLame", mappedBy="entities")
     */
    public $carouselsLame;

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
        $this->dataWorkshops = new ArrayCollection();
        $this->carouselsLame = new ArrayCollection();
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
     * @return string|null
     */
    public function getUrlInstitution(): ?string
    {
        return $this->urlInstitution;
    }

    /**
     * @param string|null $urlInstitution
     *
     * @return $this
     */
    public function setUrlInstitution(?string $urlInstitution): self
    {
        $this->urlInstitution = $urlInstitution;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlCollection(): ?string
    {
        return $this->urlCollection;
    }

    /**
     * @param string|null $urlCollection
     *
     * @return $this
     */
    public function setUrlCollection(?string $urlCollection): self
    {
        $this->urlCollection = $urlCollection;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlCollectionContact(): ?string
    {
        return $this->urlCollectionContact;
    }

    /**
     * @param string|null $urlCollectionContact
     *
     * @return $this
     */
    public function setUrlCollectionContact(?string $urlCollectionContact): self
    {
        $this->urlCollectionContact = $urlCollectionContact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlOpenScience(): ?string
    {
        return $this->urlOpenScience;
    }

    /**
     * @param string|null $urlOpenScience
     *
     * @return $this
     */
    public function setUrlOpenScience(?string $urlOpenScience): self
    {
        $this->urlOpenScience = $urlOpenScience;

        return $this;
    }

    /**
     * @return DataWorkshop[]|ArrayCollection
     */
    public function getDataWorkshops()
    {
        return $this->dataWorkshops;
    }

    /**
     * @param DataWorkshop[]|ArrayCollection $dataWorkshops
     *
     * @return $this
     */
    public function setDataWorkshops($dataWorkshops): self
    {
        $this->dataWorkshops = $dataWorkshops;

        return $this;
    }

    /**
     * @param DataWorkshop $dataworkshop
     *
     * @return $this
     */
    public function addDataWorkshop(DataWorkshop $dataworkshop)
    {
        if (!$this->dataWorkshops->contains($dataworkshop)) {
            $this->dataWorkshops->add($dataworkshop);
            $dataworkshop->addInstitution($this);
        }

        return $this;
    }

    /**
     * @param DataWorkshop $dataworkshop
     *
     * @return $this
     */
    public function removeDataWorkshop(DataWorkshop $dataworkshop)
    {
        if ($this->dataWorkshops->contains($dataworkshop)) {
            $this->dataWorkshops->removeElement($dataworkshop);
            $dataworkshop->removeInstitution($this);
        }

        return $this;
    }

    /**
     * @return CarouselLame[]|ArrayCollection
     */
    public function getCarouselsLame()
    {
        return $this->carouselsLame;
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
