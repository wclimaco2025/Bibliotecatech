<?php
    namespace Climaco\Biblioteca\Repository;
    
    //Para usar las clases de Models
     use Climaco\Biblioteca\Models\Usuario;

    class UsuarioRepository {
        
        private $usuarios = [];

        public function __construct() 
        {
            $this->inicializarUsuarios();
        }

        private function inicializarUsuarios() 
        {
            $this->usuarios = [
                new Usuario(1, "Juan", "Pérez", "juan.perez@email.com", "555-0101", "2024-01-15"),
                new Usuario(2, "María", "González", "maria.gonzalez@email.com", "555-0102", "2024-01-20"),
                new Usuario(3, "Carlos", "Rodríguez", "carlos.rodriguez@email.com", "555-0103", "2024-02-01"),
                new Usuario(4, "Ana", "Martínez", "ana.martinez@email.com", "555-0104", "2024-02-05"),
                new Usuario(5, "Luis", "García", "luis.garcia@email.com", "555-0105", "2024-02-08"),
                new Usuario(6, "Carmen", "López", "carmen.lopez@email.com", "555-0106", "2024-02-10"),
                new Usuario(7, "Pedro", "Sánchez", "pedro.sanchez@email.com", "555-0107", "2024-02-12"),
                new Usuario(8, "Laura", "Fernández", "laura.fernandez@email.com", "555-0108", "2024-02-15"),
            ];
        }

        public function getUsuarios(): array 
        {
            return $this->usuarios;
        }

        public function getUsuariosPorId(int $id): ?Usuario 
        {
            foreach ($this->usuarios as $usuario) {
                if ($usuario->getId() === $id) {
                    return $usuario;
                }
            }
            return null;
        }

        public function getUsuariosActivos(): array 
        {
            $usuariosActivos = [];
            foreach ($this->usuarios as $usuario) {
                if ($usuario->isActivo()) {
                    $usuariosActivos[] = $usuario;
                }
            }
            return $usuariosActivos;
        }

        public function buscarUsuarioPorEmail(string $email): ?Usuario 
        {
            foreach ($this->usuarios as $usuario) {
                if ($usuario->getEmail() === $email) {
                    return $usuario;
                }
            }
            return null;
        }

        public function buscarUsuariosPorNombre(string $nombre): array 
        {
            $usuariosEncontrados = [];
            foreach ($this->usuarios as $usuario) {
                if (stripos($usuario->getNombre(), $nombre) !== false || 
                    stripos($usuario->getApellido(), $nombre) !== false) {
                    $usuariosEncontrados[] = $usuario;
                }
            }
            return $usuariosEncontrados;
        }

        public function agregarUsuario(Usuario $usuario): void 
        {
            $this->usuarios[] = $usuario;
        }

        public function desactivarUsuario(int $id): bool 
        {
            $usuario = $this->getUsuariosPorId($id);
            if ($usuario) {
                $usuario->setActivo(false);
                return true;
            }
            return false;
        }

        public function activarUsuario(int $id): bool 
        {
            $usuario = $this->getUsuariosPorId($id);
            if ($usuario) {
                $usuario->setActivo(true);
                return true;
            }
            return false;
        }

        public function getTotalUsuarios(): int 
        {
            return count($this->usuarios);
        }

        public function getTotalUsuariosActivos(): int 
        {
            return count($this->getUsuariosActivos());
        }
    }
?>