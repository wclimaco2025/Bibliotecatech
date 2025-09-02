<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$libroServices = new \Climaco\Biblioteca\Service\LibroServices();
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();

// Obtener estadísticas para el dashboard
$estadisticas = $prestamoServices->getEstadisticasPrestamos();
$prestamosVencidos = $prestamoServices->getPrestamosVencidosCompletos();
$librosDisponibles = $libroServices->getLibrosDisponibles();

// Contenido del dashboard
$content = '
<div class="card">
    <h1>📊 Dashboard - Sistema de Biblioteca</h1>
    <p>Bienvenido al sistema de gestión de biblioteca Climaco</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 2rem 0;">
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
        <h3>📚 Total Libros</h3>
        <h2>' . $estadisticas['total_libros'] . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
        <h3>👥 Usuarios Activos</h3>
        <h2>' . $estadisticas['usuarios_activos'] . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
        <h3>📖 Préstamos Activos</h3>
        <h2>' . $estadisticas['prestamos_activos'] . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
        <h3>⚠️ Préstamos Vencidos</h3>
        <h2>' . $estadisticas['prestamos_vencidos'] . '</h2>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0;">
    <div class="card">
        <h3>🚨 Préstamos Vencidos</h3>';

if (count($prestamosVencidos) > 0) {
    $content .= '
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Libro</th>
                    <th>Días de Retraso</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($prestamosVencidos as $prestamoVencido) {
        $content .= '
                <tr>
                    <td>' . htmlspecialchars($prestamoVencido['usuario']->getNombreCompleto()) . '</td>
                    <td>' . htmlspecialchars($prestamoVencido['libro']->getTitulo()) . '</td>
                    <td><span style="color: #e74c3c; font-weight: bold;">' . $prestamoVencido['dias_retraso'] . ' días</span></td>
                </tr>';
    }
    
    $content .= '
            </tbody>
        </table>';
} else {
    $content .= '<p style="color: #27ae60;">✅ No hay préstamos vencidos</p>';
}

$content .= '
    </div>
    
    <div class="card">
        <h3>📚 Libros Disponibles</h3>
        <p><strong>' . count($librosDisponibles) . '</strong> libros disponibles para préstamo</p>';

if (count($librosDisponibles) > 0) {
    $content .= '<ul style="max-height: 300px; overflow-y: auto;">';
    foreach (array_slice($librosDisponibles, 0, 10) as $libro) {
        $content .= '<li>' . htmlspecialchars($libro->getTitulo()) . ' (' . htmlspecialchars($libro->getYear()) . ')</li>';
    }
    if (count($librosDisponibles) > 10) {
        $content .= '<li><em>... y ' . (count($librosDisponibles) - 10) . ' más</em></li>';
    }
    $content .= '</ul>';
}

$content .= '
    </div>
</div>

<div class="card">
    <h3>🔗 Acciones Rápidas</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="libros.php" class="btn">Ver Todos los Libros</a>
        <a href="usuarios.php" class="btn btn-success">Gestionar Usuarios</a>
        <a href="prestamos.php" class="btn" style="background-color: #f39c12;">Gestionar Préstamos</a>
        <a href="nuevo-prestamo.php" class="btn btn-success">Nuevo Préstamo</a>
    </div>
</div>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Dashboard - Sistema de Biblioteca", $content);
