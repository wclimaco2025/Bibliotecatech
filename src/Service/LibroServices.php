<?php

namespace Climaco\Biblioteca\Service;
// Para cargar las clases Repository
use Climaco\Biblioteca\Repository\{
    AutorRepository,
    CategoriaRepository,
    LibroRepository,
    PrestamoRepository

};

// Para cargar la clase de Models
use Climaco\Biblioteca\Models\Libro;

class LibroServices
{
    private $libroRepository;
    private $autorRepository;
    private $categoriaRepository;
    private $prestamoRepository;

    public function __construct()
    {
        $this->libroRepository = new LibroRepository();
        $this->autorRepository = new AutorRepository();
        $this->categoriaRepository = new CategoriaRepository();
        $this->prestamoRepository = new PrestamoRepository();
    }

    // Obtener todos los libros
    public function getLibros(): array
    {
        return $this->libroRepository->getLibros();
    }

    // Obtener un libro por ID
    public function getLibro(int $id): ?Libro
    {
        return $this->libroRepository->getLibroPorId($id);
    }

    // Obtener libros por autor
    public function getLibrosPorAutor(int $idAutor): array
    {
        return $this->libroRepository->getLibrosPorAutor($idAutor);
    }

    // Obtener libros por categoría
    public function getLibrosPorCategoria(int $idCategoria): array
    {
        return $this->libroRepository->getLibrosPorCategoria($idCategoria);
    }

    // Obtener información completa de un libro (con autor, categoría y disponibilidad)
    public function getLibroCompleto(int $id): ?array
    {
        $libro = $this->libroRepository->getLibroPorId($id);
        if (!$libro) {
            return null;
        }

        $autor = $this->autorRepository->getAutorPorId($libro->getIdAutor());
        $categoria = $this->categoriaRepository->getCategoriaPorId($libro->getIdCategoria());
        $disponible = $this->prestamoRepository->libroEstaDisponible($id);

        return [
            'libro' => $libro,
            'autor' => $autor,
            'categoria' => $categoria,
            'disponible' => $disponible
        ];
    }

    // Agregar un nuevo libro
    public function agregarLibro(Libro $libro): void
    {
        $this->libroRepository->agregarLibro($libro);
    }

    // Obtener estadísticas
    public function getEstadisticas(): array
    {
        return [
            'total_libros' => $this->libroRepository->getTotalLibros(),
            'total_autores' => $this->autorRepository->getTotalAutores(),
            'total_categorias' => $this->categoriaRepository->getTotalCategorias(),
            'libros_prestados' => $this->prestamoRepository->getTotalPrestamosActivos(),
            'libros_disponibles' => count($this->getLibrosDisponibles())
        ];
    }

    // Buscar libros por título
    public function buscarLibrosPorTitulo(string $titulo): array
    {
        $librosEncontrados = [];
        $todosLosLibros = $this->libroRepository->getLibros();

        foreach ($todosLosLibros as $libro) {
            if (stripos($libro->getTitulo(), $titulo) !== false) {
                $librosEncontrados[] = $libro;
            }
        }

        return $librosEncontrados;
    }

    // Verificar si un libro está disponible para préstamo
    public function libroEstaDisponible(int $idLibro): bool
    {
        return $this->prestamoRepository->libroEstaDisponible($idLibro);
    }

    // Obtener todos los libros disponibles
    public function getLibrosDisponibles(): array
    {
        $librosDisponibles = [];
        $todosLosLibros = $this->libroRepository->getLibros();

        foreach ($todosLosLibros as $libro) {
            if ($this->prestamoRepository->libroEstaDisponible($libro->getId())) {
                $librosDisponibles[] = $libro;
            }
        }

        return $librosDisponibles;
    }

    // Obtener libros con información completa y disponibilidad
    public function getLibrosConDisponibilidad(): array
    {
        $librosConInfo = [];
        $todosLosLibros = $this->libroRepository->getLibros();

        foreach ($todosLosLibros as $libro) {
            $autor = $this->autorRepository->getAutorPorId($libro->getIdAutor());
            $categoria = $this->categoriaRepository->getCategoriaPorId($libro->getIdCategoria());
            $disponible = $this->prestamoRepository->libroEstaDisponible($libro->getId());

            $librosConInfo[] = [
                'libro' => $libro,
                'autor' => $autor,
                'categoria' => $categoria,
                'disponible' => $disponible
            ];
        }

        return $librosConInfo;
    }

}
