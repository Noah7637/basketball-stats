<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du match</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<form method="POST" action="/match/selection">
    <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">
    <?php foreach ($joueurs as $j): ?>
        <label>
            <input type="checkbox" name="joueurs[]" value="<?= $j['id'] ?>">
            <?= htmlspecialchars($j['nom']) ?>
        </label>
    <?php endforeach; ?>
    <button type="submit">Valider la sélection</button>
</form>

</body>
</html>