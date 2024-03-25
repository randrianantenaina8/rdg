<?php

namespace App\Entity;

use App\Repository\DatasetReusedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DatasetReusedRepository::class)
 */
class DatasetReused
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicationTitle;

    /**
     * @ORM\ManyToOne(targetEntity=ReuseType::class, inversedBy="datasetReuseds")
     */
    private $reuseType;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorAffiliation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publicationDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $datasetReusedDoi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $newDatasetDoi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $newDatasetUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicationTitle(): ?string
    {
        return $this->publicationTitle;
    }

    public function setPublicationTitle(string $publicationTitle): self
    {
        $this->publicationTitle = $publicationTitle;

        return $this;
    }

    public function getReuseType(): ?ReuseType
    {
        return $this->reuseType;
    }

    public function setReuseType(?ReuseType $reuseType): self
    {
        $this->reuseType = $reuseType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthorAffiliation(): ?string
    {
        return $this->authorAffiliation;
    }

    public function setAuthorAffiliation(?string $authorAffiliation): self
    {
        $this->authorAffiliation = $authorAffiliation;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getDatasetReusedDoi(): ?string
    {
        return $this->datasetReusedDoi;
    }

    public function setDatasetReusedDoi(?string $datasetReusedDoi): self
    {
        $this->datasetReusedDoi = $datasetReusedDoi;

        return $this;
    }

    public function getNewDatasetDoi(): ?string
    {
        return $this->newDatasetDoi;
    }

    public function setNewDatasetDoi(?string $newDatasetDoi): self
    {
        $this->newDatasetDoi = $newDatasetDoi;

        return $this;
    }

    public function getNewDatasetUrl(): ?string
    {
        return $this->newDatasetUrl;
    }

    public function setNewDatasetUrl(?string $newDatasetUrl): self
    {
        $this->newDatasetUrl = $newDatasetUrl;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(?bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }
}
