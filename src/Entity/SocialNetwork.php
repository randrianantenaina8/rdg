<?php                                      
                                                     
namespace App\Entity;

use App\Contracts\TimestampableRdg\TimestampableRdgInterface;
use App\Contracts\TimestampableRdg\TimestampableRdgTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocialNetworkRepository")
 * @ORM\Table(name="social_network")
 */
class SocialNetwork implements TimestampableRdgInterface
{
    use TimestampableRdgTrait;

    /**
     * Length name property.
     * Used by custom validator.
     */
    public const LEN_NAME = 255;

    /**
     * Available Government logo for social networks.
     */
    public const AV_GV_LOGO = [
        'Dailymotion' => 'fr-fi-dailymotion-fill',
        'Facebook' => 'fr-fi-facebook-circle-fill',
        'Github' => 'fr-fi-github-fill',
        'Instagram' => 'fr-fi-instagram-fill',
        'Linkedin' => 'fr-fi-linkedin-box-fill',
        'Npmjs' => 'fr-fi-npmjs-fill',
        'Remixicon' => 'fr-fi-remixicon-fill',
        'Slack' => 'fr-fi-slack-fill',
        'Snapchat' => 'fr-fi-snapchat-fill',
        'Telegram' => 'fr-fi-telegram-fill',
        'Twitter' => 'fr-fi-twitter-fill',
        'Tiktok' => 'fr-fi-tiktok-fill',
        'Twitch' => 'fr-fi-twitch-fill',
        'Vimeo' => 'fr-fi-vimeo-fill',
        'Youtube' => 'fr-fi-youtube-fill',
    ];

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=255
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=510, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     * @Assert\Length(
     *     max=510
     * )
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight = 10;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return $this
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param $createdBy
     *
     * @return self
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
     * @return self
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
