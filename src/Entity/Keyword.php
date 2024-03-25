<?php

namespace App\Entity;

use App\Repository\KeywordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=KeywordRepository::class)
 */
class Keyword implements TranslatableInterface
{
    use TranslatableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=DataRepository::class, mappedBy="keywords")
     */
    private $dataRepositories;

    public function __construct()
    {
        $this->dataRepositories = new ArrayCollection();
    }

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

    public function addDataRepository(DataRepository $dataRepository): self
    {
        if (!$this->dataRepositories->contains($dataRepository)) {
            $this->dataRepositories[] = $dataRepository;
            $dataRepository->addKeyword($this);
        }

        return $this;
    }

    public function removeDataRepository(DataRepository $dataRepository): self
    {
        if ($this->dataRepositories->removeElement($dataRepository)) {
            $dataRepository->removeKeyword($this);
        }

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
        return (string) $this->__get('term');
    }
}
