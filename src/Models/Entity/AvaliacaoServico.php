<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Orcamento;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="avaliacao_servico")
 */
class AvaliacaoServico implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\OneToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento;

     /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_termino")
     */
    private $dataTermino;

     /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valor;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $pontualidade;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $competencia;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $prazo;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $organizacao;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $atitude;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $comentario;
    

    public function __construct(?StatusOrcamento $status, ?SolicitacaoOrcamento $solicitacao, ?Profissional $profissional, int $valor, string $descricao)
    {
        $this->status = $status;
        $this->solicitacao = $solicitacao;
        $this->profissional = $profissional;
        $this->valor = $valor;
        $this->descricao = $descricao;
        $this->data = new \DateTimeImmutable('now');
        $this->senha = $solicitacao->getAtividade()->getMneumonico()."-".$profissional->getId()."-".$data->format(y)."-".$solicitacao->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): ?StatusOrcamento
    {
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
        return $this;  
    }

    public function getSolicitacao(): ?SolicitacaoOrcamento
    {
        return $this->solicitacao;
    }

    public function setSolicitacao($solicitacao){
        $this->solicitacao = $solicitacao;
        return $this;  
    }

    public function getProfissional(): ?Profissional
    {
        return $this->profissional;
    }

    public function setProfissional($profissional){
        $this->profissional = $profissional;
        return $this;  
    }

    public function getValor(): int
    {
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor = $valor;
        return $this;  
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao($descricao){
        $this->descricao = $descricao;
        return $this;  
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha($senha){
        $this->senha = $senha;
        return $this;  
    }

    public function getData(): \DateTimeImmutable
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'solicitacao' => $this->getSolicitacao()->getId(),
            'profissional' => $this->getProfissional(),
            'valor' => $this->getValor(),
            'descricao' => $this->getDescricao(),
            'senha' => $this->getSenha()
        ];
    }
}