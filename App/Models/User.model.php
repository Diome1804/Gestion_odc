<?php


namespace App\Models;

require_once __DIR__.'/../enums.php';
require_once __DIR__.'/Model.php';

use App\Enums\DataKey;
use App\Enums\ModelFunction;
use App\Enums\UserModelKey;

return [
    UserModelKey::AUTHENTICATE->value => function(string $login, string $password) {
        $model = require __DIR__.'/Model.php';
        // Vérification de l'existence de la fonction
        if (!isset($model[ModelFunction::GET_ALL->value])) {
            throw new \RuntimeException('Fonction GET_ALL non trouvée');
        }
        $users = $model[ModelFunction::GET_ALL->value](DataKey::USERS);
        
        // Validation des entrées
        $filtered = array_filter($users, function($u) use ($login) {
            return isset($u['matricule'], $u['email'], $u['password']) 
                && ($u['matricule'] === $login || $u['email'] === $login);
        });

        if ($user = reset($filtered)) {
            return ($password === $user['password']) ? $user : null;
            // return password_verify($password, $user['password']) ? $user : null;
        }
        
        return null;
    },

    UserModelKey::GET_BY_ID->value => function(string $id) {
        $model = require __DIR__.'/Model.php';
        $users = $model[ModelFunction::GET_ALL->value](DataKey::USERS);
        
        return array_reduce($users, function($found, $user) use ($id) {
            return $found ?? ((isset($user['id']) && $user['id'] === $id) ? $user : null);
        });
    },
   
    UserModelKey::UPDATE_PASSWORD->value => function($userId, $newPassword) {
        $model = require __DIR__.'/Model.php';
        $users = $model[ModelFunction::GET_ALL->value](DataKey::USERS);
    
        // Vérification de l'utilisateur
        $user = array_reduce($users['users'], function ($found, $u) use ($userId) {
            return $found ?? (($u['id'] === $userId) ? $u : null);
        });
               
        if (!$user) {
            error_log("Utilisateur non trouvé: $userId");
            return false;
        }
    
        $userIndex = array_search($userId, array_column($users['users'], 'id'));
    
        if ($userIndex === false) {
            error_log("Index utilisateur non trouvé: $userId");
            return false;
        }
    
        // Mise à jour du mot de passe (avec hash optionnel)
        $users['users'][$userIndex]['password'] = $newPassword;
        // $users['users'][$userIndex]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    
        return $model[ModelFunction::SAVE->value](DataKey::USERS, $users);
    },

    UserModelKey::FIND_BY_LOGIN->value => function(string $login) {
        $model = require __DIR__.'/Model.php';
        $users = $model[ModelFunction::GET_ALL->value](DataKey::USERS);
    
        // Vérification des données retournées
        if (!is_array($users) || !isset($users['users']) || !is_array($users['users'])) {
            error_log("Données invalides pour USERS : " . print_r($users, true));
            return null;
        }
    
        // Recherche de l'utilisateur par login (matricule ou email)
        $filtered = array_filter($users['users'], function($u) use ($login) {
            return isset($u['matricule'], $u['email']) 
                && ($u['matricule'] === $login || $u['email'] === $login);
        });
    
        $foundUser = reset($filtered);
        error_log("Utilisateur trouvé : " . print_r($foundUser, true));
        return $foundUser ?: null; // Retourne le premier utilisateur trouvé ou null
    },
];