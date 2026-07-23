<div class='dashboard'>
    <div class="dashboard-nav">
        <header>
            <span><img src="/admin/images/logo-blanco.png" width="100px"></span>
        </header>
		<div class="user-info">
		<?php
// Verificar si existe la imagen de perfil y si el archivo existe
if (!empty($_SESSION['imagen_perfil']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/uploads/perfiles/' . $_SESSION['imagen_perfil'])) {
    $imagenPerfil = '/admin/uploads/perfiles/' . htmlspecialchars($_SESSION['imagen_perfil']);
} else {
    // Usar la imagen de marcador de posición
    $imagenPerfil = '/admin/images/profile-placeholder.jpeg';
}
?>
<img src="<?php echo $imagenPerfil; ?>" style="width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 15px;
">

			
    <p><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></p>
    <p><?php echo htmlspecialchars($_SESSION['rol']); ?></p>
	
	
		
</div>
		<hr>
        <center>
        <nav class="dashboard-nav-list">
            <a href="/admin/" class="dashboard-nav-item <?php echo (strpos($current_page, '/admin') !== false && strpos($current_page, '/admin/form-list/') === false) ? 'active' : ''; ?>"><i class="fa fa-home"></i> Inicio</a>
            <a href="/admin/form-list/" class="dashboard-nav-item <?php echo (strpos($current_page, '/admin/form-list/') !== false) ? 'active' : ''; ?>"><i class="fa fa-wpforms"></i> Formularios</a>
			
			
			<?php if ($_SESSION['rol'] === 'Administrador'): ?>
<a href="/admin/trash/" class="dashboard-nav-item <?php echo (strpos($current_page, '/admin/trash/') !== false) ? 'active' : ''; ?>"><i class="fas fa-trash-alt"></i> Papelera</a>
<?php endif; ?>
			
			
			 <a href="/admin/generate-excel/" class="dashboard-nav-item <?php echo (strpos($current_page, '/admin/generate-excel/') !== false) ? 'active' : ''; ?>"><i class="fa fa-file-excel fa-lg"></i> Generar Excel</a>
            <!-- Otros elementos del menú -->

            <!-- Mostrar el menú "Usuarios" solo si el rol es Administrador -->
            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
            <div class='dashboard-nav-dropdown'>
                <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle"><i class="fa fa-users"></i> Usuarios</a>
                <div class='dashboard-nav-dropdown-menu'>
                    <a href="/admin/users/" class="dashboard-nav-dropdown-item <?php echo (strpos($current_page, '/admin/users/') !== false) ? 'active' : ''; ?>">Todos</a>
                    <a href="/admin/users/new.php" class="dashboard-nav-dropdown-item <?php echo (strpos($current_page, 'new.php') !== false) ? 'active' : ''; ?>">Nuevo</a>
                    <!--<a href="/admin/users_banned.php" class="dashboard-nav-dropdown-item <?php echo (strpos($current_page, 'users_banned.php') !== false) ? 'active' : ''; ?>">Banned</a>
                    <a href="/admin/users_new.php" class="dashboard-nav-dropdown-item <?php echo (strpos($current_page, 'users_new.php') !== false) ? 'active' : ''; ?>">New</a>-->
                </div>
            </div>
            <?php endif; ?>

            <!-- Continuación del menú -->
            <a href="/admin/profile" class="dashboard-nav-item <?php echo (strpos($current_page, '/admin/profile/') !== false) ? 'active' : ''; ?>"><i class="fa fa-user"></i> Perfil</a>
            
            <div class="nav-item-divider"></div>
            <a href="https://api.whatsapp.com/send?phone=573116023776&text=Hola%2C%20Requiero%20soporte%20sobre%20el%20sistema%20de%20Soluctem" class="dashboard-nav-item" target="_blank"><i class="fa fa-whatsapp"></i> Soporte</a>
			
            <a href="/admin/logout.php" class="dashboard-nav-item"><i class="fa fa-power-off"></i> Salir</a>
        </nav>
    </div>

    <div class='dashboard-app'>
        <header class='dashboard-toolbar'>
            <a href="#!" class="menu-toggle"><i class="fa fa-bars"></i></a>
        </header>
        <div class='dashboard-content'>
            <div class='container'>
                <!-- Aquí va el contenido de la página -->

            