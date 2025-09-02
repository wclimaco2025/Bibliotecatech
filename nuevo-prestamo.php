<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();
$libroServices = new \Climaco\Biblioteca\Service\LibroServices();
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();
$autorRepository = new \Climaco\Biblioteca\Repository\AutorRepository();

// Procesar formulario
$mensaje = '';
$tipoMensaje = '';

if ($_POST && isset($_POST['crear_prestamo'])) {
    $resultado = $prestamoServices->crearPrestamo(
        (int)$_POST['id_usuario'],
        (int)$_POST['id_libro'],
        (int)($_POST['dias_prestamo'] ?? 15)
    );
    $mensaje = $resultado['mensaje'];
    $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
    
    if ($resultado['exito']) {
        $prestamo = $resultado['prestamo'];
        $usuario = $usuarioRepository->getUsuariosPorId($prestamo->getIdUsuario());
        $libro = $libroServices->getLibro($prestamo->getIdLibro());
        
        $mensaje .= '<br><strong>Detalles del préstamo:</strong><br>';
        $mensaje .= 'ID: ' . $prestamo->getId() . '<br>';
        $mensaje .= 'Usuario: ' . $usuario->getNombreCompleto() . '<br>';
        $mensaje .= 'Libro: ' . $libro->getTitulo() . '<br>';
        $mensaje .= 'Fecha de devolución: ' . $prestamo->getFechaDevolucionEsperada();
    }
}

// Obtener datos
$usuarios = $usuarioRepository->getUsuariosActivos();
$librosConInfo = $libroServices->getLibrosConDisponibilidad();

// Contenido de la página
$content = '';

// Mostrar mensaje si existe
if ($mensaje) {
    $content .= '<div class="alert alert-' . $tipoMensaje . '">' . $mensaje . '</div>';
}

$content .= '
<div class="card">
    <h1>➕ Crear Nuevo Préstamo</h1>
    <p>Registra un nuevo préstamo de libro en el sistema</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Formulario de préstamo -->
    <div class="card">
        <h3>📝 Datos del Préstamo</h3>
        <form method="POST">
            <div class="form-group">
                <label for="id_usuario">👤 Usuario:</label>
                <select name="id_usuario" id="id_usuario" class="form-control" required onchange="mostrarInfoUsuario(this.value)">
                    <option value="">Seleccionar usuario...</option>';

foreach ($usuarios as $usuario) {
    $content .= '<option value="' . $usuario->getId() . '" data-email="' . htmlspecialchars($usuario->getEmail()) . '" data-telefono="' . htmlspecialchars($usuario->getTelefono()) . '">' . htmlspecialchars($usuario->getNombreCompleto()) . '</option>';
}

$content .= '
                </select>
                <div id="info-usuario" style="margin-top: 0.5rem; padding: 0.5rem; background-color: #f8f9fa; border-radius: 4px; display: none;">
                    <small id="usuario-detalles"></small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="id_libro">📚 Libro:</label>
                <select name="id_libro" id="id_libro" class="form-control" required onchange="mostrarInfoLibro(this.value)">
                    <option value="">Seleccionar libro...</option>';

foreach ($librosConInfo as $libroInfo) {
    $libro = $libroInfo['libro'];
    $autor = $libroInfo['autor'];
    $disponible = $libroInfo['disponible'];
    
    if ($disponible) {
        $content .= '<option value="' . $libro->getId() . '" data-autor="' . htmlspecialchars($autor->getNombreCompleto()) . '" data-year="' . htmlspecialchars($libro->getYear()) . '">' . htmlspecialchars($libro->getTitulo()) . '</option>';
    }
}

$content .= '
                </select>
                <div id="info-libro" style="margin-top: 0.5rem; padding: 0.5rem; background-color: #f8f9fa; border-radius: 4px; display: none;">
                    <small id="libro-detalles"></small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="dias_prestamo">📅 Días de préstamo:</label>
                <select name="dias_prestamo" id="dias_prestamo" class="form-control" onchange="calcularFechaDevolucion()">
                    <option value="7">7 días</option>
                    <option value="15" selected>15 días (Estándar)</option>
                    <option value="21">21 días</option>
                    <option value="30">30 días</option>
                </select>
                <small class="form-text text-muted">Fecha de devolución: <span id="fecha-devolucion"></span></small>
            </div>
            
            <div class="form-group">
                <label for="observaciones">📝 Observaciones (opcional):</label>
                <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Notas adicionales sobre el préstamo..."></textarea>
            </div>
            
            <button type="submit" name="crear_prestamo" class="btn btn-success" style="width: 100%;">✅ Crear Préstamo</button>
        </form>
    </div>
    
    <!-- Información y estadísticas -->
    <div>
        <div class="card">
            <h3>📊 Estadísticas Rápidas</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="text-align: center; padding: 1rem; background-color: #e8f5e8; border-radius: 4px;">
                    <h4 style="margin: 0; color: #27ae60;">' . count($librosConInfo) . '</h4>
                    <small>Total Libros</small>
                </div>
                <div style="text-align: center; padding: 1rem; background-color: #e8f4fd; border-radius: 4px;">
                    <h4 style="margin: 0; color: #3498db;">' . count(array_filter($librosConInfo, function($l) { return $l['disponible']; })) . '</h4>
                    <small>Disponibles</small>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3>📚 Libros Más Populares</h3>
            <p><small>Libros con más préstamos históricos</small></p>
            <ul>';

// Obtener algunos libros populares (simulado)
$librosPopulares = array_slice($librosConInfo, 0, 5);
foreach ($librosPopulares as $libroInfo) {
    $libro = $libroInfo['libro'];
    $autor = $libroInfo['autor'];
    $disponible = $libroInfo['disponible'];
    
    $estado = $disponible ? '✅ Disponible' : '❌ Prestado';
    $color = $disponible ? '#27ae60' : '#e74c3c';
    
    $content .= '<li style="margin-bottom: 0.5rem;">
        <strong>' . htmlspecialchars($libro->getTitulo()) . '</strong><br>
        <small>' . htmlspecialchars($autor->getNombreCompleto()) . ' (' . htmlspecialchars($libro->getYear()) . ')</small><br>
        <small style="color: ' . $color . ';">' . $estado . '</small>
    </li>';
}

$content .= '
            </ul>
        </div>
        
        <div class="card">
            <h3>🔗 Acciones Rápidas</h3>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="prestamos.php" class="btn">📋 Ver Todos los Préstamos</a>
                <a href="libros.php" class="btn btn-success">📚 Gestionar Libros</a>
                <a href="usuarios.php" class="btn" style="background-color: #f39c12;">👥 Gestionar Usuarios</a>
                <a href="index.php" class="btn" style="background-color: #9b59b6;">🏠 Volver al Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarInfoUsuario(idUsuario) {
    const select = document.getElementById("id_usuario");
    const infoDiv = document.getElementById("info-usuario");
    const detallesSpan = document.getElementById("usuario-detalles");
    
    if (idUsuario) {
        const option = select.querySelector(`option[value="${idUsuario}"]`);
        const email = option.getAttribute("data-email");
        const telefono = option.getAttribute("data-telefono");
        
        detallesSpan.innerHTML = `📧 ${email}<br>📞 ${telefono}`;
        infoDiv.style.display = "block";
    } else {
        infoDiv.style.display = "none";
    }
}

function mostrarInfoLibro(idLibro) {
    const select = document.getElementById("id_libro");
    const infoDiv = document.getElementById("info-libro");
    const detallesSpan = document.getElementById("libro-detalles");
    
    if (idLibro) {
        const option = select.querySelector(`option[value="${idLibro}"]`);
        const autor = option.getAttribute("data-autor");
        const year = option.getAttribute("data-year");
        
        detallesSpan.innerHTML = `✍️ Autor: ${autor}<br>📅 Año: ${year}`;
        infoDiv.style.display = "block";
    } else {
        infoDiv.style.display = "none";
    }
}

function calcularFechaDevolucion() {
    const dias = parseInt(document.getElementById("dias_prestamo").value);
    const fechaActual = new Date();
    fechaActual.setDate(fechaActual.getDate() + dias);
    
    const fechaDevolucion = fechaActual.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });
    
    document.getElementById("fecha-devolucion").textContent = fechaDevolucion;
}

// Calcular fecha inicial
document.addEventListener("DOMContentLoaded", function() {
    calcularFechaDevolucion();
});
</script>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Nuevo Préstamo - Sistema de Biblioteca", $content);

?>