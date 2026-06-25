<?php
/**
 * Vista detallada de una ruta
 */
require_once __DIR__ . '/../../includes/header.php';

// Obtener ID de la ruta
$routeId = $_GET['id'] ?? '';

if (empty($routeId)) {
    header('Location: list.php');
    exit();
}

// Buscar la ruta
$route = null;
foreach ($_SESSION['routes'] ?? [] as $r) {
    if ($r['id'] === $routeId) {
        $route = $r;
        break;
    }
}

if (!$route) {
    header('Location: list.php');
    exit();
}

// Verificar si el usuario actual es el creador
$isOwner = isLoggedIn() && getCurrentUser()['id'] === $route['user_id'];
?>

<div class="row">
    <div class="col-12 mb-3">
        <a href="list.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
        <?php if ($isOwner): ?>
            <a href="edit.php?id=<?php echo $route['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="delete.php?id=<?php echo $route['id']; ?>" class="btn btn-danger"
               onclick="return confirm('¿Estás seguro de eliminar esta ruta?')">
                <i class="fas fa-trash"></i> Eliminar
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Galería de fotos -->
        <?php if (!empty($route['fotos'])): ?>
            <div class="card mb-4">
                <div id="routePhotosCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($route['fotos'] as $index => $foto): ?>
                            <button type="button" data-bs-target="#routePhotosCarousel"
                                    data-bs-slide-to="<?php echo $index; ?>"
                                    <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($route['fotos'] as $index => $foto): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo SITE_URL; ?>/uploads/photos/<?php echo htmlspecialchars($foto); ?>"
                                     class="d-block w-100" alt="Foto <?php echo $index + 1; ?>"
                                     style="max-height: 500px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($route['fotos']) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#routePhotosCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#routePhotosCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Información principal -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3">
                    <i class="fas fa-route text-primary"></i>
                    <?php echo htmlspecialchars($route['nombre']); ?>
                </h2>
                
                <p class="text-muted">
                    <i class="fas fa-user"></i> Creado por
                    <strong><?php echo htmlspecialchars($route['username']); ?></strong>
                    el <?php echo formatDate($route['fecha_creacion']); ?>
                </p>
                
                <hr>
                
                <h5><i class="fas fa-info-circle"></i> Descripción</h5>
                <p class="lead"><?php echo nl2br(htmlspecialchars($route['descripcion'])); ?></p>
            </div>
        </div>
        
        <!-- Comentarios (placeholder) -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comments"></i> Comentarios</h5>
            </div>
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comment-slash fa-3x mb-3"></i>
                    <p>Los comentarios estarán disponibles en la próxima fase del proyecto</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Datos técnicos -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Datos Técnicos</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><i class="fas fa-signal"></i> Dificultad:</strong><br>
                    <?php
                    $dificultadClass = [
                        'facil' => 'success',
                        'moderada' => 'info',
                        'dificil' => 'warning',
                        'muy_dificil' => 'danger'
                    ];
                    $badgeClass = $dificultadClass[$route['dificultad']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?php echo $badgeClass; ?> fs-6">
                        <?php echo ucfirst(str_replace('_', ' ', $route['dificultad'])); ?>
                    </span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-route"></i> Distancia:</strong><br>
                    <span class="fs-5"><?php echo $route['distancia']; ?> km</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-mountain"></i> Desnivel Positivo:</strong><br>
                    <span class="fs-5"><?php echo $route['desnivel']; ?> m</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-clock"></i> Duración Estimada:</strong><br>
                    <span class="fs-5"><?php echo $route['duracion']; ?> horas</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-cog"></i> Nivel Técnico:</strong><br>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $route['nivel_tecnico'] ? 'text-warning' : 'text-muted'; ?>"></i>
                    <?php endfor; ?>
                    <span class="ms-2"><?php echo $route['nivel_tecnico']; ?>/5</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-heart"></i> Nivel Físico:</strong><br>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-heart <?php echo $i <= $route['nivel_fisico'] ? 'text-danger' : 'text-muted'; ?>"></i>
                    <?php endfor; ?>
                    <span class="ms-2"><?php echo $route['nivel_fisico']; ?>/5</span>
                </div>
            </div>
        </div>
        
        <!-- Ubicación -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Ubicación</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <strong>Provincia:</strong><br>
                    <span class="fs-5"><?php echo htmlspecialchars($route['provincia']); ?></span>
                </p>
            </div>
        </div>
        
        <!-- Época recomendada -->
        <?php if (!empty($route['epocas'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Época Recomendada</h5>
                </div>
                <div class="card-body">
                    <?php
                    $epocaIcons = [
                        'primavera' => 'seedling',
                        'verano' => 'sun',
                        'otono' => 'leaf',
                        'invierno' => 'snowflake'
                    ];
                    ?>
                    <?php foreach ($route['epocas'] as $epoca): ?>
                        <span class="badge bg-info me-1 mb-1">
                            <i class="fas fa-<?php echo $epocaIcons[$epoca] ?? 'calendar'; ?>"></i>
                            <?php echo ucfirst($epoca); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Acciones -->
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-success" disabled>
                        <i class="fas fa-thumbs-up"></i> Me gusta (0)
                    </button>
                    <button class="btn btn-primary" disabled>
                        <i class="fas fa-bookmark"></i> Guardar ruta
                    </button>
                    <button class="btn btn-info" disabled>
                        <i class="fas fa-share-alt"></i> Compartir
                    </button>
                </div>
                <small class="text-muted d-block mt-2 text-center">
                    Funciones disponibles en próximas fases
                </small>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
