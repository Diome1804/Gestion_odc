<?php
// ReferentielController.php

namespace App\Controllers\Referentiels;
use App\Controllers as App;
use App\Models;
use App\Enums\Referentiel_Model_Key;
use App\Enums\ReferentielAttribute;
use App\Enums\SuccessCode;
use App\Enums\ModelFunction;
use App\Enums\DataKey;
use App\Services\Session as Session;
use App\Services\Validation_referentiel as valide;
use Exception;
use RuntimeException;
use App\Enums\ErrorCode;
require_once __DIR__.'/../Services/service.validate_ref.php';

    function handleReferentielActions() 
    {
        session_init();
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') 
            {
                $action = $_POST['action'] ?? '';
                
                switch ($action) {
                    case 'create':
                        createReferentiel();
                        break;
                    // case 'update':
                    //     updateReferentiel();
                    //     break;
                    // case 'delete':
                    //     deleteReferentiel();
                    //     break;
                    default:
                        throw new Exception('Action POST non valide');
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) 
            {
                $action = $_GET['action'];
                
                switch ($action) 
                {
                    default:
                    get_all_referentiels();
                }
            }
        } catch (Exception $e) 
        {
            session_set('error_message', $e->getMessage());
        }
    }


    function createReferentiel() {
        try {
        
            $chemin_model = __DIR__.'/../Models/Ref.Model.php';
            if (!file_exists($chemin_model)) {
                die("Fichier modèle introuvable : $chemin_model");
            }
            
            $model = require $chemin_model;
            $data = $_POST;
            $files = $_FILES;
            $errors = valide\validate_referentiel($data, $files);
            if (!empty($errors)) 
            {
                session_set('validation_errors', $errors);
                session_set('old_input', $data);
                App\redirect('/referentiels#create-modal');
                exit;
            }
            
            $photo_path = '';
            if (isset($files['photo']) && $files['photo']['size'] > 0) 
            {
                // echo "photo bi: " . $files['photo']['name'];
                $photo_path = handle_file_upload($files['photo']);
                // echo "<br>file  " . $photo_path;
            }
            
            $new_referentiel = [
                'nom' => $data['nom'],
                'description' => $data['description'] ?? '',
                'photo' => $photo_path,
                'capacite' => $data['capacite'],
                'sessions' => $data['sessions'],
            ];
            // var_dump($new_referentiel);
            // die; 
            $resultat = $model[Referentiel_Model_Key::ADD->value]($new_referentiel); 
            session_remove('validation_errors');
            session_remove('old_input');
            if (!session_has('success_message')) {
                session_set('success_message', ['content' => SuccessCode::REFERENTIEL_CREATED->value]);
                App\redirect('/referentiels');
            } else {
                error_log('déjà enregistre');
            }    
            // echo "<p> c bon .</p>";   
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage();
            session_set('error_message', $e->getMessage());
            App\redirect('/referentiels');
        }
    }

    function get_all_referentiels($search = null) {
        $model = require __DIR__.'/../Models/Ref.Model.php';
        $referentiels = $model[Referentiel_Model_Key::GET_ALL->value]();
    
        // Filtrage par recherche
        if (!empty($search)) {
            $search = strtolower(trim($search));
            $referentiels = array_filter($referentiels, function($ref) use ($search) {
                return (isset($ref['nom']) && strpos(strtolower($ref['nom']), $search) !== false) ||
                       (isset($ref['description']) && strpos(strtolower($ref['description']), $search) !== false);
            });
        }
    
        return array_values($referentiels); // Réindexe le tableau
    }
    
    function get_nbr_referentiels() {
        global $fonctions_models;
        return $fonctions_models[ModelFunction::GET_NBR->value](DataKey::REFERENTIELS);
    }

    function get_referentiel_by_id($id) {
        $model = require __DIR__.'/../Models/Ref.Model.php';
        $referentiels = $model[Referentiel_Model_Key::GET_BY_ID->value]($id);
        
        return !empty($referentiels) ? reset($referentiels) : null;
    }

   
    

    function handle_file_upload($file) {
        $upload_dir = __DIR__.'/../../public/uploads/referentiels/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = uniqid('ref_').'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
        $target_path = $upload_dir.$filename;
        
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new RuntimeException('Erreur lors du téléchargement du fichier');
        }
        
        return $filename;
    }