<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du match</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Mon équipe <?= $match['domicile'] ? 'vs' : '@' ?> <?= htmlspecialchars($match['adversaire']) ?></h1>
    <p>Score : <?= (int) $match['score_mon_equipe'] ?> - <?= (int) $match['score_adversaire'] ?></p>
    <p>Date : <?= htmlspecialchars($match['date_match']) ?></p>
    <p><?= $match['domicile'] ? 'Match à domicile' : 'Match à l\'extérieur' ?></p>

    <!-- Ici tu ajouteras l'affichage des statistiques par joueur (étape suivante) -->

    <a href="/">Retour à la liste</a>
</body>
</html>
