<?php
    namespace Climaco\Biblioteca\Repository;

    //Para usar las clases
    use Climaco\Biblioteca\Models\Autor;

    class AutorRepository {
        
        private $autores = [];

        public function __construct() 
        {
            $this->inicializarAutores();
        }

        private function inicializarAutores() 
        {
            $this->autores = [
                new Autor(1, "Gabriel", "García Márquez"),
                new Autor(2, "Miguel", "de Cervantes"),
                new Autor(3, "George", "Orwell"),
                new Autor(4, "Antoine", "de Saint-Exupéry"),
                new Autor(5, "Fiódor", "Dostoievski"),
                new Autor(6, "J.R.R.", "Tolkien"),
                new Autor(7, "J.K.", "Rowling"),
                new Autor(8, "Jane", "Austen"),
                new Autor(9, "Dan", "Brown"),
                new Autor(10, "Suzanne", "Collins")
            ];
        }

        public function getAutores(): array 
        {
            return $this->autores;
        }

        public function getAutorPorId(int $id): ?Autor 
        {
            foreach ($this->autores as $autor) {
                if ($autor->getId() === $id) {
                    return $autor;
                }
            }
            return null;
        }

        public function buscarAutorPorNombre(string $nombre): array 
        {
            $autoresEncontrados = [];
            foreach ($this->autores as $autor) {
                if (stripos($autor->getNombre(), $nombre) !== false || 
                    stripos($autor->getApellido(), $nombre) !== false) {
                    $autoresEncontrados[] = $autor;
                }
            }
            return $autoresEncontrados;
        }

        public function agregarAutor(Autor $autor): void 
        {
            $this->autores[] = $autor;
        }

        public function getTotalAutores(): int 
        {
            return count($this->autores);
        }

        public function getNombreCompleto(int $id): ?string 
        {
            $autor = $this->getAutorPorId($id);
            if ($autor) {
                return $autor->getNombre() . " " . $autor->getApellido();
            }
            return null;
        }
    }
?>