<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le joueur</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Modifier le joueur</h1>
    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/joueur/update">
        <input type="hidden" name="joueur_id" value="<?= (int) $joueur['id'] ?>">

        <div class="form-group">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($_POST['nom'] ?? $joueur['nom']) ?>" required>
        </div>

        <div class="form-group">
            <label for="numero">Numéro</label>
            <input type="number" id="numero" name="numero" min="0"
                   value="<?= htmlspecialchars($_POST['numero'] ?? $joueur['numero'] ?? '') ?>">
        </div>

        <div class="form-group">
    <label for="poste">Poste</label>
    <select id="poste" name="poste">
        <option value=""><?= $_POST['poste'] ?? $joueur['poste'] ?? '' ?></option>
        <?php $postes = ['Meneur', 'Arrière', 'Ailier', 'Ailier fort', 'Pivot']; ?>
        <?php foreach ($postes as $poste): ?>
            <option value="<?= $poste ?>" <?= (($_POST['poste'] ?? $joueur['poste'] ?? '') === $poste) ? 'selected' : '' ?>>
                <?= $poste ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <p><a href="/">Annuler</a></p>

    <script src="/assets/js/app.js"></script>
</body>
</html>