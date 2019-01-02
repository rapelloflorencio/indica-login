<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\SolicitacaoOrcamento;
use App\Models\Entity\Orcamento;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="servico")
 */
class Servico implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\SolicitacaoOrcamento")
     */
    private $solicitacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento;
     
    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_inicio")
     */
    private $dataInicio;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valorInicialServico;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valorInicialMaoObra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $prazoInicial;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true, name="data_termino")
     */
    private $dataTermino;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true, name="data_pagamento")
     */
    private $dataPagamento;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valorTotalServico;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valorTotalMaoObra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valorTotalAPagar;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="status")
     */
    private $status;

    public function __construct(?SolicitacaoOrcamento $solicitacao, ?Orcamento $orcamento, string $dataInicio, int $valorInicialServico, int $valorInicialMaoObra, int $prazoInicial)
    {
        $this->solicitacao = $solicitacao;
        $this->orcamento = $orcamento;
        $this->dataInicio = new \DateTimeImmutable($dataInicio);
        $this->valorInicialServico = $valorInicialServico;
        $this->valorInicialMaoObra = $valorInicialMaoObra;
        $this->prazoInicial = $prazoInicial;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSolicitacao(): ?SolicitacaoOrcamento
    {
        return $this->solicitacao;
    }

    public function setSolicitacao($solicitacao){
        $this->solicitacao = $solicitacao;
        return $this;  
    }

    public function getOrcamento(): ?Orcamento
    {
        return $this->orcamento;
    }

    public function setOrcamento($orcamento){
        $this->orcamento = $orcamento;
        return $this;  
    }

    public function getDataInicio(): \DateTimeImmutable
    {
        return $this->dataInicio;
    }

    public function setDataInicio($dataInicio){
        $this->dataInicio = new \DateTimeImmutable($dataInicio);
        return $this;  
    }

    public function getValorInicialServico(): int
    {
        return $this->valorInicialServico;
    }

    public function setValorInicialServico($valorInicialServico){
        $this->valorInicialServico = $valorInicialServico;
        return $this;  
    }

    public function getValorInicialMaoObra(): int
    {
        return $this->valorInicialMaoObra;
    }

    public function setValorInicialMaoObra($valorInicialMaoObra){
        $this->valorInicialMaoObra = $valorInicialMaoObra;
        return $this;  
    }

    public function getPrazoInicial(): int
    {
        return $this->prazoInicial;
    }

    public function setPrazoInicial($prazoInicial){
        $this->prazoInicial = $prazoInicial;
        return $this;  
    }

    public function getDataTermino(): \DateTimeImmutable
    {
        return $this->dataTermino;
    }

    public function setDataTermino($dataTermino){
        $this->dataTermino = new \DateTimeImmutable($dataTermino);
        return $this;  
    }

    public function getDataPagamento(): \DateTimeImmutable
    {
        return $this->dataPagamento;
    }

    public function setDataPagamento($dataPagamento){
        $this->dataPagamento = new \DateTimeImmutable($dataPagamento);
        return $this;  
    }

    public function getValorTotalServico(): int
    {
        return $this->valorTotalServico;
    }

    public function setValorTotalServico($valorTotalServico){
        $this->valorTotalServico = $valorTotalServico;
        return $this;  
    }

    public function getValorTotalMaoObra(): int
    {
        return $this->valorTotalMaoObra;
    }

    public function setValorTotalMaoObra($valorTotalMaoObra){
        $this->valorTotalMaoObra = $valorTotalMaoObra;
        return $this;  
    }

    public function getValorTotalAPagar(): int
    {
        return $this->valorTotalAPagar;
    }

    public function setValorTotalAPagar($valorTotalAPagar){
        $this->valorTotalAPagar = $valorTotalAPagar;
        return $this;  
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
        return $this;  
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'solicitacao' => $this->getSolicitacao()->getId(),
            'orcamento' => $this->getOrcamento()->getId(),
            'dataInicio' => $this->getDataInicio(),
            'valorInicialServico' => $this->getValorInicialServico(),
            'valorInicialMaoObra' => $this->getValorInicialMaoObra(),
            'prazoInicial' => $this->getPrazoInicial(),
            'dataTermino' => $this->getDataTermino(),
            'dataPagamento' => $this->getDataPagamento(),
            'valorTotalServico' => $this->getValorTotalServico(),
            'valorTotalMaoObra' => $this->getValorTotalMaoObra(),
            'valorTotalAPagar' => $this->getValorTotalAPagar(),
            'status' => $this->getStatus()
        ];
    }
}