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
    

    public function __construct(?Orcamento $orcamento, string $dataTermino, int $valor, int $pontualidade,int $competencia, int $prazo, int $organizacao, int $atitude, string $comentario)
    {
        $this->orcamento = $orcamento;
        $this->dataTermino = new \DateTimeImmutable($dataTermino);;
        $this->valor = $valor;
        $this->pontualidade = $pontualidade;
        $this->competencia = $competencia;
        $this->prazo = $prazo;
        $this->organizacao = $organizacao;
        $this->atitude = $atitude;
        $this->comentario = $comentario;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrcamento(): ?Orcamento
    {
        return $this->orcamento;
    }

    public function setOrcamento($orcamento){
        $this->orcamento = $orcamento;
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

    public function getPontualidade(): int
    {
        return $this->pontualidade;
    }

    public function setPontualidade($pontualidade){
        $this->pontualidade = $pontualidade;
        return $this;  
    }

    public function getPrazo(): int
    {
        return $this->prazo;
    }

    public function setPrazo($prazo){
        $this->prazo = $prazo;
        return $this;  
    }

    public function getOrganizacao(): int
    {
        return $this->organizacao;
    }

    public function setOrganizacao($organizacao){
        $this->organizacao = $organizacao;
        return $this;  
    }

    public function getCompetencia(): int
    {
        return $this->competencia;
    }

    public function setCompetencia($competencia){
        $this->competencia = $competencia;
        return $this;  
    }

    public function getAtitude(): int
    {
        return $this->atitude;
    }

    public function setAtitude($atitude){
        $this->atitude = $atitude;
        return $this;  
    }

    public function getComentario(): int
    {
        return $this->comentario;
    }

    public function setComentario($comentario){
        $this->comentario = $comentario;
        return $this;  
    }

    public function getDataTermino(): \DateTimeImmutable
    {
        return $this->dataTermino;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'orcamento' => $this->getOrcamento(),
            'valor' => $this->getValor(),
            'pontualidade' => $this->getPontualidade(),
            'prazo' => $this->getPrazo(),
            'organizacao' => $this->getOrganizacao(),
            'atitude' => $this->getAtitude(),
            'competencia' => $this->getCompetencia(),
            'comentario' => $this->getComentario()
        ];
    }
}