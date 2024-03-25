<?php

namespace App\Entity;

use App\Repository\DataRepositoryRepository;
use App\Entity\DataRepositoryTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=DataRepositoryRepository::class)
 */
class DataRepository implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serversLocation;

    /**
     * @ORM\ManyToMany(targetEntity=SupportingInstitution::class, inversedBy="dataRepositories")
     */
    private $supportingInstitutions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $catopidorLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $re3dataLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $catopidorIdentifier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $re3dataIdentifier;

    /**
     * @ORM\ManyToMany(targetEntity=Discipline::class, inversedBy="dataRepositories")
     */
    private $disciplines;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $certificate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $repositoryIdentifier;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $repositoryCreationDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileVolumeLimit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $datasetVolumeLimit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $retentionPeriod;

    /**
     * @var string[]|ArrayCollection
     * @ORM\Column(type="array", nullable=true)
     */
    public $disciplinaryAreas;

    /**
     * @ORM\ManyToMany(targetEntity=Keyword::class, inversedBy="dataRepositories")
     */
    private $keywords;

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
        $this->disciplines = new ArrayCollection();
        $this->disciplinaryAreas = new ArrayCollection;
        $this->keywords = new ArrayCollection();
        $this->supportingInstitutions = new ArrayCollection();
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     * @return self
     */
    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getServersLocation(): ?string
    {
        return $this->serversLocation;
    }

    /**
     * @param string|null $serversLocation
     * @return self
     */
    public function setServersLocation(?string $serversLocation): self
    {
        $this->serversLocation = $serversLocation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCatopidorLink(): ?string
    {
        return $this->catopidorLink;
    }

    /**
     * @param string|null $catopidorLink
     * @return self
     */
    public function setCatopidorLink(?string $catopidorLink): self
    {
        $this->catopidorLink = $catopidorLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRe3dataLink(): ?string
    {
        return $this->re3dataLink;
    }

    /**
     * @param string|null $re3dataLink
     * @return self
     */
    public function setRe3dataLink(?string $re3dataLink): self
    {
        $this->re3dataLink = $re3dataLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCatopidorIdentifier(): ?string
    {
        return $this->catopidorIdentifier;
    }

    /**
     * @param string|null $catopidorIdentifier
     * @return self
     */
    public function setCatopidorIdentifier(?string $catopidorIdentifier): self
    {
        $this->catopidorIdentifier = $catopidorIdentifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRe3dataIdentifier(): ?string
    {
        return $this->re3dataIdentifier;
    }

    /**
     * @param string|null $re3dataIdentifier
     * @return self
     */
    public function setRe3dataIdentifier(?string $re3dataIdentifier): self
    {
        $this->re3dataIdentifier = $re3dataIdentifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCertificate(): ?string
    {
        return $this->certificate;
    }

    /**
     * @param string|null $certificate
     * @return self
     */
    public function setCertificate(?string $certificate): self
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRepositoryIdentifier(): ?string
    {
        return $this->repositoryIdentifier;
    }

    /**
     * @param string|null $repositoryIdentifier
     * @return self
     */
    public function setRepositoryIdentifier(?string $repositoryIdentifier): self
    {
        $this->repositoryIdentifier = $repositoryIdentifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRepositoryCreationDate(): ?string
    {
        return $this->repositoryCreationDate;
    }

    /**
     * @param string|null $repositoryCreationDate
     * @return self
     */
    public function setRepositoryCreationDate(?string $repositoryCreationDate): self
    {
        $this->repositoryCreationDate = $repositoryCreationDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileVolumeLimit(): ?string
    {
        return $this->fileVolumeLimit;
    }

    /**
     * @param string|null $fileVolumeLimit
     * @return self
     */
    public function setFileVolumeLimit(?string $fileVolumeLimit): self
    {
        $this->fileVolumeLimit = $fileVolumeLimit;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDatasetVolumeLimit(): ?string
    {
        return $this->datasetVolumeLimit;
    }

    /**
     * @param string|null $datasetVolumeLimit
     * @return self
     */
    public function setDatasetVolumeLimit(?string $datasetVolumeLimit): self
    {
        $this->datasetVolumeLimit = $datasetVolumeLimit;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRetentionPeriod(): ?string
    {
        return $this->retentionPeriod;
    }

    /**
     * @param string|null $retentionPeriod
     * @return self
     */
    public function setRetentionPeriod(?string $retentionPeriod): self
    {
        $this->retentionPeriod = $retentionPeriod;

        return $this;
    }

    /**
     * @return string[]|ArrayCollection
     */
    public function getDisciplinaryAreas()
    {
        return $this->disciplinaryAreas;
    }

    /**
     * @param array $disciplinaryAreas
     * @return $this
     */
    public function setDisciplinaryAreas(array $disciplinaryAreas): void
    {
        $this->disciplinaryAreas = $disciplinaryAreas;
    }

    /**
     * @param string $disciplinaryArea
     * @return void
     */
    public function addDisciplinaryArea(string $disciplinaryArea): void
    {
        $this->disciplinaryAreas[] = $disciplinaryArea;
    }

    /**
     * @param string $disciplinaryArea
     * @return void
     */
    public function removeDisciplinaryArea(string $disciplinaryArea): void
    {
        $key = array_search($disciplinaryArea, $this->disciplinaryAreas);

        if ($key !== false) {
            unset($this->disciplinaryAreas[$key]);
            $this->disciplinaryAreas = array_values($this->disciplinaryAreas);
        }
    }

    /**
     * @return Collection
     */
    public function getSupportingInstitutions(): Collection
    {
        return $this->supportingInstitutions;
    }

    /**
     * @param SupportingInstitution $supportingInstitution
     * @return self
     */
    public function addSupportingInstitution(SupportingInstitution $supportingInstitution): self
    {
        if (!$this->supportingInstitutions->contains($supportingInstitution)) {
            $this->supportingInstitutions[] = $supportingInstitution;
        }

        return $this;
    }

    /**
     * @param SupportingInstitution $supportingInstitution
     * @return self
     */
    public function removeSupportingInstitution(SupportingInstitution $supportingInstitution): self
    {
        $this->supportingInstitutions->removeElement($supportingInstitution);

        return $this;
    }

    /**
     * @return Collection<int, Discipline>
     */
    public function getDisciplines(): Collection
    {
        return $this->disciplines;
    }

    /**
     * @param Discipline $discipline
     * @return self
     */
    public function addDiscipline(Discipline $discipline): self
    {
        if (!$this->disciplines->contains($discipline)) {
            $this->disciplines[] = $discipline;
        }

        return $this;
    }

    /**
     * @param Discipline $discipline
     * @return self
     */
    public function removeDiscipline(Discipline $discipline): self
    {
        $this->disciplines->removeElement($discipline);

        return $this;
    }

    /**
     * @return Collection<int, Keyword>
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    /**
     * @param Keyword $keyword
     * @return self
     */
    public function addKeyword(Keyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    /**
     * @param Keyword $keyword
     * @return self
     */
    public function removeKeyword(Keyword $keyword): self
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    /**
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $createdBy
     * @return self
     */
    public function setCreatedBy($createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param $updatedBy
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
     * @return void
     */
    public function getName()
    {
        return $this->proxyCurrentLocaleTranslation('getName');
    }

    /**
     * @return void
     */
    public function getDescription()
    {
        return $this->proxyCurrentLocaleTranslation('getDescription');
    }

    /**
     * @return void
     */
    public function getUrl()
    {
        return $this->proxyCurrentLocaleTranslation('getUrl');
    }

    /**
     * @return void
     */
    public function getDataType()
    {
        return $this->proxyCurrentLocaleTranslation('getDataType');
    }

    /**
     * @return void
     */
    public function getRepositoryModeration()
    {
        return $this->proxyCurrentLocaleTranslation('getRepositoryModeration');
    }

    /**
     * @return void
     */
    public function getEmbargo()
    {
        return $this->proxyCurrentLocaleTranslation('getEmbargo');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $name = (string) $this->getName();
        
        if ($name instanceof DataRepositoryTranslation) {
            if (empty($name)) {
                switch ($this->getCurrentLocale()) {
                    case 'fr':
                        $name = $this->id . ' - Non traduit en français';
                        break;
                    case 'en':
                        $name = $this->id . ' - Not translated in english';
                        break;
                    default:
                        $name = $this->id . ' - Not translated at all';
                        break;
                }
            }
        }

        return $name;
    }
}
