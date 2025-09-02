<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();

// Obtener ID del usuario
$idUsuario = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idUsuario === 0) {
    header('Location: usuarios.php');
    exit;
}

// Obtener datos del usuario
$usuario = $usuarioRepository->getUsuariosPorId($idUsuario);

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

// Obtener historial de préstamos
$historial = $prestamoServices->getHistorialUsuario($idUsuario);

// Estadísticas del usuario
$prestamosActivos = 0;
$prestamosDevueltos = 0;
$prestamosVencidos = 0;

foreach ($historial as $item) {
    $prestamo = $item['prestamo'];
    switch ($prestamo->getEstado()) {
        case 'activo':
            if ($prestamo->estaVencido()) {
                $prestamosVencidos++;
            } else {
                $prestamosActivos++;
            }
            break;
        case 'devuelto':
            $prestamosDevueltos++;
            break;
        case 'vencido':
            $prestamosVencidos++;
            break;
    }
}

// Contenido de la página
$content = '
<div class="card">
    <h1>📋 Historial de Préstamos</h1>
    <h2>👤 ' . htmlspecialchars($usuario->getNombreCompleto()) . '</h2>
    <p>
        📧 ' . htmlspecialchars($usuario->getEmail()) . ' | 
        📞 ' . htmlspecialchars($usuario->getTelefono()) . ' | 
        📅 Registrado: ' . $usuario->getFechaRegistro() . '
    </p>
</div>

<!-- Estadísticas del usuario -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
        <h4>📖 Préstamos Activos</h4>
        <h2>' . $prestamosActivos . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
        <h4>✅ Préstamos Devueltos</h4>
        <h2>' . $prestamosDevueltos . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
        <h4>⚠️ Préstamos Vencidos</h4>
        <h2>' . $prestamosVencidos . '</h2>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
        <h4>📚 Total Préstamos</h4>
        <h2>' . count($historial) . '</h2>
    </div>
</div>';

if (count($historial) > 0) {
    $content .= '
    <div class="card">
        <h3>📚 Historial Completo de Préstamos</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libro</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Esperada</th>
                    <th>Fecha Devolución</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($historial as $item) {
        $prestamo = $item['prestamo'];
        $libro = $item['libro'];
        
        $estadoColor = '';
        $estadoTexto = $prestamo->getEstado();
        
        switch ($prestamo->getEstado()) {
            case 'activo':
                $estadoColor = $prestamo->estaVencido() ? '#e74c3c' : '#f39c12';
                $estadoTexto = $prestamo->estaVencido() ? 'Vencido (' . $prestamo->getDiasRetraso() . ' días)' : 'Activo';
                break;
            case 'devuelto':
                $estadoColor = '#27ae60';
                $estadoTexto = 'Devuelto';
                break;
            case 'vencido':
                $estadoColor = '#e74c3c';
                $estadoTexto = 'Vencido';
                break;
        }
        
        $fechaDevolucion = $prestamo->getFechaDevolucionReal() ?: '-';
        $observaciones = $prestamo->getObservaciones() ?: '-';
        
        $content .= '
                <tr>
                    <td>' . $prestamo->getId() . '</td>
                    <td><strong>' . htmlspecialchars($libro->getTitulo()) . '</strong><br><small>(' . htmlspecialchars($libro->getYear()) . ')</small></td>
                    <td>' . $prestamo->getFechaPrestamo() . '</td>
                    <td>' . $prestamo->getFechaDevolucionEsperada() . '</td>
                    <td>' . $fechaDevolucion . '</td>
                    <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                    <td><small>' . htmlspecialchars($observaciones) . '</small></td>
                </tr>';
    }
    
    $content .= '
            </tbody>
        </table>
    </div>';
} else {
    $content .= '
    <div class="card">
        <h3>📚 Sin Préstamos</h3>
        <p>Este usuario aún no ha realizado ningún préstamo.</p>
        <a href="nuevo-prestamo.php?usuario=' . $usuario->getId() . '" class="btn btn-success">➕ Crear Primer Préstamo</a>
    </div>';
}

$content .= '
<!-- Acciones -->
<div class="card">
    <h3>🔗 Acciones</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="nuevo-prestamo.php?usuario=' . $usuario->getId() . '" class="btn btn-success">➕ Nuevo Préstamo</a>
        <a href="usuarios.php" class="btn">👥 Volver a Usuarios</a>
        <a href="prestamos.php" class="btn" style="background-color: #f39c12;">📋 Ver Todos los Préstamos</a>
        <a href="index.php" class="btn" style="background-color: #9b59b6;">🏠 Dashboard</a>
        <button onclick="window.print()" class="btn" style="background-color: #34495e;">🖨️ Imprimir Historial</button>
    </div>
</div>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Historial de " . $usuario->getNombreCompleto() . " - Sistema de Biblioteca", $content);

?>