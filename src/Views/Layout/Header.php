<?php namespace Climaco\Biblioteca; ?>

<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">📚 Sistema de Biblioteca</h1>
            <ul class="nav-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="libros.php">Libros</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="prestamos.php">Préstamos</a></li>
                <li><a href="buscar.php">🔍 Buscar</a></li>
            </ul>
        </div>
    </nav>
</header>

<style>
.header {
    background-color: #2c3e50;
    color: white;
    padding: 1rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
}

.nav-title {
    margin: 0;
    font-size: 1.5rem;
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.nav-menu a:hover {
    background-color: #34495e;
}
</style>