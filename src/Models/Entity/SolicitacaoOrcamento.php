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
     * @ORM\Column(type="string", nullable=false, name="data_desejada")
     */
    private $dataDesejada;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\HorarioServico")
     */
    private $horario;

     /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_solcitacao")
     */
    private $dataSolicitacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento1;

    
    /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento2;


    public function __construct(?Usuario $usuario, ?AtividadeProfissional $atividade, ?Bairro $bairro, string $textoSolicitacao, string $dataDesejada,?HorarioServico $horario)
    {
        $this->usuario = $usuario;
        $this->atividade = $atividade;
        $this->bairro = $bairro;
        $this->textoSolicitacao = $textoSolicitacao;
        $this->dataDesejada = $dataDesejada;
        $this->horario = $horario;
        $this->dataSolicitacao = new \DateTimeImmutable('now');
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

    public function getDataDesejada(): string
    {
        return $this->dataDesejada;
    }

    public function setDataDesejada($dataDesejada){
        $this->dataDesejada = $dataDesejada;
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

    public function getDataSolicitacao(): \DateTimeImmutable
    {
        return $this->dataSolicitacao;
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
            'dataDesejada' => $this->getDataDesejada(),
            'horario' => $this->getHorario(),
            'dataSolicitacao' => $this->getDataSolicitacao(),
            'orcamento1' => $this->getOrcamento1(),
            'orcamento2' => $this->getOrcamento2()
        ];
    }
}