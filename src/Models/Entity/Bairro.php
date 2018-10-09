<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="bairro")
 */
class Bairro implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $uf;

    /**
     * @ORM\Column(type="integer")
     */
    private $codigo_ibge;

    /**
     * @ORM\Column(type="string")
     */
    private $nome_maiusculo;

    /**
     * @ORM\Column(type="string")
     */
    private $nome;

    /**
     * @ORM\Column(type="string")
     */
    private $latitude;

    /**
     * @ORM\Column(type="string")
     */
    private $longitude;

    

    public function getId(): int
    {
        return $this->id;
    }

    public function getUf(): string
    {
        return $this->uf;
    }

    public function setUf($uf){
        $this->uf = $uf;
        return $this;  
    }

    public function getCodigo_Ibge(): integer
    {
        return $this->codigo_ibge;
    }

    public function setCodigo_Ibge($codigo_ibge){
        $this->codigo_ibge = $codigo_ibge;
        return $this;  
    }

    public function getNome_Maiusculo(): string
    {
        return $this->nome_maiusculo;
    }

    public function setNome_Maiusculo($nome_maiusculo){
        $this->nome_maiusculo = $nome_maiusculo;
        return $this;  
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome($nome){
        $this->nome = $nome;
        return $this;  
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude($latitude){
        $this->latitude = $latitude;
        return $this;  
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude($longitude){
        $this->longitude = $longitude;
        return $this;  
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'uf' => $this->getUf(),
            'nome_maiusculo' => $this->getNome_Maiusculo(),
            'nome' => $this->getNome(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude()
        ];
    }
}