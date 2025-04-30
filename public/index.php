<?php
// Fichier: public/index.php

// Configuration des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/../storage/logs/error.log');

// Initialisation des services
require_once __DIR__.'/../App/Services/session.service.php';

session_init();

// Chargement des routes
$routes = require __DIR__ . '/../App/route/route.web.php';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$routeFound = false;

foreach ($routes as $route => $handler) {
    list($routeMethod, $routePath) = explode(' ', $route, 2);  
    
    if ($method === $routeMethod && $uri === $routePath) {
        try {
            $routeFound = true;
            
            // Exécution du handler
            $data = call_user_func($handler);
            
            // Gestion des vues protégées
            if (isset($data['content'])) {
                extract($data);
                
                require __DIR__ . '/../App/Views/layout/base.layout.php';
            }
            
            
        } catch (Exception $e) {
            error_log("Route error: " . $e->getMessage());
            session_set('error_message', 'Une erreur technique est survenue');
            App\Controllers\redirect('/500');
        }
        break;
    }
}

if (!$routeFound) {
    App\Controllers\redirect('/404');
}