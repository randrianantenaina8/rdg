<?php                                      
                                                     
namespace App\Entity\Lame;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use App\Entity\Actuality;
use App\Entity\Event;
use App\Validator as RdgAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Lame\NewsLameRepository")
 * @ORM\Table(name="lame_news")
 *
 * @RdgAssert\Constraint\LaminaConstraint()
 */
class NewsLame extends Lame implements TranslatableInterface, TimestampableRdgInterface
{
    use TranslatableTrait;
    use TimestampableRdgTrait;

    public const NB_NEWS = 3;
    public const NB_EVENTS = 3;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $autoActu = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $autoEvent = false;

    /**
     * @var Actuality[]|ArrayCollection
     */
    private $actualities;

    /**
     * @var Event|ArrayCollection
     */
    private $events;

    /**
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Actuality")
     * @ORM\JoinColumn(name="actu_first", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuFirst = null;

    /**
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Actuality")
     * @ORM\JoinColumn(name="actu_second", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuSecond = null;

    /**
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Actuality")
     * @ORM\JoinColumn(name="actu_third", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuThird = null;

    /**
     * @var Actuality|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Actuality")
     * @ORM\JoinColumn(name="actu_fourth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $actuFourth = null;

    /**
     * @var Event|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="event_first", referencedColumnName="id", onDelete="SET NULL")
     */
    private $eventFirst = null;

    /**
     * @var Event|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="event_second", referencedColumnName="id", onDelete="SET NULL")
     */
    private $eventSecond = null;

    /**
     * @var Event|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="event_third", referencedColumnName="id", onDelete="SET NULL")
     */
    private $eventThird = null;

    /**
     * @var Event|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="event_fourth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $eventFourth = null;

    /**
     * @var Event|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="event_fifth", referencedColumnName="id", onDelete="SET NULL")
     */
    private $eventFifth = null;

    public function __construct()
    {
        $this->actualities = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function getAutoActu(): bool
    {
        return $this->autoActu;
    }

    /**
     * @param bool $autoActu
     *
     * @return $this
     */
    public function setAutoActu(bool $autoActu): self
    {
        $this->autoActu = $autoActu;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoEvent(): bool
    {
        return $this->autoEvent;
    }

    /**
     * @param bool $autoEvent
     *
     * @return $this
     */
    public function setAutoEvent(bool $autoEvent): self
    {
        $this->autoEvent = $autoEvent;

        return $this;
    }

    /**
     * @return Actuality[]|ArrayCollection
     */
    public function getActualities()
    {
        return $this->actualities;
    }

    /**
     * @param Actuality[]|ArrayCollection $actualities
     *
     * @return $this
     */
    public function setActualities($actualities): self
    {
        $this->actualities = $actualities;

        return $this;
    }

    /**
     * @return Event|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event|ArrayCollection $events
     *
     * @return $this
     */
    public function setEvents($events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return Actuality|null
     */
    public function getActuFirst(): ?Actuality
    {
        return $this->actuFirst;
    }

    /**
     * @param Actuality|null $actuFirst
     *
     * @return $this
     */
    public function setActuFirst(?Actuality $actuFirst): self
    {
        $this->actuFirst = $actuFirst;

        return $this;
    }

    /**
     * @return Actuality|null
     */
    public function getActuSecond(): ?Actuality
    {
        return $this->actuSecond;
    }

    /**
     * @param Actuality|null $actuSecond
     *
     * @return $this
     */
    public function setActuSecond(?Actuality $actuSecond): self
    {
        $this->actuSecond = $actuSecond;

        return $this;
    }

    /**
     * @return Actuality|null
     */
    public function getActuThird(): ?Actuality
    {
        return $this->actuThird;
    }

    /**
     * @param Actuality|null $actuThird
     *
     * @return $this
     */
    public function setActuThird(?Actuality $actuThird): self
    {
        $this->actuThird = $actuThird;

        return $this;
    }

    /**
     * @return Actuality|null
     */
    public function getActuFourth(): ?Actuality
    {
        return $this->actuFourth;
    }

    /**
     * @param Actuality|null $actuFourth
     *
     * @return $this
     */
    public function setActuFourth(?Actuality $actuFourth): self
    {
        $this->actuFourth = $actuFourth;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEventFirst(): ?Event
    {
        return $this->eventFirst;
    }

    /**
     * @param Event|null $eventFirst
     *
     * @return $this
     */
    public function setEventFirst(?Event $eventFirst): self
    {
        $this->eventFirst = $eventFirst;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEventSecond(): ?Event
    {
        return $this->eventSecond;
    }

    /**
     * @param Event|null $eventSecond
     *
     * @return $this
     */
    public function setEventSecond(?Event $eventSecond): self
    {
        $this->eventSecond = $eventSecond;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEventThird(): ?Event
    {
        return $this->eventThird;
    }

    /**
     * @param Event|null $eventThird
     *
     * @return $this
     */
    public function setEventThird(?Event $eventThird): self
    {
        $this->eventThird = $eventThird;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEventFourth(): ?Event
    {
        return $this->eventFourth;
    }

    /**
     * @param Event|null $eventFourth
     *
     * @return $this
     */
    public function setEventFourth(?Event $eventFourth): self
    {
        $this->eventFourth = $eventFourth;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEventFifth(): ?Event
    {
        return $this->eventFifth;
    }

    /**
     * @param Event|null $eventFifth
     *
     * @return $this
     */
    public function setEventFifth(?Event $eventFifth): self
    {
        $this->eventFifth = $eventFifth;

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
