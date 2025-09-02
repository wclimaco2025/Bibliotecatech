<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();
$libroServices = new \Climaco\Biblioteca\Service\LibroServices();
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();

// Procesar acciones
$mensaje = '';
$tipoMensaje = '';

if ($_POST) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear_prestamo':
                $resultado = $prestamoServices->crearPrestamo(
                    (int)$_POST['id_usuario'],
                    (int)$_POST['id_libro'],
                    (int)($_POST['dias_prestamo'] ?? 30)
                );
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;

            case 'devolver_libro':
                $resultado = $prestamoServices->devolverLibro(
                    (int)$_POST['id_prestamo'],
                    $_POST['observaciones'] ?? ''
                );
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;

            case 'extender_prestamo':
                $resultado = $prestamoServices->extenderPrestamo(
                    (int)$_POST['id_prestamo'],
                    (int)($_POST['dias_extension'] ?? 7)
                );
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
        }
    }
}

// Obtener datos para mostrar
$prestamosCompletos = $prestamoServices->getPrestamosCompletos();
$prestamosVencidos = $prestamoServices->getPrestamosVencidosCompletos();
$usuarios = $usuarioRepository->getUsuariosActivos();
$librosDisponibles = $libroServices->getLibrosDisponibles();
$estadisticas = $prestamoServices->getEstadisticasPrestamos();

// Contenido de la página
$content = '';

// Mostrar mensaje si existe
if ($mensaje) {
    $content .= '<div class="alert alert-' . $tipoMensaje . '">' . htmlspecialchars($mensaje) . '</div>';
}

$content .= '
<div class="card">
    <h1>📚 Gestión de Préstamos</h1>
    <p>Administra los préstamos de libros del sistema</p>
</div>


<!-- Formulario para crear nuevo préstamo -->
<div class="card">
    <h3>➕ Crear Nuevo Préstamo</h3>
    <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
        <input type="hidden" name="accion" value="crear_prestamo">
        
        <div class="form-group">
            <label for="id_usuario">Usuario:</label>
            <select name="id_usuario" id="id_usuario" class="form-control" required>
                <option value="">Seleccionar usuario...</option>';

foreach ($usuarios as $usuario) {
    $content .= '<option value="' . $usuario->getId() . '">' . htmlspecialchars($usuario->getNombreCompleto()) . '</option>';
}

$content .= '
            </select>
        </div>
        
        <div class="form-group">
            <label for="id_libro">Libro:</label>
            <select name="id_libro" id="id_libro" class="form-control" required>
                <option value="">Seleccionar libro...</option>';

foreach ($librosDisponibles as $libro) {
    $content .= '<option value="' . $libro->getId() . '">' . htmlspecialchars($libro->getTitulo()) . ' (' . htmlspecialchars($libro->getYear()) . ')</option>';
}

$content .= '
            </select>
        </div>
        
        <div class="form-group">
            <label for="dias_prestamo">Días de préstamo:</label>
            <input type="number" name="dias_prestamo" id="dias_prestamo" class="form-control" value="15" min="1" max="30">
        </div>
        
        <button type="submit" class="btn btn-success">Crear Préstamo</button>
    </form>
</div>';

// Mostrar préstamos vencidos si existen
if (count($prestamosVencidos) > 0) {
    $content .= '
    <div class="card">
        <h3 style="color: #e74c3c;">🚨 Préstamos Vencidos</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Libro</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Esperada</th>
                    <th>Días de Retraso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($prestamosVencidos as $prestamoData) {
        $prestamo = $prestamoData['prestamo'];
        $usuario = $prestamoData['usuario'];
        $libro = $prestamoData['libro'];
        $diasRetraso = $prestamoData['dias_retraso'];

        $content .= '
                <tr style="background-color: #fdf2f2;">
                    <td>' . $prestamo->getId() . '</td>
                    <td>' . htmlspecialchars($usuario->getNombreCompleto()) . '</td>
                    <td>' . htmlspecialchars($libro->getTitulo()) . '</td>
                    <td>' . $prestamo->getFechaPrestamo() . '</td>
                    <td>' . $prestamo->getFechaDevolucionEsperada() . '</td>
                    <td><strong style="color: #e74c3c;">' . $diasRetraso . ' días</strong></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="accion" value="devolver_libro">
                            <input type="hidden" name="id_prestamo" value="' . $prestamo->getId() . '">
                            <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">Devolver</button>
                        </form>
                    </td>
                </tr>';
    }

    $content .= '
            </tbody>
        </table>
    </div>';
}

// Mostrar todos los préstamos
$content .= '
<div class="card">
    <h3>📋 Todos los Préstamos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Libro</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Esperada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

foreach ($prestamosCompletos as $prestamoData) {
    $prestamo = $prestamoData['prestamo'];
    $usuario = $prestamoData['usuario'];
    $libro = $prestamoData['libro'];

    $estadoColor = '';
    $estadoTexto = $prestamo->getEstado();

    switch ($prestamo->getEstado()) {
        case 'activo':
            $estadoColor = $prestamo->estaVencido() ? '#e74c3c' : '#f39c12';
            $estadoTexto = $prestamo->estaVencido() ? 'Vencido' : 'Activo';
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

    $content .= '
            <tr>
                <td>' . $prestamo->getId() . '</td>
                <td>' . htmlspecialchars($usuario->getNombreCompleto()) . '</td>
                <td>' . htmlspecialchars($libro->getTitulo()) . '</td>
                <td>' . $prestamo->getFechaPrestamo() . '</td>
                <td>' . $prestamo->getFechaDevolucionEsperada() . '</td>
                <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>
                <td>';

    if ($prestamo->getEstado() === 'activo') {
        $content .= '
                    <form method="POST" style="display: inline; margin-right: 0.5rem;">
                        <input type="hidden" name="accion" value="devolver_libro">
                        <input type="hidden" name="id_prestamo" value="' . $prestamo->getId() . '">
                        <button type="submit" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">Devolver</button>
                    </form>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="accion" value="extender_prestamo">
                        <input type="hidden" name="id_prestamo" value="' . $prestamo->getId() . '">
                        <input type="hidden" name="dias_extension" value="7">
                        <button type="submit" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #f39c12;">+7 días</button>
                    </form>';
    } else {
        $content .= '<span style="color: #7f8c8d;">-</span>';
    }

    $content .= '
                </td>
            </tr>';
}

$content .= '
        </tbody>
    </table>
</div>

<!-- Acciones rápidas -->
<div class="card">
    <h3>🔗 Acciones Rápidas</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="index.php" class="btn">🏠 Volver al Dashboard</a>
        <a href="libros.php" class="btn btn-success">📚 Ver Libros</a>
        <a href="usuarios.php" class="btn" style="background-color: #f39c12;">👥 Ver Usuarios</a>
        <button onclick="window.print()" class="btn" style="background-color: #9b59b6;">🖨️ Imprimir</button>
    </div>
</div>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Gestión de Préstamos - Sistema de Biblioteca", $content);
