<?php
    namespace Climaco\Biblioteca\Repository; 

    class LibroRepository {
        
        private $libros = [];

        public function __construct() 
        {
            $this->inicializarLibros();
        }

        private function inicializarLibros() 
        {
            $this->libros = [
                new \Climaco\Biblioteca\Models\Libro(1, "Cien años de soledad", "1967", 1, 1),
                new \Climaco\Biblioteca\Models\Libro(2, "Don Quijote de la Mancha", "1605", 2, 1),
                new \Climaco\Biblioteca\Models\Libro(3, "1984", "1949", 3, 2),
                new \Climaco\Biblioteca\Models\Libro(4, "El principito", "1943", 4, 3),
                new \Climaco\Biblioteca\Models\Libro(5, "Crimen y castigo", "1866", 5, 1),
                new \Climaco\Biblioteca\Models\Libro(6, "El señor de los anillos", "1954", 6, 4),
                new \Climaco\Biblioteca\Models\Libro(7, "Harry Potter y la piedra filosofal", "1997", 7, 4),
                new \Climaco\Biblioteca\Models\Libro(8, "Orgullo y prejuicio", "1813", 8, 5),
                new \Climaco\Biblioteca\Models\Libro(9, "El código Da Vinci", "2003", 9, 6),
                new \Climaco\Biblioteca\Models\Libro(10, "Los juegos del hambre", "2008", 10, 2)
            ];
        }

        public function getLibros(): array 
        {
            return $this->libros;
        }

        public function getLibroPorId(int $id): ?Libro 
        {
            foreach ($this->libros as $libro) {
                if ($libro->getId() === $id) {
                    return $libro;
                }
            }
            return null;
        }

        public function getLibrosPorAutor(int $idAutor): array 
        {
            $librosAutor = [];
            foreach ($this->libros as $libro) {
                if ($libro->getIdAutor() === $idAutor) {
                    $librosAutor[] = $libro;
                }
            }
            return $librosAutor;
        }

        public function getLibrosPorCategoria(int $idCategoria): array 
        {
            $librosCategoria = [];
            foreach ($this->libros as $libro) {
                if ($libro->getIdCategoria() === $idCategoria) {
                    $librosCategoria[] = $libro;
                }
            }
            return $librosCategoria;
        }

        public function agregarLibro(Libro $libro): void 
        {
            $this->libros[] = $libro;
        }

        public function getTotalLibros(): int 
        {
            return count($this->libros);
        }
    }
?>