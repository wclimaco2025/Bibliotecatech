<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$libroServices = new \Climaco\Biblioteca\Service\LibroServices();
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();

// Procesar búsqueda
$termino = isset($_GET['q']) ? trim($_GET['q']) : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';

$resultadosLibros = [];
$resultadosUsuarios = [];
$resultadosPrestamos = [];

if ($termino) {
    // Buscar libros
    if ($tipo === 'todos' || $tipo === 'libros') {
        $resultadosLibros = $libroServices->buscarLibrosPorTitulo($termino);
    }
    
    // Buscar usuarios
    if ($tipo === 'todos' || $tipo === 'usuarios') {
        $resultadosUsuarios = $usuarioRepository->buscarUsuariosPorNombre($termino);
    }
    
    // Buscar préstamos (por usuario o libro)
    if ($tipo === 'todos' || $tipo === 'prestamos') {
        $todosPrestamos = $prestamoServices->getPrestamosCompletos();
        foreach ($todosPrestamos as $prestamoData) {
            $usuario = $prestamoData['usuario'];
            $libro = $prestamoData['libro'];
            
            if (stripos($usuario->getNombreCompleto(), $termino) !== false ||
                stripos($libro->getTitulo(), $termino) !== false) {
                $resultadosPrestamos[] = $prestamoData;
            }
        }
    }
}

// Contenido de la página
$content = '
<div class="card">
    <h1>🔍 Búsqueda Avanzada</h1>
    <p>Busca libros, usuarios y préstamos en el sistema</p>
</div>

<!-- Formulario de búsqueda -->
<div class="card">
    <form method="GET" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
        <div class="form-group">
            <label for="q">Término de búsqueda:</label>
            <input type="text" name="q" id="q" class="form-control" value="' . htmlspecialchars($termino) . '" placeholder="Escribe aquí para buscar..." required>
        </div>
        
        <div class="form-group">
            <label for="tipo">Buscar en:</label>
            <select name="tipo" id="tipo" class="form-control">
                <option value="todos"' . ($tipo === 'todos' ? ' selected' : '') . '>🔍 Todo el sistema</option>
                <option value="libros"' . ($tipo === 'libros' ? ' selected' : '') . '>📚 Solo libros</option>
                <option value="usuarios"' . ($tipo === 'usuarios' ? ' selected' : '') . '>👥 Solo usuarios</option>
                <option value="prestamos"' . ($tipo === 'prestamos' ? ' selected' : '') . '>📖 Solo préstamos</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">🔍 Buscar</button>
    </form>
</div>';

if ($termino) {
    $totalResultados = count($resultadosLibros) + count($resultadosUsuarios) + count($resultadosPrestamos);
    
    $content .= '
    <div class="card">
        <h3>📊 Resultados de búsqueda para: "' . htmlspecialchars($termino) . '"</h3>
        <p>Se encontraron <strong>' . $totalResultados . '</strong> resultados</p>
    </div>';
    
    // Mostrar estadísticas de resultados
    if ($totalResultados > 0) {
        $content .= '
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
                <h4>📚 Libros</h4>
                <h2>' . count($resultadosLibros) . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
                <h4>👥 Usuarios</h4>
                <h2>' . count($resultadosUsuarios) . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
                <h4>📖 Préstamos</h4>
                <h2>' . count($resultadosPrestamos) . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
                <h4>🎯 Total</h4>
                <h2>' . $totalResultados . '</h2>
            </div>
        </div>';
    }
    
    // Resultados de libros
    if (count($resultadosLibros) > 0) {
        $content .= '
        <div class="card">
            <h3>📚 Libros encontrados (' . count($resultadosLibros) . ')</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Año</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($resultadosLibros as $libro) {
            $libroCompleto = $libroServices->getLibroCompleto($libro->getId());
            $autor = $libroCompleto['autor'];
            $disponible = $libroCompleto['disponible'];
            
            $estadoColor = $disponible ? '#27ae60' : '#e74c3c';
            $estadoTexto = $disponible ? '✅ Disponible' : '📖 Prestado';
            
            $content .= '
                    <tr>
                        <td><strong>' . htmlspecialchars($libro->getTitulo()) . '</strong></td>
                        <td>' . htmlspecialchars($autor->getNombreCompleto()) . '</td>
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
        </div>';
    }
    
    // Resultados de usuarios
    if (count($resultadosUsuarios) > 0) {
        $content .= '
        <div class="card">
            <h3>👥 Usuarios encontrados (' . count($resultadosUsuarios) . ')</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($resultadosUsuarios as $usuario) {
            $estadoColor = $usuario->isActivo() ? '#27ae60' : '#e74c3c';
            $estadoTexto = $usuario->isActivo() ? '✅ Activo' : '❌ Inactivo';
            
            $content .= '
                    <tr>
                        <td><strong>' . htmlspecialchars($usuario->getNombreCompleto()) . '</strong></td>
                        <td>' . htmlspecialchars($usuario->getEmail()) . '</td>
                        <td>' . htmlspecialchars($usuario->getTelefono()) . '</td>
                        <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                        <td>
                            <a href="historial-usuario.php?id=' . $usuario->getId() . '" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #3498db;">📚 Historial</a>
                            <a href="nuevo-prestamo.php?usuario=' . $usuario->getId() . '" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">➕ Préstamo</a>
                        </td>
                    </tr>';
        }
        
        $content .= '
                </tbody>
            </table>
        </div>';
    }
    
    // Resultados de préstamos
    if (count($resultadosPrestamos) > 0) {
        $content .= '
        <div class="card">
            <h3>📖 Préstamos encontrados (' . count($resultadosPrestamos) . ')</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Libro</th>
                        <th>Fecha Préstamo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($resultadosPrestamos as $prestamoData) {
            $prestamo = $prestamoData['prestamo'];
            $usuario = $prestamoData['usuario'];
            $libro = $prestamoData['libro'];
            
            $estadoColor = '';
            $estadoTexto = $prestamo->getEstado();
            
            switch ($prestamo->getEstado()) {
                case 'activo':
                    $estadoColor = $prestamo->estaVencido() ? '#e74c3c' : '#f39c12';
                    $estadoTexto = $prestamo->estaVencido() ? '⚠️ Vencido' : '📖 Activo';
                    break;
                case 'devuelto':
                    $estadoColor = '#27ae60';
                    $estadoTexto = '✅ Devuelto';
                    break;
            }
            
            $content .= '
                    <tr>
                        <td>' . htmlspecialchars($usuario->getNombreCompleto()) . '</td>
                        <td>' . htmlspecialchars($libro->getTitulo()) . '</td>
                        <td>' . $prestamo->getFechaPrestamo() . '</td>
                        <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                        <td>
                            <a href="historial-usuario.php?id=' . $usuario->getId() . '" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #3498db;">👤 Usuario</a>';
            
            if ($prestamo->getEstado() === 'activo') {
                $content .= '
                            <form method="POST" action="prestamos.php" style="display: inline;">
                                <input type="hidden" name="accion" value="devolver_libro">
                                <input type="hidden" name="id_prestamo" value="' . $prestamo->getId() . '">
                                <button type="submit" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">📚 Devolver</button>
                            </form>';
            }
            
            $content .= '
                        </td>
                    </tr>';
        }
        
        $content .= '
                </tbody>
            </table>
        </div>';
    }
    
    // Si no hay resultados
    if ($totalResultados === 0) {
        $content .= '
        <div class="card">
            <div style="text-align: center; padding: 2rem;">
                <h3>😔 No se encontraron resultados</h3>
                <p>No se encontraron elementos que coincidan con "<strong>' . htmlspecialchars($termino) . '</strong>"</p>
                <p>Intenta con:</p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Términos más generales</li>
                    <li>Verificar la ortografía</li>
                    <li>Buscar por nombre, apellido o título completo</li>
                    <li>Cambiar el tipo de búsqueda</li>
                </ul>
            </div>
        </div>';
    }
} else {
    // Mostrar sugerencias cuando no hay búsqueda
    $content .= '
    <div class="card">
        <h3>💡 Sugerencias de búsqueda</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div style="padding: 1rem; background-color: #f8f9fa; border-radius: 4px;">
                <h4>📚 Buscar libros</h4>
                <p>Busca por título, autor o año de publicación</p>
                <small>Ejemplo: "García Márquez", "1984", "Quijote"</small>
            </div>
            
            <div style="padding: 1rem; background-color: #f8f9fa; border-radius: 4px;">
                <h4>👥 Buscar usuarios</h4>
                <p>Busca por nombre, apellido o email</p>
                <small>Ejemplo: "Juan", "Pérez", "juan@email.com"</small>
            </div>
            
            <div style="padding: 1rem; background-color: #f8f9fa; border-radius: 4px;">
                <h4>📖 Buscar préstamos</h4>
                <p>Busca préstamos por usuario o libro</p>
                <small>Ejemplo: "María González", "El principito"</small>
            </div>
        </div>
    </div>';
}

$content .= '
<!-- Acciones rápidas -->
<div class="card">
    <h3>🔗 Acciones Rápidas</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="index.php" class="btn">🏠 Dashboard</a>
        <a href="libros.php" class="btn btn-success">📚 Gestionar Libros</a>
        <a href="usuarios.php" class="btn" style="background-color: #f39c12;">👥 Gestionar Usuarios</a>
        <a href="prestamos.php" class="btn" style="background-color: #9b59b6;">📖 Gestionar Préstamos</a>
    </div>
</div>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Búsqueda - Sistema de Biblioteca", $content);

?>