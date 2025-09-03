<?php
    namespace Climaco\Biblioteca\Repository; 
    
    // Para usar las clases en los modelos
    use Climaco\Biblioteca\Models\Categoria;


    class CategoriaRepository {
        
        private $categorias = [];

        public function __construct() 
        {
            $this->inicializarCategorias();
        }

        private function inicializarCategorias() 
        {
            $this->categorias = [
                new Categoria(1, "Literatura Clásica", "Obras literarias de reconocido valor universal y perdurable"),
                new Categoria(2, "Ciencia Ficción", "Narrativa que explora conceptos futuristas, científicos y tecnológicos"),
                new Categoria(3, "Literatura Infantil", "Libros dirigidos especialmente al público infantil y juvenil"),
                new Categoria(4, "Fantasía", "Narrativa que incluye elementos mágicos y mundos imaginarios"),
                new Categoria(5, "Romance", "Historias centradas en relaciones amorosas y sentimentales"),
                new Categoria(6, "Thriller", "Novelas de suspense y misterio que mantienen al lector en tensión")
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