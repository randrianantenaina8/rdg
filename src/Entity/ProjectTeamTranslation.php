<?php

namespace App\Entity;

use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use App\Repository\ProjectTeamTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ProjectTeamTranslationRepository::class)
 * @ORM\Table(name="project_team_translation")
 */
class ProjectTeamTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * Length title property.
     * Used by custom validator.
     */
    public const LEN_IMG_LICENCE = 50;

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
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=self::LEN_IMG_LICENCE, nullable=true)
     */
    protected $imgLicence;


    /**
     * @return int/null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function setRole($role): self
    {
        $this->role = $role;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getImgLicence()
    {
        return $this->imgLicence;
    }

    /**
     * @param $imgLicence
     * 
     * @return $this
     */
    public function setImgLicence($imgLicence)
    {
        $this->imgLicence = $imgLicence;

        return $this;
    }
}
