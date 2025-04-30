<?php
namespace App\Models;
require_once __DIR__.'/../enums.php';

use App\Enums\DataKey;

use App\Enums\ModelFunction;
use App\Enums\JsonOperation;
use App\Enums\Promotion_Model_Key;

return [
       
        Promotion_Model_Key::GET_ALL->value => function() {
            global $fonctions_models;
            return $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
        },
        
        Promotion_Model_Key::GET_BY_ID->value => function($id) {
            if (empty($id)) {
                return null;
            }
            
            global $fonctions_models;
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
            $resultats = array_filter($promotions, function($promotion) use ($id) {
                return $promotion['id'] == $id;
            });
            
            $promotion = reset($resultats);
            return $promotion !== false ? $promotion : null;
        },
        
        Promotion_Model_Key::DESACTIVATE_ALL->value => function() 
        {
            global $fonctions_models;
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
            $promotions = array_map(function($promotion) {
                $promotion['statut'] = 'inactive';
                return $promotion;
            }, $promotions);
            $result = $fonctions_models[ModelFunction::SAVE->value](DataKey::PROMOTIONS, $promotions);
            error_log("Résultat de la sauvegarde : " . var_export($result, true));
            return $result;
        },
    
        
        Promotion_Model_Key::GET_BY_STATUS->value => function($status) 
        {
            global $fonctions_models;
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
    
            return array_filter($promotions, function ($promo) use ($status) {
                return isset($promo['statut']) && $promo['statut'] === $status;
            });
        },    
    
   

        Promotion_Model_Key::UPDATE->value => function($id, $data) 
        {
            global $fonctions_models;
            
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
           
            $promotions = array_map(function($promotion) use ($id, $data) {
                if ($promotion['id'] === $id) {
                    $promotion['statut'] = $data['statut'];
                }
                return $promotion;
            }, $promotions);
        
            
            $resultats = $fonctions_models[ModelFunction::SAVE->value](DataKey::PROMOTIONS, $promotions);
            return $resultats;
        },
    
   
        Promotion_Model_Key::ADD->value => function($newPromotion) {
            global $fonctions_models;
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
            $newPromotion['id'] = uniqid('promo_');
            $promotions[] = $newPromotion; 
            $fonctions_models[ModelFunction::SAVE->value](DataKey::PROMOTIONS, $promotions);
            return $newPromotion;
        },

        Promotion_Model_Key::DELETE->value => function($id) 
        {
            require_once __DIR__.'/Model.php';
            $promotions = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS);
            foreach ($promotions as $key => $promotion) {
                if ($promotion['id'] == $id) {
                    unset($promotions[$key]);
                    break;
                }
            }
            $fonctions_models[ModelFunction::SAVE->value](DataKey::PROMOTIONS, array_values($promotions));
        },

        
        Promotion_Model_Key::GET_NBR->value => function() {
            global $fonctions_models;
            return count($fonctions_models[ModelFunction::GET_ALL->value](DataKey::PROMOTIONS));
        },
    
    

];