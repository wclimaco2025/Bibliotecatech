<?php

namespace Climaco\Biblioteca;

// Cargar todas las clases del sistema
require_once 'vendor/autoload.php';

// Crear instancias de los servicios
$usuarioRepository = new \Climaco\Biblioteca\Repository\UsuarioRepository();
$prestamoServices = new \Climaco\Biblioteca\Service\PrestamoServices();

// Procesar acciones
$mensaje = '';
$tipoMensaje = '';

if ($_POST) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear_usuario':
                try {
                    // Generar nuevo ID
                    $usuarios = $usuarioRepository->getUsuarios();
                    $maxId = 0;
                    foreach ($usuarios as $u) {
                        if ($u->getId() > $maxId) {
                            $maxId = $u->getId();
                        }
                    }
                    $nuevoId = $maxId + 1;

                    $nuevoUsuario = new Usuario(
                        $nuevoId,
                        $_POST['nombre'],
                        $_POST['apellido'],
                        $_POST['email'],
                        $_POST['telefono']
                    );

                    $usuarioRepository->agregarUsuario($nuevoUsuario);
                    $mensaje = 'Usuario creado exitosamente: ' . $nuevoUsuario->getNombreCompleto();
                    $tipoMensaje = 'success';
                } catch (\Exception $e) {
                    $mensaje = 'Error al crear usuario: ' . $e->getMessage();
                    $tipoMensaje = 'danger';
                }
                break;

            case 'activar_usuario':
                if ($usuarioRepository->activarUsuario((int)$_POST['id_usuario'])) {
                    $mensaje = 'Usuario activado exitosamente';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Error al activar usuario';
                    $tipoMensaje = 'danger';
                }
                break;

            case 'desactivar_usuario':
                if ($usuarioRepository->desactivarUsuario((int)$_POST['id_usuario'])) {
                    $mensaje = 'Usuario desactivado exitosamente';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Error al desactivar usuario';
                    $tipoMensaje = 'danger';
                }
                break;

            case 'editar_usuario':
                $usuario = $usuarioRepository->getUsuariosPorId((int)$_POST['id_usuario']);
                if ($usuario) {
                    $usuario->setEmail($_POST['email']);
                    $usuario->setTelefono($_POST['telefono']);
                    $mensaje = 'Usuario actualizado exitosamente';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Usuario no encontrado';
                    $tipoMensaje = 'danger';
                }
                break;
        }
    }
}

// Obtener datos
$usuarios = $usuarioRepository->getUsuarios();
$usuariosActivos = $usuarioRepository->getUsuariosActivos();
$totalUsuarios = $usuarioRepository->getTotalUsuarios();
$totalActivos = $usuarioRepository->getTotalUsuariosActivos();

// Contenido de la página
$content = '';

// Mostrar mensaje si existe
if ($mensaje) {
    $content .= '<div class="alert alert-' . $tipoMensaje . '">' . htmlspecialchars($mensaje) . '</div>';
}

$content .= '
<div class="card">
    <h1>👥 Gestión de Usuarios</h1>
    <p>Administra los usuarios del sistema de biblioteca</p>
</div>

<!-- Formulario para crear nuevo usuario -->
<div class="card">
    <h3>➕ Crear Nuevo Usuario</h3>
    <form method="POST" id="form-nuevo-usuario">
        <input type="hidden" name="accion" value="crear_usuario">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" required>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-success">✅ Crear Usuario</button>
        <button type="reset" class="btn" style="background-color: #95a5a6;">🔄 Limpiar</button>
    </form>
</div>

<!-- Lista de usuarios -->
<div class="card">
    <h3>📋 Lista de Usuarios</h3>
    <div style="margin-bottom: 1rem;">
        <input type="text" id="buscar-usuario" placeholder="🔍 Buscar usuario..." class="form-control" style="max-width: 300px;" onkeyup="filtrarUsuarios()">
    </div>
    
    <table class="table" id="tabla-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha Registro</th>
                <th>Estado</th>
                <th>Préstamos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

foreach ($usuarios as $usuario) {
    $estadoColor = $usuario->isActivo() ? '#27ae60' : '#e74c3c';
    $estadoTexto = $usuario->isActivo() ? 'Activo' : 'Inactivo';
    $estadoIcono = $usuario->isActivo() ? '✅' : '❌';

    // Contar préstamos del usuario
    $historial = $prestamoServices->getHistorialUsuario($usuario->getId());
    $prestamosActivos = 0;
    foreach ($historial as $item) {
        if ($item['prestamo']->getEstado() === 'activo') {
            $prestamosActivos++;
        }
    }

    $content .= '
            <tr data-nombre="' . strtolower($usuario->getNombreCompleto()) . '" data-email="' . strtolower($usuario->getEmail()) . '">
                <td>' . $usuario->getId() . '</td>
                <td><strong>' . htmlspecialchars($usuario->getNombreCompleto()) . '</strong></td>
                <td>' . htmlspecialchars($usuario->getEmail()) . '</td>
                <td>' . htmlspecialchars($usuario->getTelefono()) . '</td>
                <td>' . $usuario->getFechaRegistro() . '</td>
                <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoIcono . ' ' . $estadoTexto . '</span></td>
                <td>
                    <a href="historial-usuario.php?id=' . $usuario->getId() . '" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #3498db;">
                        📚 ' . count($historial) . ' total
                    </a>';

    if ($prestamosActivos > 0) {
        $content .= '<br><small style="color: #f39c12;">(' . $prestamosActivos . ' activos)</small>';
    }

    $content .= '
                </td>
                <td>
                    <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">';

    // Botón editar
    $content .= '
                        <button onclick="editarUsuario(' . $usuario->getId() . ', \'' . htmlspecialchars($usuario->getEmail()) . '\', \'' . htmlspecialchars($usuario->getTelefono()) . '\')" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #f39c12;">✏️</button>';

    // Botón activar/desactivar
    if ($usuario->isActivo()) {
        $content .= '
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="accion" value="desactivar_usuario">
                            <input type="hidden" name="id_usuario" value="' . $usuario->getId() . '">
                            <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;" onclick="return confirm(\'¿Desactivar usuario?\')">❌</button>
                        </form>';
    } else {
        $content .= '
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="accion" value="activar_usuario">
                            <input type="hidden" name="id_usuario" value="' . $usuario->getId() . '">
                            <button type="submit" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">✅</button>
                        </form>';
    }

    // Botón nuevo préstamo
    $content .= '
                        <a href="nuevo-prestamo.php?usuario=' . $usuario->getId() . '" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #9b59b6;">📖</a>
                    </div>
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
        <a href="index.php" class="btn">🏠 Dashboard</a>
        <a href="prestamos.php" class="btn btn-success">📚 Gestionar Préstamos</a>
        <a href="libros.php" class="btn" style="background-color: #f39c12;">📖 Gestionar Libros</a>
        <button onclick="window.print()" class="btn" style="background-color: #34495e;">🖨️ Imprimir Lista</button>
    </div>
</div>

<!-- Modal para editar usuario -->
<div id="modal-editar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
        <h3>✏️ Editar Usuario</h3>
        <form method="POST" id="form-editar-usuario">
            <input type="hidden" name="accion" value="editar_usuario">
            <input type="hidden" name="id_usuario" id="edit-id">
            
            <div class="form-group">
                <label for="edit-email">Email:</label>
                <input type="email" name="email" id="edit-email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit-telefono">Teléfono:</label>
                <input type="tel" name="telefono" id="edit-telefono" class="form-control" required>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="cerrarModal()" class="btn" style="background-color: #95a5a6;">Cancelar</button>
                <button type="submit" class="btn btn-success">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function filtrarUsuarios() {
    const busqueda = document.getElementById("buscar-usuario").value.toLowerCase();
    const filas = document.querySelectorAll("#tabla-usuarios tbody tr");
    
    filas.forEach(fila => {
        const nombre = fila.getAttribute("data-nombre");
        const email = fila.getAttribute("data-email");
        
        if (nombre.includes(busqueda) || email.includes(busqueda)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

function editarUsuario(id, email, telefono) {
    document.getElementById("edit-id").value = id;
    document.getElementById("edit-email").value = email;
    document.getElementById("edit-telefono").value = telefono;
    document.getElementById("modal-editar").style.display = "block";
}

function cerrarModal() {
    document.getElementById("modal-editar").style.display = "none";
}

// Cerrar modal al hacer clic fuera
document.getElementById("modal-editar").addEventListener("click", function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>';

// Renderizar la página
\Climaco\Biblioteca\Views\Layout\Template::render("Gestión de Usuarios - Sistema de Biblioteca", $content);
