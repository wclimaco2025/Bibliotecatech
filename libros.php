<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$libroServices = new \Climaco\Biblioteca\Service\LibroServices();
$libroRepository = new \Climaco\Biblioteca\Repository\LibroRepository();
$autorRepository = new \Climaco\Biblioteca\Repository\AutorRepository();
$categoriaRepository = new \Climaco\Biblioteca\Repository\CategoriaRepository();
$prestamoRepository = new \Climaco\Biblioteca\Repository\PrestamoRepository();

// Procesar acciones
$mensaje = '';
$tipoMensaje = '';

if ($_POST) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear_libro':
                try {
                    // Generar nuevo ID
                    $libros = $libroRepository->getLibros();
                    $maxId = 0;
                    foreach ($libros as $l) {
                        if ($l->getId() > $maxId) {
                            $maxId = $l->getId();
                        }
                    }
                    $nuevoId = $maxId + 1;
                    
                    $nuevoLibro = new Libro(
                        $nuevoId,
                        $_POST['titulo'],
                        $_POST['year'],
                        (int)$_POST['id_autor'],
                        (int)$_POST['id_categoria']
                    );
                    
                    $libroRepository->agregarLibro($nuevoLibro);
                    $mensaje = 'Libro creado exitosamente: ' . $nuevoLibro->getTitulo();
                    $tipoMensaje = 'success';
                } catch (\Exception $e) {
                    $mensaje = 'Error al crear libro: ' . $e->getMessage();
                    $tipoMensaje = 'danger';
                }
                break;
                
            case 'crear_autor':
                try {
                    // Generar nuevo ID
                    $autores = $autorRepository->getAutores();
                    $maxId = 0;
                    foreach ($autores as $a) {
                        if ($a->getId() > $maxId) {
                            $maxId = $a->getId();
                        }
                    }
                    $nuevoId = $maxId + 1;
                    
                    $nuevoAutor = new Autor(
                        $nuevoId,
                        $_POST['nombre_autor'],
                        $_POST['apellido_autor']
                    );
                    
                    $autorRepository->agregarAutor($nuevoAutor);
                    $mensaje = 'Autor creado exitosamente: ' . $nuevoAutor->getNombre() . ' ' . $nuevoAutor->getApellido();
                    $tipoMensaje = 'success';
                } catch (\Exception $e) {
                    $mensaje = 'Error al crear autor: ' . $e->getMessage();
                    $tipoMensaje = 'danger';
                }
                break;
                
            case 'crear_categoria':
                try {
                    // Generar nuevo ID
                    $categorias = $categoriaRepository->getCategorias();
                    $maxId = 0;
                    foreach ($categorias as $c) {
                        if ($c->getId() > $maxId) {
                            $maxId = $c->getId();
                        }
                    }
                    $nuevoId = $maxId + 1;
                    
                    $nuevaCategoria = new Categoria(
                        $nuevoId,
                        $_POST['nombre_categoria'],
                        $_POST['descripcion_categoria']
                    );
                    
                    $categoriaRepository->agregarCategoria($nuevaCategoria);
                    $mensaje = 'Categoría creada exitosamente: ' . $nuevaCategoria->getNombre();
                    $tipoMensaje = 'success';
                } catch (\Exception $e) {
                    $mensaje = 'Error al crear categoría: ' . $e->getMessage();
                    $tipoMensaje = 'danger';
                }
                break;
        }
    }
}

// Obtener datos
$librosConInfo = $libroServices->getLibrosConDisponibilidad();
$autores = $autorRepository->getAutores();
$categorias = $categoriaRepository->getCategorias();
$estadisticas = $libroServices->getEstadisticas();

// Contenido de la página
$content = '';

// Mostrar mensaje si existe
if ($mensaje) {
    $content .= '<div class="alert alert-' . $tipoMensaje . '">' . htmlspecialchars($mensaje) . '</div>';
}

$content .= '
<div class="card">
    <h1>📚 Gestión de Libros</h1>
    <p>Administra el catálogo de libros, autores y categorías</p>
</div>

<!-- Estadísticas -->
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
</div>

<!-- Pestañas de navegación -->
<div class="card">
    <div style="border-bottom: 2px solid #ecf0f1; margin-bottom: 2rem;">
        <button class="tab-button active" onclick="mostrarTab(\'libros\')">📚 Libros</button>
        <button class="tab-button" onclick="mostrarTab(\'autores\')">✍️ Autores</button>
        <button class="tab-button" onclick="mostrarTab(\'categorias\')">🏷️ Categorías</button>
    </div>
    
    <!-- Tab Libros -->
    <div id="tab-libros" class="tab-content">
        <h3>➕ Agregar Nuevo Libro</h3>
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
    $content .= '<option value="' . $autor->getId() . '">' . htmlspecialchars($autor->getNombre() . ' ' . $autor->getApellido()) . '</option>';
}

$content .= '
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id_categoria">Categoría:</label>
                    <select name="id_categoria" id="id_categoria" class="form-control" required>
                        <option value="">Seleccionar categoría...</option>';

foreach ($categorias as $categoria) {
    $content .= '<option value="' . $categoria->getId() . '">' . htmlspecialchars($categoria->getNombre()) . '</option>';
}

$content .= '
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">✅ Agregar Libro</button>
            <button type="reset" class="btn" style="background-color: #95a5a6;">🔄 Limpiar</button>
        </form>
        
        <hr style="margin: 2rem 0;">
        
        <h3>📋 Catálogo de Libros</h3>
        <div style="margin-bottom: 1rem;">
            <input type="text" id="buscar-libro" placeholder="🔍 Buscar libro..." class="form-control" style="max-width: 300px;" onkeyup="filtrarLibros()">
        </div>
        
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
    
    $content .= '
                <tr data-titulo="' . strtolower($libro->getTitulo()) . '" data-autor="' . strtolower($autor->getNombreCompleto()) . '">
                    <td>' . $libro->getId() . '</td>
                    <td><strong>' . htmlspecialchars($libro->getTitulo()) . '</strong></td>
                    <td>' . htmlspecialchars($autor->getNombreCompleto()) . '</td>
                    <td>' . htmlspecialchars($categoria->getNombre()) . '</td>
                    <td>' . htmlspecialchars($libro->getYear()) . '</td>
                    <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                    <td>';
    
    if ($disponible) {
        $content .= '<a href="nuevo-prestamo.php?libro=' . $libro->getId() . '" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">📖 Prestar</a>';
    } else {
        $content .= '<span style="color: #7f8c8d; font-size: 0.8rem;">En préstamo</span>';
    }
    
    $content .= '
                    </td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
    
    <!-- Tab Autores -->
    <div id="tab-autores" class="tab-content" style="display: none;">
        <h3>➕ Agregar Nuevo Autor</h3>
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
        </form>
        
        <hr style="margin: 2rem 0;">
        
        <h3>📋 Lista de Autores</h3>
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
    
    $content .= '
                <tr>
                    <td>' . $autor->getId() . '</td>
                    <td><strong>' . htmlspecialchars($autor->getNombreCompleto()) . '</strong></td>
                    <td>' . count($librosDelAutor) . ' libros</td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
    
    <!-- Tab Categorías -->
    <div id="tab-categorias" class="tab-content" style="display: none;">
        <h3>➕ Agregar Nueva Categoría</h3>
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
        </form>
        
        <hr style="margin: 2rem 0;">
        
        <h3>📋 Lista de Categorías</h3>
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
    
    $content .= '
                <tr>
                    <td>' . $categoria->getId() . '</td>
                    <td><strong>' . htmlspecialchars($categoria->getNombre()) . '</strong></td>
                    <td>' . htmlspecialchars($categoria->getDescripcion()) . '</td>
                    <td>' . count($librosDeLaCategoria) . ' libros</td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="card">
    <h3>🔗 Acciones Rápidas</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="index.php" class="btn">🏠 Dashboard</a>
        <a href="prestamos.php" class="btn btn-success">📚 Gestionar Préstamos</a>
        <a href="usuarios.php" class="btn" style="background-color: #f39c12;">👥 Gestionar Usuarios</a>
        <a href="nuevo-prestamo.php" class="btn" style="background-color: #9b59b6;">➕ Nuevo Préstamo</a>
        <button onclick="window.print()" class="btn" style="background-color: #34495e;">🖨️ Imprimir Catálogo</button>
    </div>
</div>

<style>
.tab-button {
    background: none;
    border: none;
    padding: 1rem 2rem;
    cursor: pointer;
    font-size: 1rem;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.tab-button:hover {
    background-color: #f8f9fa;
}

.tab-button.active {
    border-bottom-color: #3498db;
    color: #3498db;
    font-weight: bold;
}

.tab-content {
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
function mostrarTab(tabName) {
    // Ocultar todos los tabs
    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => tab.style.display = "none");
    
    // Remover clase active de todos los botones
    const buttons = document.querySelectorAll(".tab-button");
    buttons.forEach(button => button.classList.remove("active"));
    
    // Mostrar el tab seleccionado
    document.getElementById("tab-" + tabName).style.display = "block";
    
    // Agregar clase active al botón correspondiente
    event.target.classList.add("active");
}

function filtrarLibros() {
    const busqueda = document.getElementById("buscar-libro").value.toLowerCase();
    const filas = document.querySelectorAll("#tabla-libros tbody tr");
    
    filas.forEach(fila => {
        const titulo = fila.getAttribute("data-titulo");
        const autor = fila.getAttribute("data-autor");
        
        if (titulo.includes(busqueda) || autor.includes(busqueda)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}
</script>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Gestión de Libros - Sistema de Biblioteca", $content);

?>