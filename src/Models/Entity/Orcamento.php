<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\StatusOrcamento;
use App\Models\Entity\SolicitacaoOrcamento;
use App\Models\Entity\Profissional;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="orcamento")
 */
class Orcamento implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\StatusOrcamento")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\SolicitacaoOrcamento")
     */
    private $solicitacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Profissional")
     */
    private $profissional;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false)
     */
    private $data;

     /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valor;

     /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $descricao;

    public function __construct(?StatusOrcamento $status, ?SolicitacaoOrcamento $solicitacao, ?Profissional $profissional, int $valor, string $descricao)
    {
        $this->status = $status;
        $this->solcitacao = $solicitacao;
        $this->profissional = $profissional;
        $this->valor = $valor;
        $this->descricao = $descricao;
        $this->data = new \DateTimeImmutable('now');
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
            'solicitacao' => $this->getSolicitacao(),
            'profissional' => $this->getProfissional(),
            'valor' => $this->getValor(),
            'descricao' => $this->getDescricao()
        ];
    }
}