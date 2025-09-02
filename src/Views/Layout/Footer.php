<?php

namespace Climaco\Biblioteca; ?>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Biblioteca Climaco. Todos los derechos reservados.</p>
            <p>Desarrollado con PHP - Gestión de Préstamos de Libros</p>
        </div>
    </div>
</footer>

<style>
    .footer {
        background-color: #34495e;
        color: white;
        padding: 2rem 0;
        margin-top: auto;
        text-align: center;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .footer-content p {
        margin: 0.5rem 0;
        font-size: 0.9rem;
    }

    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    main {
        flex: 1;
        padding: 2rem 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
</style>