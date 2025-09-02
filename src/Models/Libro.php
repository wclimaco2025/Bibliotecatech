<?php

namespace Climaco\Biblioteca\Models;

class Libro
{
    private $id;
    private $titulo;
    private $year;
    private $idAutor;
    private $idCategoria;


    public function __construct(int $id, string $titulo, string $year, int $idAutor, int $idCategoria)
    {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->year = $year;
        $this->idAutor = $idAutor;
        $this->idCategoria = $idCategoria;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function getIdAutor(): int
    {
        return $this->idAutor;
    }

    public function getIdCategoria(): int
    {
        return $this->idCategoria;
    }
}
