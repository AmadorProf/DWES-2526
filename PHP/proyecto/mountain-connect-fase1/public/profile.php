<?php
/**
 * Página de perfil de usuario
 * Tarea 1.3: Página protegida con información del usuario
 */
require_once __DIR__ . '/../includes/header.php';

// Verificar que el usuario esté logueado
requireAuth();

$user = getCurrentUser();

// Contar rutas del usuario
$userRoutes = array_filter($_SESSION['routes'] ?? [], function($route) use ($user) {
    return $route['user_id'] === $user['id'];
});
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>
                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2">
                        <i class="fas fa-chart-line text-info"></i>
                        <strong>Nivel:</strong>
                        <?php echo ucfirst(htmlspecialchars($user['nivel_experiencia'])); ?>
                    </p>
                    
                    <?php if (!empty($user['especialidad'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-mountain text-success"></i>
                            <strong>Especialidad:</strong>
                            <?php echo ucfirst(htmlspecialchars($user['especialidad'])); ?>
                        </p>
                    <?php endif; ?>
                    
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <strong>Provincia:</strong>
                        <?php echo htmlspecialchars($user['provincia']); ?>
                    </p>
                </div>
                
                <hr>
                
                <button class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-cog"></i> Configuración
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="text-primary"><?php echo count($userRoutes); ?></h3>
                        <p class="text-muted">Rutas creadas</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-success">0</h3>
                        <p class="text-muted">Ferratas</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-warning">0</h3>
                        <p class="text-muted">Escaladas</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-info">0</h3>
                        <p class="text-muted">Fotografías</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-route"></i> Mis Rutas Recientes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($userRoutes)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-route fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aún no has creado ninguna ruta</p>
                        <a href="routes/create.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Crear mi primera ruta
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach (array_slice($userRoutes, -5) as $route): ?>
                            <a href="routes/view.php?id=<?php echo $route['id']; ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="fas fa-route text-primary"></i>
                                        <?php echo htmlspecialchars($route['nombre']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo formatDate($route['fecha_creacion']); ?>
                                    </small>
                                </div>
                                <p class="mb-1 small">
                                    <span class="badge bg-secondary"><?php echo $route['dificultad']; ?></span>
                                    <span class="badge bg-info"><?php echo $route['distancia']; ?> km</span>
                                    <span class="badge bg-success"><?php echo $route['provincia']; ?></span>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="routes/list.php" class="btn btn-outline-primary">
                            Ver todas mis rutas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Actividad Reciente</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <p>No hay actividad reciente</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
