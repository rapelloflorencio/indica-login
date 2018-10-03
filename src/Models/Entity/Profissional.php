<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Perfil;
use App\Models\Entity\AtividadeProfissional;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="profissionais")
 */
class Profissional implements \JsonSerializable
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
     * @ORM\Column(type="string")
     */
    private $nome_fantasia;
     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $cep;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $complemento;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $telefone2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefone3;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $telefone4;

     /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $cpf;

     /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $cnpj;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $frenterg;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $versorg;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $comprovante;
     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $imagem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\AtividadeProfissional")
     */
    private $atividade_principal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\AtividadeProfissional")
     */
    private $atividade_extra;

     /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $situacao_cadastral;

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
   
    public function __construct(string $nome, string $nome_fantasia, string $password, string $cep, string $endereco, string $complemento, string $bairro, string $email, string $telefone1, string $telefone2,string $telefone3, string $telefone4, string $cpf, string $cnpj, string $frenterg, string $versorg, string $comprovante, string $imagem, ?AtividadeProfissional $atividade_principal, ?AtividadeProfissional $atividade_extra, string $situacao_cadastral, ?Perfil $perfil)
    {
        $this->nome = $nome;
        $this->nome_fantasia = $nome_fantasia;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->registeredAt = new \DateTimeImmutable('now');
        $this->cep = $cep;
        $this->endereco = $endereco;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->email = $email;
        $this->telefone1 = $telefone1;
        $this->telefone2 = $telefone2;
        $this->telefone3 = $telefone3;
        $this->telefone4 = $telefone4;
        $this->cpf = $cpf;
        $this->cnpj = $cnpj;
        $this->frenterg = $frenterg;
        $this->versorg = $versorg;
        $this->comprovante = $comprovante;
        $this->atividade_principal = $atividade_principal;
        $this->atividade_extra = $atividade_extra;
        $this->situacao_cadastral = $situacao_cadastral;
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

    public function getNome_fantasia(): string
    {
        return $this->nome_fantasia;
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
    public function getComplemento(): string
    {
        return $this->complemento;
    }
    public function getBairro(): string
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

    public function getTelefone3(): string
    {
        return $this->telefone3;
    }

    public function getTelefone4(): string
    {
        return $this->telefone4;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    public function getFrenterg(): string
    {
        return $this->frenterg;
    }

    public function getVersorg(): string
    {
        return $this->versorg;
    }

    public function getComprovante(): string
    {
        return $this->comprovante;
    }

    public function getAtividade_principal(): ?AtividadeProfissional
    {
        return $this->atividade_principal;
    }

    public function getAtividade_Extra(): ?AtividadeProfissional
    {
        return $this->atividade_extra;
    }

    public function getSituacao_cadastral(): string
    {
        return $this->situacao_cadastral;
    }

    public function getImagem(): string
    {
        return $this->imagem;
    }

    public function setNome($nome){
        $this->nome = $nome;
        return $this;  
    }

    public function setNome_fantasia($nome_fantasia){
        $this->nome_fantasia = $nome_fantasia;
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

    public function setCep($cep){
        $this->cep = $cep;
        return $this;  
    }

    public function setEndereco($endereco){
        $this->endereco = $endereco;
        return $this;  
    }

    public function setComplemento($complemento){
        $this->complemento = $complemento;
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

    public function setTelefone3($telefone3){
        $this->telefone3 = $telefone3;
        return $this;  
    }

    public function setTelefone4($telefone4){
        $this->telefone4 = $telefone4;
        return $this;  
    }

    public function setCpf($cpf){
        $this->cpf = $cpf;
        return $this;  
    }

    public function setCnpj($cnpj){
        $this->cnpj = $cnpj;
        return $this;  
    }

    public function setFrenterg($frenterg){
        $this->frenterg = $frenterg;
        return $this;  
    }

    public function setVersorg($versorg){
        $this->versorg = $versorg;
        return $this;  
    }

    public function setComprovante($comprovante){
        $this->comprovante = $comprovante;
        return $this;  
    }

    public function setAtividade_principal($atividade_principal){
        $this->atividade_principal = $atividade_principal;
        return $this;  
    }

    public function setAtividade_extra($atividade_extra){
        $this->atividade_extra = $atividade_extra;
        return $this;  
    }

    public function setSituacao_cadastral($situacao_cadastral){
        $this->situacao_cadastral = $situacao_cadastral;
        return $this;  
    }

    public function setImagem($imagem){
        $this->imagem = $imagem;
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
            'nome_fantasia' => $this->getNome_fantasia(),
            'registered_at' => $this->getRegisteredAt()
                ->format(\DateTime::ATOM),
            'cep' => $this->getCep(),
            'endereco' =>   $this->getEndereco(),
            'complemento' => $this->getComplemento(),
            'bairro' =>   $this->getBairro(),
            'email' =>   $this->getEmail(),
            'telefone1' =>    $this->getTelefone1(),
            'telefone2' =>   $this->getTelefone2(),
            'telefone3' =>    $this->getTelefone3(),
            'telefone4' =>   $this->getTelefone4(),
            'cpf' =>   $this->getCpf(),
            'cnpj' =>   $this->getCnpj(),
            'atividade_principal' => $this->getAtividade_principal(),
            'atividade_extra' => $this->getAtividade_Extra(),
            'situacao_cadastral' => $this->getSituacao_cadastral(),
            'foto' => $this->getImagem(),
            'frenterg' => $this->getFrenterg(),
            'versorg' => $this->getVersorg(),
            'comprovante' => $this->getComprovante(),
            'perfil' => $this->getPerfil()
        ];
    }
}
