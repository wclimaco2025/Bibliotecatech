<?php
    namespace Climaco\Biblioteca\Models;

    class Usuario {
        private $id;
        private $nombre;
        private $apellido;
        private $email;
        private $telefono;
        private $fechaRegistro;
        private $activo;

        public function __construct(int $id, string $nombre, string $apellido, string $email, string $telefono, string $fechaRegistro = null, bool $activo = true) 
        {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->apellido = $apellido;
            $this->email = $email;
            $this->telefono = $telefono;
            $this->fechaRegistro = $fechaRegistro ?? date('Y-m-d');
            $this->activo = $activo;
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

        public function getEmail(): string 
        {
            return $this->email;
        }

        public function getTelefono(): string 
        {
            return $this->telefono;
        }

        public function getFechaRegistro(): string 
        {
            return $this->fechaRegistro;
        }

        public function isActivo(): bool 
        {
            return $this->activo;
        }

        public function getNombreCompleto(): string 
        {
            return $this->nombre . " " . $this->apellido;
        }

        public function setActivo(bool $activo): void 
        {
            $this->activo = $activo;
        }

        public function setTelefono(string $telefono): void 
        {
            $this->telefono = $telefono;
        }

        public function setEmail(string $email): void 
        {
            $this->email = $email;
        }
    }
?>