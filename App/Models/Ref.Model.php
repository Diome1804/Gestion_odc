<?php
namespace App\Models;
require_once __DIR__.'/../enums.php';
use App\Enums\DataKey;
use App\Enums\ModelFunction;
use App\Enums\Referentiel_Model_Key;
return[

    Referentiel_Model_Key::GET_ALL->value => function() 
    {
        global $fonctions_models;
        return $fonctions_models[ModelFunction::GET_ALL->value](DataKey::REFERENTIELS);
    },

    Referentiel_Model_Key::GET_BY_ID->value => function($id) 
    {
            global $fonctions_models;
            $referentiels = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::REFERENTIELS);
            return array_filter($referentiels, function($referentiel) use ($id) {
                return $referentiel['id'] == $id;
            });
    },
    Referentiel_Model_Key::SEARCH->value => function($term) {
        global $fonctions_models;
        $referentiels = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::REFERENTIELS);
        
        $term = strtolower(trim($term));
        $results = array_filter($referentiels, function($ref) use ($term) {
            return strpos(strtolower($ref['nom']), $term) !== false;
        });
        
        return array_values($results);
    },

    Referentiel_Model_Key::ADD->value => function($newReferentiel) 
    {
            global $fonctions_models;
            $referentiels = $fonctions_models[ModelFunction::GET_ALL->value](DataKey::REFERENTIELS);
            $newReferentiel['id'] = uniqid('ref_');
            $referentiels[] = $newReferentiel;
            $fonctions_models[ModelFunction::SAVE->value](DataKey::REFERENTIELS, $referentiels);
            return $newReferentiel;
    },
    
        
        Referentiel_Model_Key::GET_NBR->value => function() 
        {
            global $fonctions_models;
            return $fonctions_models[ModelFunction::GET_NBR->value](DataKey::REFERENTIELS);
        },
    

];