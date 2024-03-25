<?php

namespace App\Entity;

use App\Repository\DataRepositoryTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass=DataRepositoryTranslationRepository::class)
 */
class DataRepositoryTranslation implements TranslationInterface
{

    use TranslationTrait;
    /**
     * Length title property.
     * Used by custom validator.
     */
    public const LEN_TITLE = 255;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    public $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dataType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $repositoryModeration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $embargo;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return self
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    /**
     * @param string|null $dataType
     * @return self
     */
    public function setDataType(?string $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRepositoryModeration(): ?string
    {
        return $this->repositoryModeration;
    }

    /**
     * @param string|null $repositoryModeration
     * @return self
     */
    public function setRepositoryModeration(?string $repositoryModeration): self
    {
        $this->repositoryModeration = $repositoryModeration;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmbargo(): ?string
    {
        return $this->embargo;
    }

    /**
     * @param string|null $embargo
     * @return self
     */
    public function setEmbargo(?string $embargo): self
    {
        $this->embargo = $embargo;

        return $this;
    }
}
