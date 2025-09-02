<?php
namespace Climaco\Biblioteca\Views\Layout;

class Template {
    
    public static function render($title = "Sistema de Biblioteca", $content = "", $additionalCSS = "") {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($title); ?></title>
            <style>
                * {
                    box-sizing: border-box;
                }
                
                body {
                    margin: 0;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                }
                
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 2rem;
                }
                
                .card {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    padding: 2rem;
                    margin: 1rem 0;
                }
                
                .btn {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    background-color: #3498db;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }
                
                .btn:hover {
                    background-color: #2980b9;
                }
                
                .btn-success {
                    background-color: #27ae60;
                }
                
                .btn-success:hover {
                    background-color: #229954;
                }
                
                .btn-danger {
                    background-color: #e74c3c;
                }
                
                .btn-danger:hover {
                    background-color: #c0392b;
                }
                
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 1rem 0;
                }
                
                .table th,
                .table td {
                    padding: 0.75rem;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                
                .table th {
                    background-color: #f8f9fa;
                    font-weight: 600;
                }
                
                .table tr:hover {
                    background-color: #f8f9fa;
                }
                
                .alert {
                    padding: 1rem;
                    margin: 1rem 0;
                    border-radius: 4px;
                }
                
                .alert-success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                
                .alert-danger {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
                
                .form-group {
                    margin-bottom: 1rem;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 500;
                }
                
                .form-control {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 1rem;
                }
                
                .form-control:focus {
                    outline: none;
                    border-color: #3498db;
                    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
                }
                
                <?php echo $additionalCSS; ?>
            </style>
        </head>
        <body>
            <?php include(__DIR__ . "/Header.php"); ?>
            
            <main>
                <div class="container">
                    <?php echo $content; ?>
                </div>
            </main>
            
            <?php include(__DIR__ . "/Footer.php"); ?>
        </body>
        </html>
        <?php
    }
    
    public static function renderWithContent($title, $contentFile, $data = []) {
        // Extraer variables para usar en la vista
        extract($data);
        
        // Capturar el contenido de la vista
        ob_start();
        include($contentFile);
        $content = ob_get_clean();
        
        // Renderizar con el template
        self::render($title, $content);
    }
}
?>