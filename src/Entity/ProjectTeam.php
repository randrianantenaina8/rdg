<?php

namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Repository\ProjectTeamRepository;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProjectTeamRepository::class)
 * @ORM\Table(name="project_team")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Vich\Uploadable()
 *
 */
class ProjectTeam implements TranslatableInterface, TimestampableRdgInterface
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
     * @ORM\Column(type="integer", options={"default": 10})
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

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
     * @return int|null
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function setName($name): self
    {
        $this->name = $name;
        
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
    
    /**
     * @codeCoverageIgnore
     */
    public function getRole()
    {
        return $this->proxyCurrentLocaleTranslation('getRole');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDescription()
    {
        return $this->proxyCurrentLocaleTranslation('getDescription');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getImgLicence()
    {
        return $this->proxyCurrentLocaleTranslation('getImgLicence');
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getRole();
    }
}
