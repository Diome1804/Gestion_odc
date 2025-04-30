<?php
return [
    // Récupérer tous les apprenants
    'get_all' => function() {
        $filePath = __DIR__.'/../data/global.json';
        if (!file_exists($filePath)) {
            return [];
        }
        $jsonData = json_decode(file_get_contents($filePath), true);
        return $jsonData['apprenants'] ?? [];
    },

    // Ajouter un nouvel apprenant
    'create' => function($data) {
        $filePath = __DIR__.'/../data/global.json';
        $jsonData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

        // Générer un nouvel ID
        $newId = 'APP-' . str_pad((count($jsonData['apprenants'] ?? []) + 1), 3, '0', STR_PAD_LEFT);
        $data['id'] = $newId;

        // Ajouter l'apprenant
        $jsonData['apprenants'][] = $data;

        // Sauvegarder dans le fichier JSON
        file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));
    },

    // Récupérer un apprenant par ID
    'get_by_id' => function($id) {
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
    },

    // Supprimer un apprenant par ID
    'delete' => function($id) {
        $filePath = __DIR__.'/../data/global.json';
        if (!file_exists($filePath)) {
            return;
        }
        $jsonData = json_decode(file_get_contents($filePath), true);
        $jsonData['apprenants'] = array_filter($jsonData['apprenants'], function($apprenant) use ($id) {
            return $apprenant['id'] !== $id;
        });

        // Sauvegarder les modifications
        file_put_contents($filePath, json_encode(array_values($jsonData['apprenants']), JSON_PRETTY_PRINT));
    }
];