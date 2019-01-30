<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Entity\Usuario;
use App\Models\Entity\Servico;
/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="avaliacao_cliente")
 */
class AvaliacaoCliente implements \JsonSerializable
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
     * @ORM\OneToOne(targetEntity="App\Models\Entity\Servico")
     */
    private $servico;

     /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=false, name="data_avaliacao")
     */
    private $dataAvaliacao;

     /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $desisteAdiaCancelaServico;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $pagaCombinado;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $exigeAlemCombinado;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $comentario;

    public function __construct(?Usuario $usuario, ?Servico $servico, int $desisteAdiaCancelaServico, int $pagaCombinado,int $exigeAlemCombinado, string $comentario)
    {
        $this->usuario = $usuario;
        $this->servico = $servico;
        $this->dataAvaliacao = new \DateTimeImmutable("now");
        $this->desisteAdiaCancelaServico = $desisteAdiaCancelaServico;
        $this->pagaCombinado = $pagaCombinado;
        $this->exigeAlemCombinado = $exigeAlemCombinado;
        $this->comentario = $comentario;
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

    public function getServico(): ?Servico
    {
        return $this->servico;
    }

    public function setServico($servico){
        $this->servico = $servico;
        return $this;  
    }

    public function getDataAvaliacao(): \DateTimeImmutable
    {
        return $this->dataAvaliacao;
    }

    public function getDesisteAdiaCancelaServico(): int
    {
        return $this->desisteAdiaCancelaServico;
    }

    public function setDesisteAdiaCancelaServico($desisteAdiaCancelaServico){
        $this->desisteAdiaCancelaServico = $desisteAdiaCancelaServico;
        return $this;  
    }

    public function getPagaCombinado(): int
    {
        return $this->pagaCombinado;
    }

    public function setPagaCombinado($pagaCombinado){
        $this->pagaCombinado = $pagaCombinado;
        return $this;  
    }

    public function getExigeAlemCombinado(): int
    {
        return $this->exigeAlemCombinado;
    }

    public function setExigeAlemCombinado($exigeAlemCombinado){
        $this->exigeAlemCombinado = $exigeAlemCombinado;
        return $this;  
    }

    public function getComentario(): String
    {
        return $this->comentario;
    }

    public function setComentario($comentario){
        $this->comentario = $comentario;
        return $this;  
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'usuario' => $this->getUsuario()->getNome(),
            'servico' => $this->getServico()->getId(),
            'dataAvaliacao' => $this->getDataAvaliacao(),
            'desisteAdiaCancelaServico' => $this->getDesisteAdiaCancelaServico(),
            'pagaCombinado' => $this->getPagaCombinado(),
            'exigeAlemCombinado' => $this->getExigeAlemCombinado(),
            'comentario' => $this->getComentario()
        ];
    }
}