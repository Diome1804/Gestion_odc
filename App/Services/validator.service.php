<?php
namespace App\Services\Validate_promotion;

require_once __DIR__.'/../enums.php';
use App\Enums\ErrorCode;
use App\Enums\PromotionAttribute;
use App\Enums\Promotion_Model_Key;
use App\Enums\ModelFunction;
use App\Enums\DataKey;
use DateTime;


function validate_promotion(array $data, array $files) {
    $errors = [];
    $translations = require __DIR__.'/../translate/fr/error.fr.php';

    // Règles de validation structurées
    $validation_rules = [
        'nom' => [
            'required' => [
                'message' => ErrorCode::REQUIRED_FIELD,
                'params' => ['Nom de la promotion']
            ],
            'unique' => [
                'message' => ErrorCode::UNIQUE_NAME
            ]
        ],
        'datedebut' => [
            'required' => [
                'message' => ErrorCode::REQUIRED_FIELD,
                'params' => ['Date de début']
            ],
            

            'date_format' => [
                'format' => 'd/m/Y',
                'message' => ErrorCode::INVALID_DATE
            ]
        ],
        'datefin' => [
            'required' => [
                'message' => ErrorCode::REQUIRED_FIELD,
                'params' => ['Date de fin']
            ],
            

            'date_format' => [
                'format' => 'd/m/Y',
                'message' => ErrorCode::INVALID_DATE
            ]
        ],

        'photo' => [
            'file_required' => [
                'message' => ErrorCode::PHOTO_REQUIRED
            ],
            'file_type' => [
                'allowed' => ['image/jpeg', 'image/png'],
                'message' => ErrorCode::PHOTO_FORMAT
            ],
            'file_size' => [
                'max' => 2 * 1024 * 1024,
                'message' => ErrorCode::PHOTO_SIZE
            ]
        ]
    ];

    foreach ($validation_rules as $field => $rules) {
        $value = $data[$field] ?? ($field === 'photo' ? $files[$field] : null);   
        foreach ($rules as $rule_type => $rule) {
            $valid = true;
            $message_params = [];
            switch ($rule_type) {
                case 'required':
                    $valid = !empty(trim($value));
                    $message_params = $rule['params'];
                    break;
                case 'unique':
                    $valid = check_unique_name($value);
                    $message_params = [$value]; 
                    break;
                case 'date_format':
                    $date = \DateTime::createFromFormat($rule['format'], $value);
                    $valid = $date && $date->format($rule['format']) === $value;
                    break;
                case 'file_required':
                    $valid = !empty($value['tmp_name']);
                    break;
                case 'file_type':
                    $valid = in_array(mime_content_type($value['tmp_name']), $rule['allowed']);
                    break;
                case 'file_size':
                    $valid = $value['size'] <= $rule['max'];
                    break;
            }
            if (!$valid) {
                $errors[$field][] = vsprintf(
                    $translations[$rule['message']->value],
                    $message_params
                );
                break;
            }
        }
    }
    return $errors;
}

function check_unique_name($name) 
{
    $model = require __DIR__.'/../Models/Promo.model.php';
    foreach ($model[Promotion_Model_Key::GET_ALL->value]() as $promo) {
        if ($promo[PromotionAttribute::NAME->value] === $name) return false;
    }
    return true;
}