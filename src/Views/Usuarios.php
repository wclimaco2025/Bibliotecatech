<?php
namespace Climaco\Biblioteca;

class UsuariosView {
    
    public static function renderTablaUsuarios($usuarios, $prestamoServices) {
        $html = '
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
            
            $html .= '
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
                $html .= '<br><small style="color: #f39c12;">(' . $prestamosActivos . ' activos)</small>';
            }
            
            $html .= '
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">';
            
            // Botón editar
            $html .= '
                            <button onclick="editarUsuario(' . $usuario->getId() . ', \'' . htmlspecialchars($usuario->getEmail()) . '\', \'' . htmlspecialchars($usuario->getTelefono()) . '\')" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #f39c12;">✏️</button>';
            
            // Botón activar/desactivar
            if ($usuario->isActivo()) {
                $html .= '
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="accion" value="desactivar_usuario">
                                <input type="hidden" name="id_usuario" value="' . $usuario->getId() . '">
                                <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;" onclick="return confirm(\'¿Desactivar usuario?\')">❌</button>
                            </form>';
            } else {
                $html .= '
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="accion" value="activar_usuario">
                                <input type="hidden" name="id_usuario" value="' . $usuario->getId() . '">
                                <button type="submit" class="btn btn-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">✅</button>
                            </form>';
            }
            
            // Botón nuevo préstamo
            $html .= '
                            <a href="nuevo-prestamo.php?usuario=' . $usuario->getId() . '" class="btn" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; background-color: #9b59b6;">📖</a>
                        </div>
                    </td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
        
        return $html;
    }
    
    public static function renderFormularioNuevoUsuario() {
        return '
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
        </form>';
    }
    
    public static function renderModalEditar() {
        return '
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
        </div>';
    }
    
    public static function renderEstadisticasUsuarios($totalUsuarios, $totalActivos) {
        return '
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
                <h4>👥 Total Usuarios</h4>
                <h2>' . $totalUsuarios . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
                <h4>✅ Usuarios Activos</h4>
                <h2>' . $totalActivos . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                <h4>❌ Usuarios Inactivos</h4>
                <h2>' . ($totalUsuarios - $totalActivos) . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
                <h4>📊 Tasa de Actividad</h4>
                <h2>' . ($totalUsuarios > 0 ? round(($totalActivos / $totalUsuarios) * 100) : 0) . '%</h2>
            </div>
        </div>';
    }
}
?>