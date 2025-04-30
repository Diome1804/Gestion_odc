<?php
$promotionActive = App\Controllers\Promotions\get_active_promotion();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sonatel Academy' ?></title>
    <!-- Inclure les fichiers CSS -->
    <link rel="stylesheet" href="/assets/css/promotions.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
    <?php if (isset($additionalStyles)) echo $additionalStyles; ?>
</head>
<body>
    <!-- Menu toggle button for mobile -->
    <div class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </div>   
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-container">
                <div class="orange-text">Orange Digital Center</div>
                <div class="logo">
                    <div class="sonatel-text">sonatel</div>
                    <div class="orange-square"></div>
                </div>
                <div class="promotion-badge">
                    Promotion <?= isset($promotionActive['date_debut']) ? date('Y', strtotime($promotionActive['date_debut'])) : 'Non spécifiée' ?>
                </div>
            </div>
        </div>   
        <div class="sidebar-menu">
            <a href="dashboard" class="menu-item <?= $currentPage == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="promotions" class="menu-item <?= $currentPage == 'promotions' ? 'active' : '' ?>">
                <i class="fas fa-bookmark"></i>
                <span>Promotions</span>
            </a>
            <a href="referentiels" class="menu-item <?= $currentPage == 'referentiels' ? 'active' : '' ?>">
                <i class="fas fa-book"></i>
                <span>Référentiels</span>
            </a>
            <a href="apprenants" class="menu-item <?= $currentPage == 'apprenants' ? 'active' : '' ?>">
                <i class="fas fa-user-graduate"></i>
                <span>Apprenants</span>
            </a>
            <a href="" class="menu-item <?= $currentPage == 'presences' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-check"></i>
                <span>Gestion des présences</span>
            </a>
            <a href="kits" class="menu-item <?= $currentPage == 'kits' ? 'active' : '' ?>">
                <i class="fas fa-laptop"></i>
                <span>Kits & Laptops</span>
            </a>
            <a href="rapports" class="menu-item <?= $currentPage == 'rapports' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Rapports & Stats</span>
            </a>
        </div>

        <!-- Logout button -->
        <div class="sidebar-logout">
            <a href="logout" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div> 
    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search">
            </div>
            
            <div class="user-section">
                <div class="notifications">
                    <i class="fas fa-bell"></i>
                </div>
                
                <div class="user-profile">
                    <div class="user-info">
                    <div class="user-name"><?= session_has('user') ? htmlspecialchars(session_get('user')['nom']) : '' ?></div>
                        <div class="user-role"><?=session_has('user') ? htmlspecialchars(session_get('user')['role']) : '' ?></div>
                    </div>
                    
                    <div class="avatar">
                        <img src="assets/images/60111.jpg" alt="User Avatar">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content container -->
        <div class="content-wrapper">
    <?php if (isset($contentHeader)): ?>
        <div class="content-header">
            <?= $contentHeader ?>
        </div>
    <?php endif; ?>
    
    <div class="content-container">
        <?php if (isset($content)): ?>
            <?php include $content ?>
        <?php else: ?>
            <p>Contenu non disponible.</p>
        <?php endif; ?>
    </div>
</div>
    
 
    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Responsive adjustments
        function checkWidth() {
            if (window.innerWidth <= 576) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }
        
        window.addEventListener('resize', checkWidth);
        checkWidth();
    </script>
    <?php if (isset($additionalScripts)) echo $additionalScripts; ?>
</body>
</html>