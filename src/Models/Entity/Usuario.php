<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Perfil;
use App\Models\Entity\Bairro;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="usuarios")
 */
class Usuario implements \JsonSerializable
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $cep;

     /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $endereco;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Bairro")
     */
    private $bairro;

     /**
     * @var string
     *
     * @ORM\Column(type="string",  unique=true, nullable=false)
     */
    private $email;

     /**
     * @var string
     *
     * @ORM\Column(type="string",  unique=true, nullable=false)
     */
    private $telefone1;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $telefone2;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $cpf;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagem;

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
   
    public function __construct(string $nome, string $password, string $cep, string $endereco, ?Bairro $bairro, string $email, string $telefone1, string $telefone2, string $cpf, string $imagem, ?Perfil $perfil)
    {
        $this->nome = $nome;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->registeredAt = new \DateTimeImmutable('now');
        $this->cep = $cep;
        $this->endereco = $endereco;
        $this->bairro = $bairro;
        $this->email = $email;
        $this->telefone1 = $telefone1;
        $this->telefone2 = $telefone2;
        $this->cpf = $cpf;
        $this->imagem = $imagem;
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

    public function getCep(): string
    {
        return $this->cep;
    }

    public function getEndereco(): string
    {
        return $this->endereco;
    }

    public function getBairro(): ?Bairro
    {
        return $this->bairro;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefone1(): string
    {
        return $this->telefone1;
    }

    public function getTelefone2(): string
    {
        return $this->telefone2;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getImagem(): string
    {
        return $this->imagem;
    }

    public function setNome($nome){
        $this->nome = $nome;
        return $this;  
    }

    public function setCep($cep){
        $this->cep = $cep;
        return $this;  
    }

    public function setEndereco($endereco){
        $this->endereco = $endereco;
        return $this;  
    }

    public function setBairro($bairro){
        $this->bairro = $bairro;
        return $this;  
    }

    public function setEmail($email){
        $this->email = $email;
        return $this;  
    }

    public function setTelefone1($telefone1){
        $this->telefone1 = $telefone1;
        return $this;  
    }

    public function setTelefone2($telefone2){
        $this->telefone2 = $telefone2;
        return $this;  
    }

    public function setcpf($cpf){
        $this->cpf = $cpf;
        return $this;  
    }

    public function setImagem($imagem){
        $this->imagem = $imagem;
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
            'cep' => $this->getCep(),
            'endereco' =>   $this->getEndereco(),
            'bairro' =>   $this->getBairro(),
            'email' =>   $this->getEmail(),
            'telefone1' =>    $this->getTelefone1(),
            'telefone2' =>   $this->getTelefone2(),
            'cpf' =>   $this->getCpf(),
            'foto' => $this->getImagem(),
            'perfil' => $this->getPerfil()
        ];
    }
}
