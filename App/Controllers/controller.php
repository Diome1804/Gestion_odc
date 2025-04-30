<?php
namespace App\Controllers;
require_once __DIR__.'/../Services/session.service.php';
// Redirection

function redirect($path) {
    header('Location: '. $path);
    exit;
}  

// Vérification d'authentification
function require_auth() {
    if (!is_authenticated()) {
        // Stocker l'URL originale pour redirection après login
        session_set('redirect_after_login', $_SERVER['REQUEST_URI']);
        redirect('/login');
    }
}

function require_role($roles) {
    require_auth();
    $user = get_current_user();
    $roles = is_array($roles) ? $roles : [$roles];
    if (!in_array($user['role'], $roles)) {
        echo 'error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.';
        redirect('/dashboard');
    }
}

// Rendu de vue
function render_view($template, $data = []) {
    extract($data);
    ob_start();
    require __DIR__."/../Views/{$template}.php";
    $content = ob_get_clean();
    require __DIR__."/../Views/layout/base.layout.php";
    exit;
}

// Rendu avec layouted content (pour votre système actuel)
function render_with_layout($viewPath, $pageTitle, $currentPage = '') {
    return [
        'currentPage' => $currentPage,
        'content' => $viewPath,
        'contentHeader' => "<h2>{$pageTitle}</h2>"
    ];
}




