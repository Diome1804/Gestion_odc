<?php
require_once __DIR__.'/../Models/Model.php';
require_once __DIR__.'/../Controllers/controller.php';
require_once __DIR__.'/../Services/session.service.php';
require_once __DIR__.'/../Controllers/PromoController.php';
require_once __DIR__.'/../Controllers/RefController.php';
require_once __DIR__.'/../Controllers/UserController.php';

use App\Controllers\UserController as User;
use App\Controllers\Promotions as Promo;
use App\Controllers\Referentiels as Referentiels;
use App\Controllers as Controller;

function checkAuth() {
   session_init();
    if (!session_has('user')) {
        error_log('Utilisateur non authentifié');
        Controller\redirect('/');
        exit;
    }
}

function protectedView(string $currentPage, string $viewPath, string $headerTitle) {
    checkAuth();
    return [
        'currentPage' => $currentPage,
        'content' => __DIR__.'/../Views/'.$viewPath,
        'contentHeader' => '<h2>'.$headerTitle.'</h2>'
    ];
}

return [

    'GET /promotions/create' => function() {
        session_init();
        checkAuth(); // Vérifie si l'utilisateur est authentifié
        require_once __DIR__ . '/../Views/promotions/create.php'; // Charge directement la page
        exit;
    },

   
    'POST /promotions/create' => fn() => App\Controllers\PromoController\handlePromotionActions(),  
    
    'GET /' => function() {
        session_init();
        if (session_has('user')) {
            $role = session_get('user.role');
            $redirectPath = match($role) {
                'admin' => '/dashboard',
                'apprenant' => '/apprenant/dashboard',
                default => null
            };
    
            if ($redirectPath && $redirectPath !== '/') {
                Controller\redirect($redirectPath);
            }
        }
        require_once __DIR__.'/../Views/auth/login.php';
        exit;
    },

    'GET /login' => function() {
        session_init();
        if (session_has('user')) {
            Controller\redirect('/');
        }
        require_once __DIR__.'/../Views/auth/login.php';
        exit;
    },

    'POST /login' => fn() => User\login(),

    'POST /' => fn() => User\login(),

    'GET /dashboard' => fn() => protectedView(
        'dashboard',
        'dashboard.php', 
        'Bienvenue sur votre tableau de bord'
    ),

   'GET /promotions' => function () {
        session_init();
        checkAuth();
        global $data;

        // Récupération des paramètres
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? null;
        $viewMode = $_GET['view'] ?? 'grid';

        // Récupération des données
        $data['promotions'] = Promo\get_all_promotions($status, $search);
        $data['nbPromos'] = count($data['promotions']);
        $data['viewMode'] = $viewMode;

        return protectedView('promotions', 'promotions/index.php', 'Gestion des Promotions');
    },

    'GET /promotions/toggle' => fn() => Promo\togglePromotionStatus(),

    'POST /promotions/create' => fn() => Promo\handlePromotionActions(),
    
    'GET /referentiels' => fn() => protectedView('referentiels', 'referentiels/index.php', 'Gestion des Référentiels'),

    'POST /referentiels/create' => fn() => Referentiels\handleReferentielActions(),
    
    'GET /referentiels/{id}' => fn($id) => Referentiels\get_referentiel_by_id($id),

    'GET /referentiels/count' => fn() => Referentiels\get_nbr_referentiels(),

    'GET /logout' => function() {
        session_destroy_all();
        Controller\redirect('/');
        exit;
    },

    'GET /forgot-password' => fn() => User\forgotPassword(),
    'POST /forgot-password' => fn() => User\forgotPassword(),


    // Liste des apprenants
    'GET /apprenants' => fn() => protectedView('apprenants', 'apprenants/index.php', 'Gestion des Apprenants'),

    // Création d'un apprenant
    'POST /apprenants/create' => fn() => App\Controllers\ApprenantController\handleApprenantActions(),

    // Récupération d'un apprenant par ID
    'GET /apprenants/{id}' => fn($id) => App\Controllers\ApprenantController\getApprenantById($id),

    // Suppression d'un apprenant
    'POST /apprenants/delete' => fn() => App\Controllers\ApprenantController\deleteApprenant(),





];
