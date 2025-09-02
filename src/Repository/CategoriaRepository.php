<?php
    namespace Climaco\Biblioteca\Repository; 
    
    require __DIR__ . "/vendor/autoload.php";

    class CategoriaRepository {
        
        private $categorias = [];

        public function __construct() 
        {
            $this->inicializarCategorias();
        }

        private function inicializarCategorias() 
        {
            $this->categorias = [
                new \Climaco\Biblioteca\Models\Categoria(1, "Literatura Clásica", "Obras literarias de reconocido valor universal y perdurable"),
                new \Climaco\Biblioteca\Models\Categoria(2, "Ciencia Ficción", "Narrativa que explora conceptos futuristas, científicos y tecnológicos"),
                new \Climaco\Biblioteca\Models\Categoria(3, "Literatura Infantil", "Libros dirigidos especialmente al público infantil y juvenil"),
                new \Climaco\Biblioteca\Models\Categoria(4, "Fantasía", "Narrativa que incluye elementos mágicos y mundos imaginarios"),
                new \Climaco\Biblioteca\Models\Categoria(5, "Romance", "Historias centradas en relaciones amorosas y sentimentales"),
                new \Climaco\Biblioteca\Models\Categoria(6, "Thriller", "Novelas de suspense y misterio que mantienen al lector en tensión")
            ];
        }

        public function getCategorias(): array 
        {
            return $this->categorias;
        }

        public function getCategoriaPorId(int $id): ?Categoria 
        {
            foreach ($this->categorias as $categoria) {
                if ($categoria->getId() === $id) {
                    return $categoria;
                }
            }
            return null;
        }

        public function buscarCategoriaPorNombre(string $nombre): array 
        {
            $categoriasEncontradas = [];
            foreach ($this->categorias as $categoria) {
                if (stripos($categoria->getNombre(), $nombre) !== false) {
                    $categoriasEncontradas[] = $categoria;
                }
            }
            return $categoriasEncontradas;
        }

        public function agregarCategoria(Categoria $categoria): void 
        {
            $this->categorias[] = $categoria;
        }

        public function getTotalCategorias(): int 
        {
            return count($this->categorias);
        }

        public function getNombreCategoria(int $id): ?string 
        {
            $categoria = $this->getCategoriaPorId($id);
            if ($categoria) {
                return $categoria->getNombre();
            }
            return null;
        }
    }
?>