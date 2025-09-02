<?php
    namespace Climaco\Biblioteca\Models; 

    class Categoria {
        private $id;
        private $nombre;
        private $descripcion;

        public function __construct(int $id, string $nombre, string $descripcion) 
        {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
        }

        public function getId(): int 
        {
            return $this->id;
        }

        public function getNombre(): string 
        {
            return $this->nombre;
        }

        public function getDescripcion(): string 
        {
            return $this->descripcion;
        }
    }
?>