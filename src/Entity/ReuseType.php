<?php

namespace App\Entity;

use App\Repository\ReuseTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReuseTypeRepository::class)
 */
class ReuseType
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=DatasetReused::class, mappedBy="reuseType")
     */
    private $datasetReuseds;

    public function __construct()
    {
        $this->datasetReuseds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, DatasetReused>
     */
    public function getDatasetReuseds(): Collection
    {
        return $this->datasetReuseds;
    }

    public function addDatasetReused(DatasetReused $datasetReused): self
    {
        if (!$this->datasetReuseds->contains($datasetReused)) {
            $this->datasetReuseds[] = $datasetReused;
            $datasetReused->setReuseType($this);
        }

        return $this;
    }

    public function removeDatasetReused(DatasetReused $datasetReused): self
    {
        if ($this->datasetReuseds->removeElement($datasetReused)) {
            // set the owning side to null (unless already changed)
            if ($datasetReused->getReuseType() === $this) {
                $datasetReused->setReuseType(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
