<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau match</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Créer un match</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/match/store">
        <div class="form-group">
            <label for="adversaire">Adversaire</label>
            <input type="text" id="adversaire" name="adversaire"
                   value="<?= htmlspecialchars($old['adversaire'] ?? '') ?>" required>
        </div>

        <div class="form-group form-checkbox">
            <label>
                <input type="checkbox" name="domicile" value="1"
                    <?= (!isset($old['domicile']) || $old['domicile']) ? 'checked' : '' ?>>
                Match à domicile
            </label>
        </div>

        <div class="form-group">
            <label for="date_match">Date du match</label>
            <input type="date" id="date_match" name="date_match"
                   value="<?= htmlspecialchars($old['date_match'] ?? date('Y-m-d')) ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="score_mon_equipe">Score de mon équipe</label>
                <input type="number" id="score_mon_equipe" name="score_mon_equipe" min="0"
                       value="<?= htmlspecialchars($old['score_mon_equipe'] ?? '0') ?>" required>
            </div>

            <div class="form-group">
                <label for="score_adversaire">Score adverse</label>
                <input type="number" id="score_adversaire" name="score_adversaire" min="0"
                       value="<?= htmlspecialchars($old['score_adversaire'] ?? '0') ?>" required>
            </div>
        </div>

        <button type="submit">Créer le match</button>
    </form>

    <p><a href="/">Retour à la liste</a></p>

    <script src="/assets/js/app.js"></script>
</body>
</html>
