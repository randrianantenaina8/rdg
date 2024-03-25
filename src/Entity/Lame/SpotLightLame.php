<?php                                      
                                                     
namespace App\Entity\Lame;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Entity\Dataset;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Lame\SpotLightLameRepository")
 * @ORM\Table(name="lame_spotlight")
 *
 * @RdgAssert\Constraint\LaminaConstraint()
 */
class SpotLightLame extends Lame implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    public const NB_DATASETS = 6;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $autoDataset = false;

    /**
     * @var Dataset[]|ArrayCollection
     */
    private $datasets;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_first", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetFirst = null;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_second", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetSecond = null;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_third", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetThird = null;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_fourth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetFourth = null;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_fifth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetFifth;

    /**
     * @var Dataset|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Dataset")
     * @ORM\JoinColumn(name="dataset_sixth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $datasetSixth;

    public function __construct()
    {
        $this->datasets = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function getAutoDataset(): bool
    {
        return $this->autoDataset;
    }

    /**
     * @param bool $autoDataset
     *
     * @return $this
     */
    public function setAutoDataset(bool $autoDataset): self
    {
        $this->autoDataset = $autoDataset;

        return $this;
    }

    /**
     * @return Dataset[]|ArrayCollection
     */
    public function getDatasets()
    {
        return $this->datasets;
    }

    /**
     * @param Dataset[]|ArrayCollection $datasets
     *
     * @return $this
     */
    public function setDatasets($datasets): self
    {
        $this->datasets = $datasets;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetFirst(): ?Dataset
    {
        return $this->datasetFirst;
    }

    /**
     * @param Dataset|null $datasetFirst
     *
     * @return $this
     */
    public function setDatasetFirst(?Dataset $datasetFirst): self
    {
        $this->datasetFirst = $datasetFirst;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetSecond(): ?Dataset
    {
        return $this->datasetSecond;
    }

    /**
     * @param Dataset|null $datasetSecond
     *
     * @return $this
     */
    public function setDatasetSecond(?Dataset $datasetSecond): self
    {
        $this->datasetSecond = $datasetSecond;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetThird(): ?Dataset
    {
        return $this->datasetThird;
    }

    /**
     * @param Dataset|null $datasetThird
     *
     * @return $this
     */
    public function setDatasetThird(?Dataset $datasetThird): self
    {
        $this->datasetThird = $datasetThird;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetFourth(): ?Dataset
    {
        return $this->datasetFourth;
    }

    /**
     * @param Dataset|null $datasetFourth
     *
     * @return $this
     */
    public function setDatasetFourth(?Dataset $datasetFourth): self
    {
        $this->datasetFourth = $datasetFourth;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetFifth(): ?Dataset
    {
        return $this->datasetFifth;
    }

    /**
     * @param Dataset|null $datasetFifth
     *
     * @return $this
     */
    public function setDatasetFifth(?Dataset $datasetFifth): self
    {
        $this->datasetFifth = $datasetFifth;

        return $this;
    }

    /**
     * @return Dataset|null
     */
    public function getDatasetSixth(): ?Dataset
    {
        return $this->datasetSixth;
    }

    /**
     * @param Dataset|null $datasetSixth
     *
     * @return $this
     */
    public function setDatasetSixth(?Dataset $datasetSixth): self
    {
        $this->datasetSixth = $datasetSixth;

        return $this;
    }

    /* ############################################################## */
    /* ############# SPECIAL GETTERS IN CURRENT LOCALE  ############# */
    /* ############################################################## */

    public function getTitle()
    {
        return $this->proxyCurrentLocaleTranslation('getTitle');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }
}
