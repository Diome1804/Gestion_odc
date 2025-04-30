<?php
// validator.service.php - Fonction validate_referentiel

namespace App\Services\Validation_referentiel;
use App\Enums\ErrorCode;
function validate_referentiel($data, $files, $id = null) {
    $errors = [];
    $translations = require __DIR__.'/../translate/fr/error.fr.php';
   
    if (empty($data['nom'])) {
        $errors['nom'][] = ErrorCode::REFERENTIEL_REQUIRED->value;
    } elseif (strlen($data['nom']) > 100) {
        $errors['nom'][] = ErrorCode:: NOM_TROP_LONG->value ;
    }
    
  
    if (empty($data['capacite'])) {
        $errors['capacite'] = ErrorCode::REQUIRED_FIELD->value;
    } elseif (!is_numeric($data['capacite']) || $data['capacite'] <= 0) {
        $errors['capacite'][] = 'La capacité doit être un nombre positif';
    }
    
    
    if (empty($data['sessions'])) {
        $errors['sessions'][]= ErrorCode::SESSIONS_OBLIGATOIRE->value;
    }
    
    if (($id === null || (isset($files['photo']) && $files['photo']['size'] > 0)) && isset($files['photo'])) {
        if ($files['photo']['error'] !== 0 && $id === null) {
            $errors['photo'][] = ErrorCode::PHOTO_REQUIRED->value;
        } elseif ($files['photo']['error'] === 0) {
                      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($files['photo']['type'], $allowed_types)) {
                $errors['photo'][] = ErrorCode::PHOTO_FORMAT->value;
            }
            if ($files['photo']['size'] > 2 * 1024 * 1024) {
                $errors['photo'][] = ErrorCode::PHOTO_SIZE->value;
            }
        }
    }
    
    return $errors;
}