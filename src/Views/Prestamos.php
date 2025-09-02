<?php

namespace Climaco\Biblioteca;

class PrestamosView
{

    public static function renderListaPrestamos($prestamos, $mostrarAcciones = true)
    {
        $html = '
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Libro</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Esperada</th>
                    <th>Estado</th>';

        if ($mostrarAcciones) {
            $html .= '<th>Acciones</th>';
        }

        $html .= '
                </tr>
            </thead>
            <tbody>';

        foreach ($prestamos as $prestamoData) {
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

            $html .= '
                <tr>
                    <td>' . $prestamo->getId() . '</td>
                    <td>' . htmlspecialchars($usuario->getNombreCompleto()) . '</td>
                    <td>' . htmlspecialchars($libro->getTitulo()) . '</td>
                    <td>' . $prestamo->getFechaPrestamo() . '</td>
                    <td>' . $prestamo->getFechaDevolucionEsperada() . '</td>
                    <td><span style="color: ' . $estadoColor . '; font-weight: bold;">' . $estadoTexto . '</span></td>';

            if ($mostrarAcciones) {
                $html .= '<td>';

                if ($prestamo->getEstado() === 'activo') {
                    $html .= '
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
                    $html .= '<span style="color: #7f8c8d;">-</span>';
                }

                $html .= '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '
            </tbody>
        </table>';

        return $html;
    }

    public static function renderFormularioNuevoPrestamo($usuarios, $libros)
    {
        $html = '
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <input type="hidden" name="accion" value="crear_prestamo">
            
            <div class="form-group">
                <label for="id_usuario">Usuario:</label>
                <select name="id_usuario" id="id_usuario" class="form-control" required>
                    <option value="">Seleccionar usuario...</option>';

        foreach ($usuarios as $usuario) {
            $html .= '<option value="' . $usuario->getId() . '">' . htmlspecialchars($usuario->getNombreCompleto()) . '</option>';
        }

        $html .= '
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_libro">Libro:</label>
                <select name="id_libro" id="id_libro" class="form-control" required>
                    <option value="">Seleccionar libro...</option>';

        foreach ($libros as $libro) {
            $html .= '<option value="' . $libro->getId() . '">' . htmlspecialchars($libro->getTitulo()) . ' (' . htmlspecialchars($libro->getYear()) . ')</option>';
        }

        $html .= '
                </select>
            </div>
            
            <div class="form-group">
                <label for="dias_prestamo">Días de préstamo:</label>
                <input type="number" name="dias_prestamo" id="dias_prestamo" class="form-control" value="15" min="1" max="30">
            </div>
            
            <button type="submit" class="btn btn-success">Crear Préstamo</button>
        </form>';

        return $html;
    }

    public static function renderEstadisticas($estadisticas)
    {
        return '
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
                <h4>📖 Préstamos Activos</h4>
                <h2>' . $estadisticas['prestamos_activos'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                <h4>⚠️ Préstamos Vencidos</h4>
                <h2>' . $estadisticas['prestamos_vencidos'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #27ae60, #229954); color: white;">
                <h4>📚 Total Libros</h4>
                <h2>' . $estadisticas['total_libros'] . '</h2>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
                <h4>👥 Usuarios Activos</h4>
                <h2>' . $estadisticas['usuarios_activos'] . '</h2>
            </div>
        </div>';
    }

    public static function renderPrestamosVencidos($prestamosVencidos)
    {
        if (count($prestamosVencidos) === 0) {
            return '<div class="alert alert-success">✅ No hay préstamos vencidos</div>';
        }

        $html = '
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

            $html .= '
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

        $html .= '
                </tbody>
            </table>
        </div>';

        return $html;
    }
}
