<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le match</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Modifier le match</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/match/update">
        <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">

        <div class="form-group">
            <label for="adversaire">Adversaire</label>
            <input type="text" id="adversaire" name="adversaire"
                   value="<?= htmlspecialchars($_POST['adversaire'] ?? $match['adversaire']) ?>" required>
        </div>

        <div class="form-group form-checkbox">
            <label>
                <input type="checkbox" name="domicile" value="1"
                    <?= ($_POST['domicile'] ?? $match['domicile']) ? 'checked' : '' ?>>
                Match à domicile
            </label>
        </div>

        <div class="form-group">
            <label for="date_match">Date du match</label>
            <input type="date" id="date_match" name="date_match"
                   value="<?= htmlspecialchars($_POST['date_match'] ?? $match['date_match']) ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="score_mon_equipe">Score de mon équipe</label>
                <input type="number" id="score_mon_equipe" name="score_mon_equipe" min="0"
                       value="<?= htmlspecialchars($_POST['score_mon_equipe'] ?? $match['score_mon_equipe']) ?>" required>
            </div>

            <div class="form-group">
                <label for="score_adversaire">Score adverse</label>
                <input type="number" id="score_adversaire" name="score_adversaire" min="0"
                       value="<?= htmlspecialchars($_POST['score_adversaire'] ?? $match['score_adversaire']) ?>" required>
            </div>
        </div>

        <h2>Joueurs présents</h2>
        <?php foreach ($joueurs as $j): ?>
            <label class="checkbox-line">
                <input type="checkbox" name="joueurs[]" value="<?= (int) $j['id'] ?>"
                    <?= in_array($j['id'], $selectionnes) ? 'checked' : '' ?>>
                <?= htmlspecialchars($j['nom']) ?>
                <?= $j['numero'] !== null ? '(#' . (int) $j['numero'] . ')' : '' ?>
            </label>
        <?php endforeach; ?>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <p><a href="/match?id=<?= (int) $match['id'] ?>">Annuler</a></p>

    <script src="/assets/js/app.js"></script>
</body>
</html>