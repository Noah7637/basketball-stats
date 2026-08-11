<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Ajouter un joueur</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/joueurs/store">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="numero">Numéro de maillot</label>
                <input type="number" id="numero" name="numero" min="0"
                       value="<?= htmlspecialchars($old['numero'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="poste">Poste</label>
                <select id="poste" name="poste">
                    <option value="">—</option>
                    <?php $postes = ['Meneur', 'Arrière', 'Ailier', 'Ailier fort', 'Pivot']; ?>
                    <?php foreach ($postes as $poste): ?>
                        <option value="<?= $poste ?>" <?= (($old['poste'] ?? '') === $poste) ? 'selected' : '' ?>>
                            <?= $poste ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit">Ajouter</button>
    </form>

    <p><a href="/">Retour à l'accueil</a></p>

    <script src="/assets/js/app.js"></script>
</body>
</html>
