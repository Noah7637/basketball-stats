<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du joueur</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($joueur['nom']) ?></h1>
    <p>Numéro <?= (int) $joueur['numero'] ?></p>
    <p><?= htmlspecialchars($joueur['poste']) ?></p>
    <p>Record dans la saison : (à venir)</p>


    <a href="/">Retour à la liste</a>
</body>
</html>
