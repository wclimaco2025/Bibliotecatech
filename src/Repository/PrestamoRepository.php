<?php
    namespace Climaco\Biblioteca\Repository;

    class PrestamoRepository {
        
        private $prestamos = [];

        public function __construct() 
        {
            $this->inicializarPrestamos();
        }

        private function inicializarPrestamos() 
        {
            $this->prestamos = [
                new \Climaco\Biblioteca\Models\Prestamo(1, 1, 1, "2024-02-01", "2024-02-16"),
                new \Climaco\Biblioteca\Models\Prestamo(2, 2, 3, "2024-02-03", "2024-02-18"),
                new \Climaco\Biblioteca\Models\Prestamo(3, 3, 5, "2024-02-05", "2024-02-20"),
                new \Climaco\Biblioteca\Models\Prestamo(4, 1, 7, "2024-02-07", "2024-02-22"),
                new \Climaco\Biblioteca\Models\Prestamo(5, 4, 2, "2024-02-08", "2024-02-23"),
            ];
            
            // Marcar algunos como devueltos
            $this->prestamos[1]->marcarComoDevuelto("2024-02-17", "Devuelto en buen estado");
            $this->prestamos[2]->marcarComoDevuelto("2024-02-19", "Devuelto con ligero desgaste");
        }

        public function getPrestamos(): array 
        {
            return $this->prestamos;
        }

        public function getPrestamoPorId(int $id): ?Prestamo 
        {
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getId() === $id) {
                    return $prestamo;
                }
            }
            return null;
        }

        public function getPrestamosPorUsuario(int $idUsuario): array 
        {
            $prestamosUsuario = [];
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getIdUsuario() === $idUsuario) {
                    $prestamosUsuario[] = $prestamo;
                }
            }
            return $prestamosUsuario;
        }

        public function getPrestamosPorLibro(int $idLibro): array 
        {
            $prestamosLibro = [];
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getIdLibro() === $idLibro) {
                    $prestamosLibro[] = $prestamo;
                }
            }
            return $prestamosLibro;
        }

        public function getPrestamosActivos(): array 
        {
            $prestamosActivos = [];
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getEstado() === 'activo') {
                    $prestamosActivos[] = $prestamo;
                }
            }
            return $prestamosActivos;
        }

        public function getPrestamosVencidos(): array 
        {
            $prestamosVencidos = [];
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->estaVencido()) {
                    $prestamo->marcarComoVencido();
                    $prestamosVencidos[] = $prestamo;
                }
            }
            return $prestamosVencidos;
        }

        public function libroEstaDisponible(int $idLibro): bool 
        {
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getIdLibro() === $idLibro && $prestamo->getEstado() === 'activo') {
                    return false;
                }
            }
            return true;
        }

        public function usuarioTieneLibrosPendientes(int $idUsuario): bool 
        {
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getIdUsuario() === $idUsuario && $prestamo->getEstado() === 'activo') {
                    return true;
                }
            }
            return false;
        }

        public function agregarPrestamo(Prestamo $prestamo): void 
        {
            $this->prestamos[] = $prestamo;
        }

        public function getTotalPrestamos(): int 
        {
            return count($this->prestamos);
        }

        public function getTotalPrestamosActivos(): int 
        {
            return count($this->getPrestamosActivos());
        }

        public function getProximoId(): int 
        {
            $maxId = 0;
            foreach ($this->prestamos as $prestamo) {
                if ($prestamo->getId() > $maxId) {
                    $maxId = $prestamo->getId();
                }
            }
            return $maxId + 1;
        }
    }
?>