<?php
// UserController.php
namespace App\Controllers\UserController;
use Exception;

use App\Enums\UserModelKey;
use App\Controllers as Controller;

require_once __DIR__.'/controller.php';
require_once __DIR__.'/../Services/session.service.php';
require_once __DIR__.'/../enums.php'; 

function login() 
{
    session_init();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userModel = require __DIR__.'/../Models/User.model.php';
        $login = trim($_POST['login']);
        $password = trim($_POST['password']);
        try {   
            if (empty($login) || empty($password)) {
                throw new Exception('required_field');
            }
            $user = $userModel[UserModelKey::AUTHENTICATE->value]($login, $password);
            error_log("Auth result: " . json_encode($user));
            if (empty($user) || !is_array($user)) {
                throw new Exception('invalid_credentials');
            }
            if (!isset($user['id'], $user['role'], $user['matricule'])) {
                throw new Exception('invalid_user_data');
            }
            session_set('user', [
                'id' => $user['id'],
                'role' => (string)$user['role'],
                'nom' => (string)($user['nom'] ?? ''),
                'matricule' => (string)$user['matricule']
            ]);  
            error_log("Utilisateur authentifié: ".print_r($user, true));
            error_log("Session avant redirection: ".print_r($_SESSION, true));
            ob_start(); // Démarre un buffer de sortie
            Controller\redirect(match($user['role']) {
                'admin' => '/dashboard',
                'apprenant' => '/apprenant/dashboard',
                'vigile' => '/vigile/scan',
            });
            exit;
        } catch (Exception $e) {
            error_log("Erreur d'authentification: " . $e->getMessage());
            $errorKey = $e->getMessage();
            
            $errors = require __DIR__.'/error.controller.php'; 
            session_set('login_errors', [$errors[$errorKey] ?? 'Une erreur est survenue']);
            session_set('old_input', [
                'login' => $login
            ]);
        }
    }
    require __DIR__.'/../Views/auth/login.php';
    
    if (session_has('login_errors')) {
        session_remove('login_errors');
    }
}

function logout() {
    session_destroy_all();
    Controller\redirect('/');
}
 

function forgotPassword() {
    session_init();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {      
            $login = trim($_POST['login'] ?? '');
            $newPassword = trim($_POST['new_password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');          
            if (empty($login) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('required_field');
            }  
            if ($newPassword !== $confirmPassword) {
                throw new Exception('password_mismatch');     
            }   
            $userModel = require __DIR__.'/../Models/User.model.php';
            $user = $userModel[UserModelKey::FIND_BY_LOGIN->value]($login);
            
            if (!$user) {
                throw new Exception('user_not_found');
            }
            $success = $userModel[UserModelKey::UPDATE_PASSWORD->value]($user['id'], $newPassword);
            
            if (!$success) {
                throw new Exception('update_failed');
            }
            session_set('login_success', 'Mot de passe mis à jour avec succès');
            Controller\redirect('/');
            
        } catch (Exception $e) {    
            $errorFile = __DIR__.'/error.controller.php';
            error_log('Loading error file: ' . $errorFile);   
            $errors = null;
            if (file_exists($errorFile)) {
                $errors = require $errorFile;
                if (is_array($errors)) {
                }
            } 
            $errorMessage = 'Erreur inconnue';
            if (is_array($errors) && isset($errors[$e->getMessage()])) {
                $errorMessage = $errors[$e->getMessage()];
            }     
            error_log('Final error message: ' . $errorMessage);
            session_set('forgot_password_errors', [$errorMessage]);
            session_set('old_input', ['login' => $login ?? '']);
        }
    }

    require __DIR__.'/../Views/auth/forgot_password.php';
    session_remove('forgot_password_errors');
}