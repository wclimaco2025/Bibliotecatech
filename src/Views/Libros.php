<?php
namespace Climaco\Biblioteca;

class LibrosView {
    
    public static function renderTablaLibros($librosConInfo) {
        $html = '
        <table class="table" id="tabla-libros">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($librosConInfo as $libroInfo) {
            $libro = $libroInfo['libro'];
            $autor = $libroInfo['autor'];
            $categoria = $libroInfo['categoria'];
            $disponible = $libroInfo['disponible'];
            
            $estadoColor = $disponible ? '#27ae60' : '#e74c3c';
            $estadoTexto = $disponible ? '✅ Disponible' : '📖 Prestado';
            
            $html .= '
                <tr data-titulo="' . strtolower($libro->getTitulo()) . '" data-autor="' . strtolower($autor->getNombreCompleto()) . '">
                    <td>' . $libro->getId() . '</td>
                    <td><strong>' . htmlspecialchars($libro->getTitulo()) . '</strong></td>
                    <td>' . htmlspecialchars($autor->getNombreCompleto()) . '</td>
                    <td>' . htmlspecialchars($categoria->getNombre()) . '</td>
                    <td>' . htmlspecialchars($libro->getYear()) . '</td>
                    <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                    <td>';
            
            if ($disponible) {
                $html .= '<a href="nuevo-prestamo.php?libro=' . $libro->getId() . '" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">📖 Prestar</a>';
            } else {
                $html .= '<span style="color: #7f8c8d; font-size: 0.8rem;">En préstamo</span>';
            }
            
            $html .= '
                    </td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
        
        return $html;
    }
    
    public static function renderFormularioNuevoLibro($autores, $categorias) {
        $html = '
        <form method="POST" id="form-nuevo-libro">
            <input type="hidden" name="accion" value="crear_libro">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Año de Publicación:</label>
                    <input type="text" name="year" id="year" class="form-control" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="id_autor">Autor:</label>
                    <select name="id_autor" id="id_autor" class="form-control" required>
                        <option value="">Seleccionar autor...</option>';
        
        foreach ($autores as $autor) {
            $html .= '<option value="' . $autor->getId() . '">' . htmlspecialchars($autor->getNombre() . ' ' . $autor->getApellido()) . '</option>';
        }
        
        $html .= '
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id_categoria">Categoría:</label>
                    <select name="id_categoria" id="id_categoria" class="form-control" required>
                        <option value="">Seleccionar categoría...</option>';
        
        foreach ($categorias as $categoria) {
            $html .= '<option value="' . $categoria->getId() . '">' . htmlspecialchars($categoria->getNombre()) . '</option>';
        }
        
        $html .= '
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">✅ Agregar Libro</button>
            <button type="reset" class="btn" style="background-color: #95a5a6;">🔄 Limpiar</button>
        </form>';
        
        return $html;
    }
    
    public static function renderTablaAutores($autores, $libroRepository) {
        $html = '
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Libros en Catálogo</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($autores as $autor) {
            $librosDelAutor = $libroRepository->getLibrosPorAutor($autor->getId());
            
            $html .= '
                <tr>
                    <td>' . $autor->getId() . '</td>
                    <td><strong>' . htmlspecialchars($autor->getNombreCompleto()) . '</strong></td>
                    <td>' . count($librosDelAutor) . ' libros</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
        
        return $html;
    }
    
    public static function renderTablaCategorias($categorias, $libroRepository) {
        $html = '
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Libros en Categoría</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($categorias as $categoria) {
            $librosDeLaCategoria = $libroRepository->getLibrosPorCategoria($categoria->getId());
            
            $html .= '
                <tr>
                    <td>' . $categoria->getId() . '</td>
                    <td><strong>' . htmlspecialchars($categoria->getNombre()) . '</strong></td>
                    <td>' . htmlspecialchars($categoria->getDescripcion()) . '</td>
                    <td>' . count($librosDeLaCategoria) . ' libros</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
        
        return $html;
    }
    
    public static function renderFormularioNuevoAutor() {
        return '
        <form method="POST" id="form-nuevo-autor">
            <input type="hidden" name="accion" value="crear_autor">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="nombre_autor">Nombre:</label>
                    <input type="text" name="nombre_autor" id="nombre_autor" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido_autor">Apellido:</label>
                    <input type="text" name="apellido_autor" id="apellido_autor" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">✅ Agregar Autor</button>
            <button type="reset" class="btn" style="background-color: #95a5a6;">🔄 Limpiar</button>
        </form>';
    }
    
    public static function renderFormularioNuevaCategoria() {
        return '
        <form method="POST" id="form-nueva-categoria">
            <input type="hidden" name="accion" value="crear_categoria">
            
            <div class="form-group">
                <label for="nombre_categoria">Nombre:</label>
                <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion_categoria">Descripción:</label>
                <textarea name="descripcion_categoria" id="descripcion_categoria" class="form-control" rows="3" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">✅ Agregar Categoría</button>
            <button type="reset" class="btn" style="background-color: #95a5a6;">🔄 Limpiar</button>
        </form>';
    }
    
    public static function renderEstadisticasLibros($estadisticas) {
        return '
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
                <h4>📚 Total Libros</h4>
                <h2>' . $estadisticas['total_libros'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
                <h4>✅ Disponibles</h4>
                <h2>' . $estadisticas['libros_disponibles'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
                <h4>📖 Prestados</h4>
                <h2>' . $estadisticas['libros_prestados'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
                <h4>✍️ Autores</h4>
                <h2>' . $estadisticas['total_autores'] . '</h2>
            </div>
        </div>';
    }
}
?>