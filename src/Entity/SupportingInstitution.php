<?php

namespace App\Entity;

use App\Repository\SupportingInstitutionRepository;
use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SupportingInstitutionRepository::class)
 */
class SupportingInstitution implements TranslatableInterface, TimestampableRdgInterface
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
     * @ORM\ManyToMany(targetEntity=DataRepository::class, mappedBy="supportingInstitutions")
     */
    private $dataRepositories;

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
        $this->dataRepositories = new ArrayCollection();
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
