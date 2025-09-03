<?php
    namespace Climaco\Biblioteca\Service;
    
    // Para cargar las clases Repository
    use Climaco\Biblioteca\Repository\{
        UsuarioRepository,
        LibroRepository,
        PrestamoRepository

    };

    // Para cargar la clase de Models
    use Climaco\Biblioteca\Models\Prestamo;

    class PrestamoServices {
        
        private $prestamoRepository;
        private $usuarioRepository;
        private $libroRepository;

        public function __construct() 
        {
            $this->prestamoRepository = new PrestamoRepository();
            $this->usuarioRepository = new UsuarioRepository();
            $this->libroRepository = new LibroRepository();
        }

        // Crear un nuevo préstamo
        public function crearPrestamo(int $idUsuario, int $idLibro, int $diasPrestamo = 15): array 
        {
            // Validar que el usuario existe y está activo
            $usuario = $this->usuarioRepository->getUsuariosPorId($idUsuario);
            if (!$usuario || !$usuario->isActivo()) {
                return ['exito' => false, 'mensaje' => 'Usuario no encontrado o inactivo'];
            }

            // Validar que el libro existe
            $libro = $this->libroRepository->getLibroPorId($idLibro);
            if (!$libro) {
                return ['exito' => false, 'mensaje' => 'Libro no encontrado'];
            }

            // Validar que el libro está disponible
            if (!$this->prestamoRepository->libroEstaDisponible($idLibro)) {
                return ['exito' => false, 'mensaje' => 'El libro no está disponible'];
            }

            // Crear el préstamo
            $id = $this->prestamoRepository->getProximoId();
            $fechaDevolucion = date('Y-m-d', strtotime('+' . $diasPrestamo . ' days'));
            $prestamo = new Prestamo($id, $idUsuario, $idLibro, null, $fechaDevolucion);
            
            $this->prestamoRepository->agregarPrestamo($prestamo);

            return [
                'exito' => true, 
                'mensaje' => 'Préstamo creado exitosamente',
                'prestamo' => $prestamo
            ];
        }

        // Devolver un libro
        public function devolverLibro(int $idPrestamo, string $observaciones = ''): array 
        {
            $prestamo = $this->prestamoRepository->getPrestamoPorId($idPrestamo);
            
            if (!$prestamo) {
                return ['exito' => false, 'mensaje' => 'Préstamo no encontrado'];
            }

            if ($prestamo->getEstado() !== 'activo') {
                return ['exito' => false, 'mensaje' => 'El préstamo ya fue devuelto'];
            }

            $prestamo->marcarComoDevuelto(null, $observaciones);

            return [
                'exito' => true, 
                'mensaje' => 'Libro devuelto exitosamente',
                'prestamo' => $prestamo
            ];
        }

        // Extender un préstamo
        public function extenderPrestamo(int $idPrestamo, int $diasExtension): array 
        {
            $prestamo = $this->prestamoRepository->getPrestamoPorId($idPrestamo);
            
            if (!$prestamo) {
                return ['exito' => false, 'mensaje' => 'Préstamo no encontrado'];
            }

            if ($prestamo->getEstado() !== 'activo') {
                return ['exito' => false, 'mensaje' => 'Solo se pueden extender préstamos activos'];
            }

            $prestamo->extenderPrestamo($diasExtension);

            return [
                'exito' => true, 
                'mensaje' => 'Préstamo extendido exitosamente',
                'prestamo' => $prestamo
            ];
        }

        // Obtener préstamos con información completa
        public function getPrestamoCompleto(int $idPrestamo): ?array 
        {
            $prestamo = $this->prestamoRepository->getPrestamoPorId($idPrestamo);
            if (!$prestamo) {
                return null;
            }

            $usuario = $this->usuarioRepository->getUsuariosPorId($prestamo->getIdUsuario());
            $libro = $this->libroRepository->getLibroPorId($prestamo->getIdLibro());

            return [
                'prestamo' => $prestamo,
                'usuario' => $usuario,
                'libro' => $libro
            ];
        }

        // Obtener todos los préstamos con información completa
        public function getPrestamosCompletos(): array 
        {
            $prestamosCompletos = [];
            $prestamos = $this->prestamoRepository->getPrestamos();

            foreach ($prestamos as $prestamo) {
                $usuario = $this->usuarioRepository->getUsuariosPorId($prestamo->getIdUsuario());
                $libro = $this->libroRepository->getLibroPorId($prestamo->getIdLibro());

                $prestamosCompletos[] = [
                    'prestamo' => $prestamo,
                    'usuario' => $usuario,
                    'libro' => $libro
                ];
            }

            return $prestamosCompletos;
        }

        // Obtener préstamos vencidos con información completa
        public function getPrestamosVencidosCompletos(): array 
        {
            $prestamosVencidos = [];
            $prestamos = $this->prestamoRepository->getPrestamosVencidos();

            foreach ($prestamos as $prestamo) {
                $usuario = $this->usuarioRepository->getUsuariosPorId($prestamo->getIdUsuario());
                $libro = $this->libroRepository->getLibroPorId($prestamo->getIdLibro());

                $prestamosVencidos[] = [
                    'prestamo' => $prestamo,
                    'usuario' => $usuario,
                    'libro' => $libro,
                    'dias_retraso' => $prestamo->getDiasRetraso()
                ];
            }

            return $prestamosVencidos;
        }

        // Obtener historial de préstamos de un usuario
        public function getHistorialUsuario(int $idUsuario): array 
        {
            $prestamos = $this->prestamoRepository->getPrestamosPorUsuario($idUsuario);
            $historial = [];

            foreach ($prestamos as $prestamo) {
                $libro = $this->libroRepository->getLibroPorId($prestamo->getIdLibro());
                $historial[] = [
                    'prestamo' => $prestamo,
                    'libro' => $libro
                ];
            }

            return $historial;
        }

        // Obtener estadísticas de préstamos
        public function getEstadisticasPrestamos(): array 
        {
            return [
                'total_prestamos' => $this->prestamoRepository->getTotalPrestamos(),
                'prestamos_activos' => $this->prestamoRepository->getTotalPrestamosActivos(),
                'prestamos_vencidos' => count($this->prestamoRepository->getPrestamosVencidos()),
                'total_usuarios' => $this->usuarioRepository->getTotalUsuarios(),
                'usuarios_activos' => $this->usuarioRepository->getTotalUsuariosActivos(),
                'total_libros' => $this->libroRepository->getTotalLibros()
            ];
        }
    }
?>