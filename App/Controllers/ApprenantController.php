<?php
namespace App\Controllers;

class ApprenantController {
    public static function handleApprenantActions() {
        // Récupérer les données du formulaire
        $data = $_POST;

        // Valider les données
        $errors = self::validateApprenant($data);
        if (!empty($errors)) {
            session_set('validation_errors', $errors);
            session_set('old_input', $data);
            Controller\redirect('/apprenants');
            exit;
        }

        // Charger le fichier JSON
        $filePath = __DIR__.'/../data/global.json';
        $jsonData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

        // Générer un nouvel ID pour l'apprenant
        $newId = 'APP-' . str_pad((count($jsonData['apprenants'] ?? []) + 1), 3, '0', STR_PAD_LEFT);
        $data['id'] = $newId;

        // Ajouter l'apprenant dans la liste
        $jsonData['apprenants'][] = $data;

        // Sauvegarder les modifications dans le fichier JSON
        file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));

        // Redirection avec un message de succès
        session_set('success_message', 'Apprenant ajouté avec succès.');
        Controller\redirect('/apprenants');
    }

    public static function getApprenantById($id) {
        $filePath = __DIR__.'/../data/global.json';
        if (!file_exists($filePath)) {
            return null;
        }
    
        $jsonData = json_decode(file_get_contents($filePath), true);
        foreach ($jsonData['apprenants'] as $apprenant) {
            if ($apprenant['id'] === $id) {
                return $apprenant;
            }
        }
        return null;
    }

    public static function deleteApprenant() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $model = require __DIR__.'/../Models/Apprenant.Model.php';
            $model['delete']($id);
            session_set('success_message', 'Apprenant supprimé avec succès.');
        } else {
            session_set('error_message', 'ID de l\'apprenant manquant.');
        }
        Controller\redirect('/apprenants');
    }

    public static function index() {
        // Charger le fichier JSON
        $filePath = __DIR__.'/../data/global.json';
        $jsonData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
        $apprenants = $jsonData['apprenants'] ?? [];
    
        // Charger la vue avec les données
        $content = __DIR__.'/../Views/apprenants/index.php';
        $currentPage = 'apprenants';
        require __DIR__.'/../Views/layout/base.layout.php';
    }

    private static function validateApprenant($data) {
        $errors = [];
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire.';
        }
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Un email valide est obligatoire.';
        }
        if (empty($data['promotion_id'])) {
            $errors['promotion_id'] = 'La promotion est obligatoire.';
        }
        return $errors;
    }
}