<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/** @Hateoas\Relation(
 *    "self",
 *    href = @Hateoas\Route(
 *        "api_get_user",
 *        parameters = {"id" = "expr(object.getId())"},
 *        absolute = true
 *    ),
 * )
 * @Hateoas\Relation(
 *     "previous",
 *     href = @Hateoas\Route(
 *         "api_get_user",
 *         parameters = {"id" = "1"},
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"detail"}, excludeIf="expr(object.getId() == 1)")
 * )
 * @Hateoas\Relation(
 *     "next",
 *     href = @Hateoas\Route(
 *         "api_get_user",
 *         parameters = {"id" = "expr(object.getId() + 1)"},
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"detail"})
 * )
 * @Hateoas\Relation(
 *     "first",
 *     href = @Hateoas\Route(
 *         "api_get_user",
 *         parameters = {"id" = "1"},
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"detail"})
 * )
 * @Hateoas\Relation(
 *     "last",
 *     href = @Hateoas\Route(
 *         "api_get_user",
 *         parameters = {"id" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"detail"})
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastname;

    #[Serializer\Exclude]
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
