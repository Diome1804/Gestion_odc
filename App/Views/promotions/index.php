<?php
session_init();
$promos = App\Controllers\Promotions\get_all_promotions(
    $_GET['status'] ?? null,
    $_GET['search'] ?? null
) ?? [];
$nbPromos = count($promos);
$viewMode = $_GET['view'] ?? 'grid';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Promotions</title>
    <link rel="stylesheet" href="/assets/css/promotions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php if (session_has('success_message')): ?>
        <div class="global-alert success">
            <i class="fas fa-check-circle"></i>
            <p><?= htmlspecialchars(session_get('success_message')['content']) ?></p>
        </div>
    <?php endif; ?>

    <div class="container">
        <header class="header">
            <div class="title-section">
                <h1>Gestion des Promotions</h1>
                <p>Gérez les promotions de l'école facilement.</p>
            </div>
            <a href="/promotions/create" class="add-button">
                <i class="fas fa-plus"></i> Ajouter une promotion
            </a>
        </header>

        <section class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <h2>0</h2>
                    <p>Apprenants</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h2>5</h2>
                    <p>Référentiels</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h2>1</h2>
                    <p>Promotions actives</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <p>Total promotions</p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </section>

        <form method="GET" action="/promotions" id="filterForm" class="filter-form">
            <div class="search-filter-section">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher..." name="search"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        onchange="document.getElementById('filterForm').submit()">
                    <input type="hidden" name="view" value="<?= htmlspecialchars($_GET['view'] ?? 'grid') ?>">
                </div>
                <div class="filter-section">
                    <select class="filter-dropdown" name="status" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Tous</option>
                        <option value="active" <?= (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : '' ?>>Actifs</option>
                        <option value="inactive" <?= (isset($_GET['status']) && $_GET['status'] === 'inactive') ? 'selected' : '' ?>>Inactifs</option>
                    </select>
                    <div class="view-buttons">
                        <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'grid'])) ?>" class="view-button <?= ($viewMode === 'grid' ? 'active' : '') ?>">
                            <i class="fas fa-th-large"></i> Grille
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'list'])) ?>" class="view-button <?= ($viewMode === 'list' ? 'active' : '') ?>">
                            <i class="fas fa-list"></i> Liste
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <?php if ($viewMode === 'grid'): ?>
            <section class="promotions-grid">
                <?php if (!empty($promos)) : ?>
                    <?php foreach ($promos as $promotion) : ?>
                        <?php
                        $defaults = [
                            'id' => '0',
                            'nom' => 'Promotion sans nom',
                            'statut' => 'inactif',
                            'date_debut' => 'Non définie',
                            'date_fin' => 'Non définie',
                            'photo' => '/assets/default-promo.png',
                            'nbr_etudiants' => 0
                        ];
                        $promotion = array_merge($defaults, $promotion);

                        $name = $promotion['nom'] ?? 'Promotion';
                        $words = explode(' ', $name);
                        $initials = count($words) >= 2 
                            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                            : strtoupper(substr($name, 0, 2));

                        $colors = ['#00a67d', '#ff6b1b', '#2ecc71', '#e74c3c', '#3498db'];
                        $colorIndex = hexdec(substr(md5($name), 0, 1)) % count($colors);
                        $bgColor = $colors[$colorIndex];
                        ?>
                        <div class="promotion-card">
                            <div class="status-bar">
                                <span class="status-badge <?= $promotion['statut'] === 'active' ? 'active' : 'inactive' ?>">
                                    <?= htmlspecialchars(ucfirst($promotion['statut'])) ?>
                                </span>
                                <a href="/promotions/toggle?promotion_id=<?= $promotion['id'] ?>" 
                                    class="power-button <?= $promotion['statut'] === 'active' ? 'active' : '' ?>">
                                    <i class="fas fa-power-off"></i>
                                </a>
                            </div>

                            <div class="promotion-content">
                                <div class="promotion-header">
                                    <div class="promotion-logo initials-logo" style="background-color: <?= $bgColor ?>">
                                        <?= $initials ?>
                                    </div>
                                    <h3 class="promotion-title">
                                        <?= htmlspecialchars($name) ?>
                                    </h3>
                                </div>
                                <div class="promotion-date">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>
                                        <?= htmlspecialchars($promotion['date_debut']) ?> - 
                                        <?= htmlspecialchars($promotion['date_fin']) ?>
                                    </span>
                                </div>
                                <div class="promotion-stats">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>
                                        <?= htmlspecialchars($promotion['nbr_etudiants']) ?> apprenants
                                    </span>
                                </div>
                                <div class="promotion-footer">
                                    <a href="/promotions/<?= $promotion['id'] ?>" class="details-link">
                                        Voir détails <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="no-promotions">
                        <p>Aucune promotion disponible pour le moment</p>
                    </div>
                <?php endif; ?>
            </section>
            <?php else : ?>
                <!-- Affichage en liste -->
                <div class="promotions-list-container">
                    <table class="promotions-list-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Promotion</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Référentiel</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($promos)) : ?>
                                <?php foreach ($promos as $promotion) : ?>
                                    <?php
                                    $defaults = [
                                        'id' => '0',
                                        'nom' => 'Promotion sans nom',
                                        'statut' => 'inactif',
                                        'date_debut' => 'Non définie',
                                        'date_fin' => 'Non définie',
                                        'photo' => '/assets/images/60111.jpg',
                                        'nbr_etudiants' => 0,
                                        'referentiels' => []
                                    ];
                                    $promotion = array_merge($defaults, $promotion);
                                    
                                    $name = $promotion['nom'] ?? 'Promotion';
                                    $words = explode(' ', $name);
                                    $initials = count($words) >= 2 
                                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                        : strtoupper(substr($name, 0, 2));
                                    
                                    $colors = ['#00a67d', '#ff6b1b', '#2ecc71', '#e74c3c', '#3498db'];
                                    $colorIndex = hexdec(substr(md5($name), 0, 1)) % count($colors);
                                    $bgColor = $colors[$colorIndex];
                                    
                                    $isActive = $promotion['statut'] === 'active' ? true : false;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="promotion-logo initials-logo" style="background-color: <?= $bgColor ?>">
                                                <?= $initials ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($promotion['nom']) ?></td>
                                        <td><?= htmlspecialchars($promotion['date_debut']) ?></td>
                                        <td><?= htmlspecialchars($promotion['date_fin']) ?></td>
                                        <td>
                                            <div class="ref-badges">
                                                <?php if (!empty($promotion['referentiels'])) : ?>
                                                    <?php foreach ($promotion['referentiels'] as $referentiel) : ?>
                                                        <?php
                                                        // Déterminer une classe CSS basée sur le référentiel
                                                        $refLower = strtolower(trim($referentiel));
                                                        $refClass = '';
                                                        
                                                        if (strpos($refLower, 'dev web') !== false) {
                                                            $refClass = 'dev-web';
                                                        } elseif (strpos($refLower, 'dev mobile') !== false) {
                                                            $refClass = 'dev-web'; // Même classe que dev web
                                                        } elseif (strpos($refLower, 'ref dig') !== false) {
                                                            $refClass = 'ref-dig';
                                                        } elseif (strpos($refLower, 'dev data') !== false) {
                                                            $refClass = 'dev-data';
                                                        } elseif (strpos($refLower, 'aws') !== false) {
                                                            $refClass = 'aws';
                                                        } elseif (strpos($refLower, 'hackeuse') !== false) {
                                                            $refClass = 'hackeuse';
                                                        } else {
                                                            // Classe par défaut basée sur le nom
                                                            $refClass = 'ref-' . preg_replace('/[^a-z0-9]/', '-', $refLower);
                                                        }
                                                        ?>
                                                        <span class="ref-badge <?= $refClass ?>">
                                                            <?= htmlspecialchars(strtoupper($referentiel)) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <span class="ref-badge">NON DÉFINI</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $isActive ? 'active' : 'inactive' ?>">
                                                <?= $isActive ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="/promotions/<?= $promotion['id'] ?>" class="action-button">•••</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="no-promotions">
                                        <p>Aucune promotion disponible pour le moment</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <div class="pagination">
                        <div class="page-selector">
                            <span>page</span>
                            <select>
                                <option>5</option>
                                <option>10</option>
                                <option>15</option>
                                <option>20</option>
                            </select>
                        </div>
                        <div class="pagination-info">1 à <?= min(count($promos), 5) ?> pour <?= count($promos) ?></div>
                        <div class="pagination-controls">
                            <button class="pagination-button nav"><i class="fas fa-chevron-left"></i></button>
                            <button class="pagination-button active">1</button>
                            <?php if (count($promos) > 5) : ?>
                            <button class="pagination-button">2</button>
                            <?php endif; ?>
                            <button class="pagination-button nav"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
        
        <?php endif; ?>
        <!-- Modal d'ajout de promotion -->
        <div id="promotionModal" class="modal-overlay">
            <div class="modal">
                <div class="modal-header">
                    <h2 class="modal-title">Créer une nouvelle promotion</h2>
                    <p class="modal-subtitle">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</p>
                    <a href="#" class="close-button">&times;</a>
                </div>

                <?php if (session_has('validation_errors')): ?>
                    <div style="color:red;">
                        <?php foreach (session_get('validation_errors', []) as $field => $errors): ?>
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="/promotions/create" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <!-- Champ Nom -->
                        <div class="form-group">
                            <label for="promotionName">Nom de la promotion</label>
                            <input type="text" id="promotionName" name="nom" class="form-control" 
                                placeholder="Ex: Promotion 7" 
                                value="<?= htmlspecialchars(get_old_input('nom')) ?>">
                            <?php if (session_has('validation_errors.nom')): ?>
                                <div class="error-text">
                                    <?= implode('<br>', session_get('validation_errors.nom', [])) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Dates -->
                        <div class="date-inputs">
                            <!-- Date de début -->
                            <div class="form-group">
                                <label for="startDate">Date de début</label>
                                <input type="text" id="startDate" name="datedebut" class="form-control" 
                                    placeholder="dd/mm/yyyy"
                                    value="<?= htmlspecialchars(get_old_input('datedebut')) ?>">
                                <?php if (session_has('validation_errors.datedebut')): ?>
                                    <div class="error-text">
                                        <?= implode('<br>', session_get('validation_errors.datedebut', [])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Date de fin -->
                            <div class="form-group">
                                <label for="endDate">Date de fin</label>
                                <input type="text" id="endDate" name="datefin" class="form-control" 
                                    placeholder="dd/mm/yyyy"
                                    value="<?= htmlspecialchars(get_old_input('datefin')) ?>">
                                <?php if (session_has('validation_errors.datefin')): ?>
                                    <div class="error-text">
                                        <?= implode('<br>', session_get('validation_errors.datefin', [])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="form-group">
                            <label for="photo">Photo de la promotion</label>
                            <label for="photoInput" class="upload-area">
                                <span class="upload-button">Ajouter</span>
                                <span class="upload-text">ou glisser</span>
                            </label>
                            <input type="file" id="photoInput" name="photo" class="file-input" 
                                accept="image/jpeg,image/png">
                            <?php if (session_has('validation_errors.photo')): ?>
                                <div class="error-text">
                                    <?= implode('<br>', session_get('validation_errors.photo', [])) ?>
                                </div>
                            <?php endif; ?>
                            <div class="upload-info">Format JPG, PNG. Taille max 2MB</div>
                        </div>

                        <!-- Référentiels -->
                        <div class="form-group">
                            <label>Référentiels</label>
                            <div class="search-input">
                                <i class="fas fa-search"></i>
                                <input type="text" name="referentiels" class="form-control" 
                                    placeholder="Rechercher un référentiel..."
                                    value="<?= htmlspecialchars(get_old_input('referentiels')) ?>">
                            </div>
                            <?php if (session_has('validation_errors.referentiels')): ?>
                                <div class="error-text">
                                    <?= implode('<br>', session_get('validation_errors.referentiels', [])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="/promotions" class="btn btn-cancel">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer la promotion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    clear_session_messages();
    ?>
<style>
    /* Variables de couleurs */
:root {
    --primary-color: #00a67d;
    --secondary-color: #ff6b1b;
    --danger-color: #ff4d4d;
    --success-color: #28a745;
    --text-color: #333333;
    --light-text: #ffffff;
    --border-color: #e0e0e0;
    --light-bg: #f8f9fa;
    --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    --transition-speed: 0.3s;
    --border-radius: 8px;
    
    /* Référentiels couleurs */
    --ref-dev-web: #4CD964;
    --ref-ref-dig: #5AC8FA;
    --ref-dev-data: #AF52DE;
    --ref-aws: #FF9500;
    --ref-hackeuse: #FF3B30;
}

/* Styles pour l'affichage en liste */
.promotions-list-container {
    width: 100%;
    margin: 0;
    padding: 0;
    background-color: var(--light-text);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--card-shadow);
}

.promotions-list-table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

.promotions-list-table thead {
    background-color: var(--secondary-color);
    color: var(--light-text);
}

.promotions-list-table th {
    padding: 15px 20px;
    text-align: left;
    font-weight: 500;
    font-size: 16px;
    border: none;
}

.promotions-list-table th:first-child {
    border-top-left-radius: var(--border-radius);
}

.promotions-list-table th:last-child {
    border-top-right-radius: var(--border-radius);
    text-align: center;
}

.promotions-list-table td {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
    font-size: 14px;
}

.promotions-list-table tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.promotions-list-table td:last-child {
    text-align: center;
}

/* Cercles d'initiales */
.promotion-logo.initials-logo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: none !important;
    margin: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Badges de référentiel */
.ref-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.ref-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    color: var(--light-text);
}

.ref-badge.dev-web {
    background-color: var(--ref-dev-web);
}

.ref-badge.ref-dig {
    background-color: var(--ref-ref-dig);
}

.ref-badge.dev-data {
    background-color: var(--ref-dev-data);
}

.ref-badge.aws {
    background-color: var(--ref-aws);
}

.ref-badge.hackeuse {
    background-color: var(--ref-hackeuse);
}

/* Badges de statut */
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.status-badge.active {
    background-color: rgba(40, 167, 69, 0.1);
    color: var(--success-color);
}

.status-badge.active::before {
    content: "•";
    margin-right: 6px;
    font-size: 20px;
    color: var(--success-color);
}

.status-badge.inactive {
    background-color: rgba(255, 77, 77, 0.1);
    color: var(--danger-color);
}

.status-badge.inactive::before {
    content: "•";
    margin-right: 6px;
    font-size: 20px;
    color: var(--danger-color);
}

/* Boutons d'action */
.actions-cell {
    text-align: center;
}

.action-button {
    width: 8px;
    height: 26px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    color: #6c757d;
    font-size: 18px;
    font-weight: bold;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: var(--light-text);
    border-top: 1px solid var(--border-color);
}

.pagination-info {
    color: #6c757d;
    font-size: 14px;
}

.pagination-controls {
    display: flex;
    gap: 5px;
}

.pagination-button {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background-color: var(--light-text);
    border: 1px solid var(--border-color);
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    transition: all var(--transition-speed);
}

.pagination-button.active {
    background-color: var(--secondary-color);
    color: var(--light-text);
    border-color: var(--secondary-color);
}

.pagination-button.nav {
    border-radius: 50%;
    background-color: var(--light-bg);
}

.pagination-button:hover:not(.active) {
    background-color: #e9ecef;
}

.page-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-selector select {
    padding: 5px 10px;
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

/* Adaptation responsive */
@media (max-width: 992px) {
    .promotions-list-table th,
    .promotions-list-table td {
        padding: 12px 10px;
    }
    
    .ref-badge {
        padding: 3px 6px;
        font-size: 10px;
    }
}

@media (max-width: 768px) {
    .promotions-list-container {
        overflow-x: auto;
    }
    
    .promotions-list-table {
        min-width: 800px;
    }
}
</style>
</body>
</html>