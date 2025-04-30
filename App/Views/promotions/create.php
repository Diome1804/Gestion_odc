<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/create-promotion.css">

    <title>Ajouter Promo</title>
</head>
<body>
    
<div class="content-container">
    <div class="container create-promotion">
        <h1>Créer une nouvelle promotion</h1>
        <p>Remplissez les informations ci-dessous pour créer une nouvelle promotion.</p>

        <?php if (session_has('validation_errors')): ?>
            <div class="error-messages">
                <?php foreach (session_get('validation_errors') as $field => $errors): ?>
                    <?php foreach ($errors as $error): ?>
                        <p class="error-text"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/promotions/create" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">

            <!-- Champ Nom -->
            <div class="form-group">
                <label for="promotionName">Nom de la promotion</label>
                <input type="text" id="promotionName" name="nom" class="form-control" 
                    placeholder="Ex: Promotion 7" 
                    value="<?= htmlspecialchars(get_old_input('nom')) ?>">
            </div>

            <!-- Dates -->
            <div class="form-group">
                <label for="startDate">Date de début</label>
                <input type="text" id="startDate" name="datedebut" class="form-control" 
                    placeholder="dd/mm/yyyy"
                    value="<?= htmlspecialchars(get_old_input('datedebut')) ?>">
            </div>
            <div class="form-group">
                <label for="endDate">Date de fin</label>
                <input type="text" id="endDate" name="datefin" class="form-control" 
                    placeholder="dd/mm/yyyy"
                    value="<?= htmlspecialchars(get_old_input('datefin')) ?>">
            </div>

            <!-- Référentiels -->
            <div class="form-group">
                <label for="referentiels">Référentiels</label>
                <input type="text" id="referentiels" name="referentiels" class="form-control" 
                    placeholder="Ex: Développement Web"
                    value="<?= htmlspecialchars(get_old_input('referentiels')) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer la promotion</button>
                <a href="/promotions" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>







