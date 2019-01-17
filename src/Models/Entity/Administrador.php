<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Perfil;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="administrador")
 */
class Administrador implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $nome;

     /**
     * @var string
     *
     * @ORM\Column(type="string",  unique=true, nullable=false)
     */
    private $email;

     /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $telefone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     */
    private $registeredAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Perfil")
     */
     private $perfil;

    public function __construct(string $nome, string $password, string $email, string $telefone, ?Perfil $perfil)
    {
        $this->nome = $nome;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->registeredAt = new \DateTimeImmutable('now');
        $this->email = $email;
        $this->telefone = $telefone;
        $this->perfil = $perfil;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefone(): string
    {
        return $this->telefone;
    }

    public function setNome($nome){
        $this->nome = $nome;
        return $this;  
    }
 
    public function setPassword($password){
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;  
    }

    public function getPerfil(): ?Perfil
    {
        return $this->perfil;
    }

    public function setPerfil(?Perfil $perfil): self
    {
        $this->perfil = $perfil;

        return $this;
    }

    public function setEmail($email){
        $this->email = $email;
        return $this;  
    }

    public function setTelefone($telefone){
        $this->telefone = $telefone;
        return $this;  
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'registered_at' => $this->getRegisteredAt()
                ->format(\DateTime::ATOM),
            'email' =>   $this->getEmail(),
            'telefone' =>    $this->getTelefone(),
            'perfil' => $this->getPerfil()
        ];
    }
}
