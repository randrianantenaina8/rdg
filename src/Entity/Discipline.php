<?php

namespace App\Entity;


use App\Repository\DisciplineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=DisciplineRepository::class)
 */
class Discipline implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=DataRepository::class, mappedBy="disciplines")
     */
    private $dataRepositories;

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
     * @return Collection<int, DataRepository>
     */
    public function getDataRepositories(): Collection
    {
        return $this->dataRepositories;
    }

    /**
     * @param DataRepository $dataRepository
     * @return self
     */
    public function addDataRepository(DataRepository $dataRepository): self
    {
        if (!$this->dataRepositories->contains($dataRepository)) {
            $this->dataRepositories[] = $dataRepository;
            $dataRepository->addDiscipline($this);
        }

        return $this;
    }

    /**
     * @param DataRepository $dataRepository
     * @return self
     */
    public function removeDataRepository(DataRepository $dataRepository): self
    {
        if ($this->dataRepositories->removeElement($dataRepository)) {
            $dataRepository->removeDiscipline($this);
        }

        return $this;
    }

    /**
     * Magic method used in EasyAdmin BO list to get the translated properties.
     * Ex: when you list actualities, it will give you title, slug and locale.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($title)
    {
        $method = 'get' . ucfirst($title);
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
        return (string) $this->__get('title');
    }
}
