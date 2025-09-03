<?php

namespace Climaco\Biblioteca\Models;

class Autor {
    private $id;
    private $nombre;
    private $apellido;

    public function __construct(int $id, string $nombre, string $apellido) 
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
    }

    public function getId(): int 
    {
        return $this->id;
    }

    public function getNombre(): string 
    {
        return $this->nombre;
    }

    public function getApellido(): string 
    {
        return $this->apellido;
    }

    public function getNombreCompleto(){
        return trim($this->nombre) . " " . trim($this->apellido);
    }
}
