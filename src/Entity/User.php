<?php                                      
                                                     
namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator as UserAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @UserAssert\Constraint\UserCreate(groups={"new"})
 * @UserAssert\Constraint\UserEditConstraint(groups={"edit"})
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Array that lists all roles defines in security.yaml where key is translations' key.
     */
    public const ROLES = [
        'role.admin' => 'ROLE_ADMIN',
        'role.coord' => 'ROLE_COORD',
        'role.contrib' => 'ROLE_CONTRIB'
    ];

    /**
     * Length username property.
     * Used by custom validator.
     */
    public const LEN_USERNAME = 255;

    /**
     * Length email property.
     * Used by custom validator.
     */
    public const LEN_EMAIL = 255;

    /**
     * Length password property.
     * Used by custom validator.
     */
    public const LEN_PASSWORD = 255;

    /**
     * Validation Regex for password.
     * Used in differents user forms.
     */
    public const PATTERN_PASSWORD = '/^(?=.{12,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(groups={"new", "edit"})
     * @Assert\Length(
     *     groups={"new", "edit"},
     *     max=255
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(groups={"new"})
     */
    private $clearPassword = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\Email(groups={"new", "edit"})
     * @Assert\Length(
     *     groups={"new", "edit"},
     *     min=6,
     *     max=255
     * )
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isActivated;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     *
     * @Assert\NotBlank(groups={"new", "edit"})
     */
    private $roles = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Used in easyadmin crud controller.
     *
     * @return string|null
     */
    public function getClearPassword(): ?string
    {
        return $this->clearPassword;
    }

    /**
     * Used in easyadmin crud controller.
     *
     * @param string|null $clearPassword
     *
     * @return $this
     */
    public function setClearPassword(?string $clearPassword): self
    {
        $this->clearPassword = $clearPassword;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Used to display username in createdBy and updatedBy fields.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }
}
