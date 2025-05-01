<?php
// PromotionController.php

namespace App\Controllers\Promotions;
use App\Controllers as App;
use App\Models;
use App\Enums\PromotionAttribute;
use App\Enums\Promotion_Model_Key;
use App\Enums\SuccessCode;
use App\Enums\ModelFunction;
use App\Enums\DataKey;
use App\Services\Session as Session;
use App\Services\Validate_promotion as validation;

$model = require __DIR__.'/../Models/Promo.model.php';
require_once __DIR__.'/../Services/validator.service.php';

function handlePromotionActions() {
    session_init();

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'create':
                    createPromotion(); // Appelle la fonction de création
                    break;
                case 'update':
                    updatePromotion(); // Appelle la fonction de mise à jour
                    break;
                case 'delete':
                    deletePromotion(); // Appelle la fonction de suppression
                    break;
                default:
                    throw new Exception('Action POST non valide');
            }
        } else {
            throw new Exception('Méthode non prise en charge');
        }
    } catch (Exception $e) {
        session_set('error_message', $e->getMessage());
        App\redirect('/promotions'); // Redirection en cas d'erreur
        exit;
    }
}

function createPromotion() 
{
    try {
        $data = $_POST;
        $files = $_FILES;

        // Validation des données
        $errors = validation\validate_promotion($data, $files);  
        if (!empty($errors)) {
            session_set('validation_errors', $errors);
            session_set('old_input', $data);
            App\redirect('/promotions/create');
            exit;
        }

        // Téléchargement de la photo
        $photo_path = handle_file_upload($files['photo']);
    
        // Ajout de la promotion
        $model = require __DIR__.'/../Models/Promo.model.php';
        $model[Promotion_Model_Key::ADD->value]([
            PromotionAttribute::NAME->value => $data['nom'],
            PromotionAttribute::START_DATE->value => $data['datedebut'],
            PromotionAttribute::END_DATE->value => $data['datefin'],
            PromotionAttribute::PHOTO->value => $photo_path,
            PromotionAttribute::STATUS->value => 'inactive',
            PromotionAttribute::STUDENTS_NB->value => 0,
            PromotionAttribute::REFERENTIELS->value => [$data['referentiels']],
        ]);

        // Nettoyage des sessions et redirection
        session_remove('validation_errors');
        session_remove('old_input');
        session_set('success_message', ['content' => SuccessCode::PROMOTION_CREATED->value]);
        App\redirect('/promotions');
    } catch (Exception $e) {
        error_log("Erreur lors de la création de la promotion : " . $e->getMessage());
        session_set('error_message', $e->getMessage());
        App\redirect('/promotions/create');
    }
}
   

function get_all_promotions($status = null, $search = null) {
    $model = require __DIR__.'/../Models/Promo.model.php';
    $promotions = $model[Promotion_Model_Key::GET_ALL->value]();

    // Filtrage par statut
    if (!empty($status)) {
        $promotions = array_filter($promotions, function($promo) use ($status) {
            return isset($promo['statut']) && $promo['statut'] === $status;
        });
    }

    // Filtrage par recherche
    if (!empty($search)) {
        $search = strtolower(trim($search));
        $promotions = array_filter($promotions, function($promo) use ($search) {
            return isset($promo['nom']) && strpos(strtolower($promo['nom']), $search) !== false;
        });
    }

    return array_values($promotions); 
}

function get_nbr_promotions() {
    global $fonctions_models;
    return $fonctions_models[ModelFunction::GET_NBR->value](DataKey::PROMOTIONS);
}
function get_active_promotion() {
    global $model;
    $getByStatus = $model[Promotion_Model_Key::GET_BY_STATUS->value]??null;
    // $activePromos = $getByStatus('active');
    if (is_callable($getByStatus)) {
        $activePromos = $getByStatus('active');
    } else {
        error_log('Erreur: get_by_status est null ou non callable');
        $activePromos = [];
    }
    
    return !empty($activePromos) ? reset($activePromos) : null;
}


function togglePromotionStatus() {
    try {
        if (!isset($_GET['promotion_id'])) {
            throw new Exception('Paramètre promotion_id manquant');
        }

        $id = $_GET['promotion_id'];
        $model = require __DIR__.'/../Models/Promo.model.php';
    
    
        $promotion = $model[Promotion_Model_Key::GET_BY_ID->value]($id);
        
        if (!$promotion) {
            throw new Exception("Aucune promotion trouvée avec l'ID $id");
        }
    
        $newStatus = ($promotion['statut'] === 'active') ? 'inactive' : 'active';
        
        error_log("Changement de statut pour $id : {$promotion['statut']} => $newStatus");

        if ($newStatus === 'active') {
            if (!$model[Promotion_Model_Key::DESACTIVATE_ALL->value]()) {
                throw new Exception('Échec désactivation des autres promotions');
            }
        }
        
        if (!$model[Promotion_Model_Key::UPDATE->value]($id, ['statut' => $newStatus])) {
            throw new Exception('Échec mise à jour du statut');
        }

        session_set('success_message', [
            'content' => $newStatus === 'active' 
                ? 'Promotion activée' 
                : 'Promotion désactivée'
        ]);
    } catch (Exception $e) {
        error_log("Erreur toggle_status : " . $e->getMessage());
        session_set('error_message', $e->getMessage());
    }

    App\redirect('/promotions');
    exit;
}

function get_promotions_by_status($status) {
    if (!in_array($status, ['active', 'inactive'])) {
        throw new Exception("Statut non valide !");
    }
    $model = require __DIR__.'/../Models/Promo.model.php';
    return $model[Promotion_Model_Key::GET_BY_STATUS->value]($status);
}

function handle_file_upload($file) {
    error_log("Début du téléchargement du fichier : " . print_r($file, true));

    $upload_dir = __DIR__.'/../../public/uploads/promotions/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new \RuntimeException('Erreur lors du téléchargement du fichier : ' . $file['error']);
    }

    $filename = uniqid('promo_').'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
    $target_path = $upload_dir.$filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        throw new RuntimeException('Erreur lors du déplacement du fichier');
    }

    error_log("Fichier téléchargé avec succès : " . $target_path);
    return $filename;
}