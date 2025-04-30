<?php
// error.fr.php content
namespace App\Translate\Fr;
use App\Enums\ErrorCode;


return [
    ErrorCode::REQUIRED_FIELD->value => 'Le champ %s est obligatoire',
    ErrorCode::UNIQUE_NAME->value => 'Le nom "%s" existe déjà',
    ErrorCode::INVALID_DATE->value => 'Format de date invalide (jj/mm/aaaa requis)',
    ErrorCode::PHOTO_REQUIRED->value => 'La photo est obligatoire',
    ErrorCode::PHOTO_FORMAT->value => 'Format d\'image invalide (JPG ou PNG uniquement)',
    ErrorCode::PHOTO_SIZE->value => 'La taille de l\'image ne doit pas dépasser 2Mo',
    ErrorCode::NOM_TROP_LONG->value => 'Le nom ne doit pas dépasser 100 caractères',
    ErrorCode::SESSIONS_OBLIGATOIRE->value => 'Le nombre de sessions est obligatoire',
    ErrorCode::REFERENTIEL_REQUIRED->value => 'Referentiel  obligatoire',
];




