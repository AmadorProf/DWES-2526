<?php
/**
 * Listado de rutas con miniaturas
 * Tarea 1.5: Mostrar rutas creadas
 */
require_once __DIR__ . '/../../includes/header.php';

// Obtener rutas (ordenadas de más reciente a más antigua)
$routes = $_SESSION['routes'] ?? [];
$routes = array_reverse($routes);

// Filtros (opcional para fase avanzada)
$filterDificultad = $_GET['dificultad'] ?? '';
$filterProvincia = $_GET['provincia'] ?? '';

// Aplicar filtros si existen
if (!empty($filterDificultad)) {
    $routes = array_filter($routes, function($route) use ($filterDificultad) {
        return $route['dificultad'] === $filterDificultad;
    });
}

if (!empty($filterProvincia)) {
    $routes = array_filter($routes, function($route) use ($filterProvincia) {
        return $route['provincia'] === $filterProvincia;
    });
}
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-route"></i> Rutas de Senderismo
                    </h4>
                    <?php if (isLoggedIn()): ?>
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Nueva Ruta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="dificultad" class="form-label">Dificultad</label>
                        <select class="form-select" id="dificultad" name="dificultad">
                            <option value="">Todas</option>
                            <option value="facil" <?php echo $filterDificultad === 'facil' ? 'selected' : ''; ?>>Fácil</option>
                            <option value="moderada" <?php echo $filterDificultad === 'moderada' ? 'selected' : ''; ?>>Moderada</option>
                            <option value="dificil" <?php echo $filterDificultad === 'dificil' ? 'selected' : ''; ?>>Difícil</option>
                            <option value="muy_dificil" <?php echo $filterDificultad === 'muy_dificil' ? 'selected' : ''; ?>>Muy Difícil</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="provincia" class="form-label">Provincia</label>
                        <select class="form-select" id="provincia" name="provincia">
                            <option value="">Todas</option>
                            <?php foreach (getProvincias() as $prov): ?>
                                <option value="<?php echo $prov; ?>" <?php echo $filterProvincia === $prov ? 'selected' : ''; ?>>
                                    <?php echo $prov; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="list.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Listado de rutas -->
<div class="row">
    <?php if (empty($routes)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-route fa-4x text-muted mb-3"></i>
                    <h4>No hay rutas disponibles</h4>
                    <p class="text-muted">
                        <?php if (isLoggedIn()): ?>
                            Sé el primero en compartir una ruta con la comunidad
                        <?php else: ?>
                            <a href="../login.php">Inicia sesión</a> para crear y compartir rutas
                        <?php endif; ?>
                    </p>
                    <?php if (isLoggedIn()): ?>
                        <a href="create.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle"></i> Crear Primera Ruta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($routes as $route): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($route['fotos'])): ?>
                        <div class="position-relative" style="height: 200px; overflow: hidden;">
                            <img src="<?php echo SITE_URL; ?>/uploads/photos/<?php echo htmlspecialchars($route['fotos'][0]); ?>"
                                 class="card-img-top" alt="<?php echo htmlspecialchars($route['nombre']); ?>"
                                 style="height: 100%; object-fit: cover;">
                            <?php if (count($route['fotos']) > 1): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-dark">
                                    <i class="fas fa-images"></i> <?php echo count($route['fotos']); ?> fotos
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-mountain fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="view.php?id=<?php echo $route['id']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($route['nombre']); ?>
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted small">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($route['username']); ?>
                            <span class="ms-2">
                                <i class="fas fa-clock"></i> <?php echo formatDate($route['fecha_creacion']); ?>
                            </span>
                        </p>
                        
                        <p class="card-text">
                            <?php echo htmlspecialchars(substr($route['descripcion'], 0, 100)); ?>
                            <?php if (strlen($route['descripcion']) > 100): ?>...<?php endif; ?>
                        </p>
                        
                        <div class="mb-2">
                            <?php
                            $dificultadClass = [
                                'facil' => 'success',
                                'moderada' => 'info',
                                'dificil' => 'warning',
                                'muy_dificil' => 'danger'
                            ];
                            $badgeClass = $dificultadClass[$route['dificultad']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $badgeClass; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $route['dificultad'])); ?>
                            </span>
                            <span class="badge bg-primary">
                                <i class="fas fa-route"></i> <?php echo $route['distancia']; ?> km
                            </span>
                            <span class="badge bg-secondary">
                                <i class="fas fa-mountain"></i> <?php echo $route['desnivel']; ?> m
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> <?php echo $route['duracion']; ?> horas
                            </small>
                            <small class="text-muted ms-2">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($route['provincia']); ?>
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-cog"></i> Técnico: <?php echo $route['nivel_tecnico']; ?>/5
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-heart"></i> Físico: <?php echo $route['nivel_fisico']; ?>/5
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <a href="view.php?id=<?php echo $route['id']; ?>" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($routes)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="mb-0">
                        <strong><?php echo count($routes); ?></strong>
                        <?php echo count($routes) === 1 ? 'ruta encontrada' : 'rutas encontradas'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
