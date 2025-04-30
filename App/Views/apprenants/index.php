<div class="apprenants-container">
    <h1>Gestion des Apprenants</h1>
    <button class="btn btn-primary" onclick="window.location.href='/apprenants/create'">Ajouter un Apprenant</button>
    
    <table class="apprenants-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Promotion</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($apprenants)): ?>
                <?php foreach ($apprenants as $apprenant): ?>
                    <tr>
                        <td><?= htmlspecialchars($apprenant['nom']) ?></td>
                        <td><?= htmlspecialchars($apprenant['prenom']) ?></td>
                        <td><?= htmlspecialchars($apprenant['email']) ?></td>
                        <td><?= htmlspecialchars($apprenant['promotion']) ?></td>
                        <td>
                            <a href="/apprenants/edit?id=<?= $apprenant['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="/apprenants/delete?id=<?= $apprenant['id'] ?>" class="btn btn-sm btn-danger">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Aucun apprenant trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
