<?php
/**
 * Página principal de MountainConnect
 */
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-body text-center py-5">
                <h1 class="display-4 mb-4">
                    <i class="fas fa-mountain text-success"></i>
                    Bienvenido a <?php echo SITE_NAME; ?>
                </h1>
                <p class="lead mb-4">
                    La comunidad montañera donde compartir tus aventuras
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-user-plus"></i> Únete ahora
                        </a>
                        <a href="login.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-4">
                        <a href="routes/create.php" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-plus-circle"></i> Crear nueva ruta
                        </a>
                        <a href="routes/list.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-list"></i> Ver rutas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-route fa-3x text-primary mb-3"></i>
                <h4>Rutas de Senderismo</h4>
                <p>Descubre y comparte las mejores rutas de montaña con la comunidad</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-hiking fa-3x text-success mb-3"></i>
                <h4>Vías Ferratas</h4>
                <p>Encuentra información sobre ferratas y sus niveles de dificultad</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-mountain fa-3x text-warning mb-3"></i>
                <h4>Escalada</h4>
                <p>Documenta y explora vías de escalada con otros montañeros</p>
            </div>
        </div>
    </div>
</div>

<?php if (isLoggedIn()): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="text-primary"><?php echo count($_SESSION['routes'] ?? []); ?></h3>
                        <p class="text-muted">Rutas creadas</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-success"><?php echo count($_SESSION['users'] ?? []); ?></h3>
                        <p class="text-muted">Usuarios registrados</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-warning">0</h3>
                        <p class="text-muted">Ferratas</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-info">0</h3>
                        <p class="text-muted">Vías de escalada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-5">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-info-circle"></i> ¿Qué es MountainConnect?</h4>
                <p>
                    MountainConnect es una plataforma web diseñada para la comunidad montañera española.
                    Aquí podrás compartir tus experiencias en la montaña, descubrir nuevas rutas,
                    conectar con otros montañeros y documentar tus aventuras.
                </p>
                <ul>
                    <li>Crea y comparte rutas de senderismo con información detallada</li>
                    <li>Sube fotografías de tus aventuras</li>
                    <li>Descubre vías ferratas y vías de escalada</li>
                    <li>Conecta con otros apasionados de la montaña</li>
                    <li>Valora y comenta las rutas de otros usuarios</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
