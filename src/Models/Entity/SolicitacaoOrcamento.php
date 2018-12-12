<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\AtividadeProfissional;
use App\Models\Entity\Bairro;
use App\Models\Entity\SolicitacaoOrcamento;
use App\Models\Entity\Usuario;
use App\Models\Entity\HorarioServico;
use App\Models\Entity\Orcamento;
use App\Models\Entity\LocalAtendimento;
use App\Models\Entity\UrgenciaServico;
use App\Models\Entity\Profissional;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="solicitacao_orcamento")
 */
class SolicitacaoOrcamento implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Usuario")
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\AtividadeProfissional")
     */
    private $atividade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Bairro")
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="texto_solicitacao")
     */
    private $textoSolicitacao;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="endereco")
     */
    private $endereco;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\UrgenciaServico")
     */
    private $urgenciaServico;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\LocalAtendimento")
     */
    private $localAtendimento;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\HorarioServico")
     */
    private $horario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\HorarioServico")
     */
    private $horarioAlternativo;

     /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_solcitacao")
     */
    private $dataSolicitacao;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_servico")
     */
    private $dataServico;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true, name="data_alternativa")
     */
    private $dataAlternativa;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento1;

    
    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="status")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Profissional", nullable=true)
     */
    private $favorito;

    public function __construct(?Usuario $usuario, ?AtividadeProfissional $atividade, ?Bairro $bairro, string $textoSolicitacao, ?UrgenciaServico $urgenciaServico,?LocalAtendimento $localAtendimento,?HorarioServico $horario, string $endereco, ?HorarioServico $horarioAlternativo, string $dataServico, string $dataAlternativa, ?Profissional $favorito)
    {
        $this->usuario = $usuario;
        $this->atividade = $atividade;
        $this->bairro = $bairro;
        $this->textoSolicitacao = $textoSolicitacao;
        $this->urgenciaServico = $urgenciaServico;
        $this->localAtendimento = $localAtendimento;
        $this->horario = $horario;
        $this->dataSolicitacao = new \DateTimeImmutable('now');
        $this->status = "A";
        $this->endereco = $endereco;
        $this->horarioAlternativo = $horarioAlternativo;
        $this->dataServico = new \DateTimeImmutable($dataServico);
        if($dataAlternativa != ""){
            $this->dataAlternativa = new \DateTimeImmutable($dataAlternativa);
        } else{
            $this->dataAlternativa = null;
        }
        $this->favorito = $favorito;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario($usuario){
        $this->usuario = $usuario;
        return $this;  
    }

    public function getAtividade(): ?AtividadeProfissional
    {
        return $this->atividade;
    }

    public function setAtividade($atividade){
        $this->atividade = $atividade;
        return $this;  
    }

    public function getBairro(): ?Bairro
    {
        return $this->bairro;
    }

    public function setBairro($bairro){
        $this->bairro = $bairro;
        return $this;  
    }

    public function getTextoSolicitacao(): string
    {
        return $this->textoSolicitacao;
    }

    public function setTextoSolicitacao($textoSolicitacao){
        $this->textoSolicitacao = $textoSolicitacao;
        return $this;  
    }

    public function getEndereco(): string
    {
        return $this->endereco;
    }

    public function setEndereco($endereco){
        $this->endereco = $endereco;
        return $this;  
    }

    public function getUrgenciaServico(): ?UrgenciaServico
    {
        return $this->urgenciaServico;
    }

    public function setUrgenciaServico($urgenciaServico){
        $this->urgenciaServico = $urgenciaServico;
        return $this;  
    }

    public function getLocalAtendimento(): ?LocalAtendimento
    {
        return $this->localAtendimento;
    }

    public function setLocalAtendimento($localAtendimento){
        $this->localAtendimento = $localAtendimento;
        return $this;  
    }

    public function getHorario(): ?HorarioServico
    {
        return $this->horario;
    }

    public function setHorario($horario){
        $this->horario = $horario;
        return $this;  
    }

    public function getHorarioAlternativo(): ?HorarioServico
    {
        return $this->horarioAlternativo;
    }

    public function setHorarioAlternativo($horarioAlternativo){
        $this->horarioAlternativo = $horarioAlternativo;
        return $this;  
    }

    public function getDataSolicitacao(): \DateTimeImmutable
    {
        return $this->dataSolicitacao;
    }

    public function getDataServico(): \DateTimeImmutable
    {
        return $this->dataServico;
    }

    public function getDataAlternativa(): string
    {
        if($this->dataAlternativa != null){
            return $this->dataAlternativa->format('Y-m-d');
        }else 
            return "";
    }

    public function getOrcamento1(): ?Orcamento
    {
        return $this->orcamento1;
    }

    public function setOrcamento1($orcamento1){
        $this->orcamento1 = $orcamento1;
        return $this;  
    }

    public function getOrcamento2(): ?Orcamento
    {
        return $this->orcamento2;
    }

    public function setOrcamento2($orcamento2){
        $this->orcamento2 = $orcamento2;
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

    public function getFavorito(): ?Profissional
    {
        return $this->favorito;
    }

    public function setFavorito($favorito){
        $this->favorito = $favorito;
        return $this;  
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'usuario' => $this->getUsuario(),
            'atividade' => $this->getAtividade(),
            'bairro' => $this->getBairro(),
            'textoSolicitacao' => $this->getTextoSolicitacao(),
            'urgenciaServico' => $this->getUrgenciaServico(),
            'localAtendimento' => $this->getLocalAtendimento(),
            'dataServico' => $this->getDataServico(),
            'horario' => $this->getHorario(),
            'dataAlternativa' => $this->getDataAlternativa(),
            'horarioAlternativo' => $this->getHorarioAlternativo(),
            'dataSolicitacao' => $this->getDataSolicitacao(),
            'orcamento1' => $this->getOrcamento1(),
            'orcamento2' => $this->getOrcamento2(),
            'status' => $this->getStatus(),
            'favorito' => $this->getFavorito()
        ];
    }
}