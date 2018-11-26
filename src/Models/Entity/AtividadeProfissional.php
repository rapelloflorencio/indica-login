<?php

declare(strict_types=1);

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The User class demonstrates how to annotate a simple
 * PHP class to act as a Doctrine entity.
 *
 * @ORM\Entity()
 * @ORM\Table(name="atividade_profissional")
 */
class AtividadeProfissional implements \JsonSerializable
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
    private $nome;

    /**
     * @ORM\Column(type="string")
     */
    private $mneumonico;

    public function __construct(string $nome, string $mneumonico)
    {
        $this->nome = $nome;
        $this->mneumonico = $mneumonico;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome($nome){
        $this->nome = $nome;
        return $this;  
    }

    public function getMneumonico(): string
    {
        return $this->mneumonico;
    }

    public function setMneumonico($mneumonico){
        $this->mneumonico = $mneumonico;
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
            'mneumonico' => $this->getMneumonico()
        ];
    }
}