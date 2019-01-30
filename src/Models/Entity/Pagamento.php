<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Profissional;
use App\Models\Entity\Orcamento;
use App\Models\Entity\Servico;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="pagamento")
 */
class Pagamento implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\OneToOne(targetEntity="App\Models\Entity\Servico")
     */
    private $servico;

    /**
     * @ORM\OneToOne(targetEntity="App\Models\Entity\Orcamento")
     */
    private $orcamento;

     /**
     * @ORM\ManyToOne(targetEntity="App\Models\Entity\Profissional")
     */
    private $profissional;
     
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="tipo")
     */
    private $tipo;
    
    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_inclusao")
     */
    private $dataInclusao;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true, name="data_envio")
     */
    private $dataEnvio;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, name="status")
     */
    private $status;

    public function __construct(?Servico $servico, ?Orcamento $orcamento, ?Profissional $profissional, string $tipo,  int $valor)
    {
        $this->servico = $servico;
        $this->orcamento = $orcamento;
        $this->profissional = $profissional;
        $this->tipo = $tipo;
        $this->dataInclusao = new \DateTimeImmutable('now');
        $this->dataEnvio = null;
        $this->valor = $valor;
        $this->status = "A";
    }
    

    public function getId(): int
    {
        return $this->id;
    }

    public function getServico(): ?Servico
    {
        return $this->servico;
    }

    public function setServico($servico){
        $this->servico = $servico;
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

    public function getProfissional(): ?Profissional
    {
        return $this->profissional;
    }

    public function setProfissional($profissional){
        $this->profissional = $profissional;
        return $this;  
    }

    public function getDataInclusao(): \DateTimeImmutable
    {
        return $this->dataInclusao;
    }

    public function setDataInclusao($dataInclusao){
        $this->dataInclusao = new \DateTimeImmutable($dataInclusao);
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

   

    public function getDataEnvio(): \DateTimeImmutable
    {
        if($this->dataEnvio == null){
            return new \DateTimeImmutable("9999-01-01");
        }
        return $this->dataEnvio;
    }

    public function setDataEnvio($dataEnvio){
        $this->dataEnvio = new \DateTimeImmutable($dataEnvio);
        return $this;  
    }
    
    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo($tipo){
        $this->tipo = $tipo;
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
        if($this->getServico()==null){
        return [
            'id' => $this->getId(),
            'servico' => "",
            'orcamento' => $this->getOrcamento()->getId(),
            'profissional' => $this->getProfissional()->getNome(),
            'tipo' => $this->getTipo(),
            'dataInclusao' => $this->getDataInclusao(),
            'valor' => $this->getValor(),
            'dataEnvio' => $this->getDataEnvio(),
            'status' => $this->getStatus()
        ];
        }
        return [
            'id' => $this->getId(),
            'servico' => $this->getServico()->getId(),
            'orcamento' => "",
            'profissional' => $this->getProfissional()->getNome(),
            'tipo' => $this->getTipo(),
            'dataInclusao' => $this->getDataInclusao(),
            'valor' => $this->getValor(),
            'dataEnvio' => $this->getDataEnvio(),
            'status' => $this->getStatus()
        ];
    }
}