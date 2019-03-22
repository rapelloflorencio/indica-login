<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Profissional;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="cartao_credito")
 */
class CartaoCredito implements \JsonSerializable
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
    private $numero;

     /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $mesExpiracao;

     /**
     * @var int
     *
      * @ORM\Column(type="integer", nullable=false)
     */
    private $anoExpiracao;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  nullable=false)
     */
    private $codigoSeguranca;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  nullable=false)
     */
    private $dataNascimento;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     */
    private $registeredAt;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Profissional")
     */
    private $profissional;

    /**
     * @var string
     *
     * @ORM\Column(type="string",  nullable=false)
     */
    private $status;

    public function __construct(string $numero, int $mesExpiracao, int $anoExpiracao, string $codigoSeguranca, string $nome, string $dataNascimento, ?Profissional $profissional)
    {
        $this->numero = $numero;
        $this->mesExpiracao = $mesExpiracao;
        $this->anoExpiracao = $anoExpiracao;
        $this->codigoSeguranca = $codigoSeguranca;
        $this->nome = $nome;
        $this->dataNascimento = $dataNascimento;
        $this->profissional = $profissional;
        $this->registeredAt = new \DateTimeImmutable('now');
        
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }
    
    public function getMesExpiracao(): int
    {
        return $this->mesExpiracao;
    }

    public function getAnoExpiracao(): int
    {
        return $this->mesExpiracao;
    }

    public function getCodigoSeguranca(): string
    {
        return $this->codigoSeguranca;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDataNascimento(): string
    {
        return $this->dataNascimento;
    }


    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
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
