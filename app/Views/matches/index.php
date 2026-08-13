<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Matchs</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Matchs</h1>

    <p>
        <a href="/match/create">+ Créer un match</a>
        &nbsp;|&nbsp;
        <a href="/">Retour aux joueurs</a>
    </p>

    <?php if (empty($matches)): ?>
        <p>Aucun match pour le moment.</p>
    <?php else: ?>
        <table class="stats-table">
            <tr>
                <td></td>
                <th>Adversaire</th>
                <th>Score</th>
                <th>date</th>
                <th>V/D</th>
            </tr>
            <?php foreach ($matches as $match): ?>
                <tr>
                        <th>
                            <a href="/match?id=<?= (int) $match['id'] ?>">Détails</a>
                        </th>
                        <td>
                            <?= htmlspecialchars($match['adversaire']) ?>
                        </td>
                                
                        <td><?= (int) $match['score_mon_equipe'] ?> - <?= (int) $match['score_adversaire'] ?></td>

                        <td><?= htmlspecialchars($match['date_match']) ?></td>

                        <td<?php if ($match['score_mon_equipe'] > $match['score_adversaire']) {
                                    echo " style='color: green'>V";
                                } else if ($match['score_mon_equipe'] < $match['score_adversaire']) {
                                    echo " style='color: red'>D";
                                } else {
                                    echo ">Pas terminé";
                                }?>
                        </td>

                        
                        <th>
                            <a style="color: red" href="/match/delete?id=<?= (int) $match['id'] ?>">Supprimer</a>
                        </th>
                        <th>
                            <a style="color: green" href="/match/edit?id=<?= (int) $match['id'] ?>">Modifier</a>
                        </th>
                </tr>
            <?php endforeach; ?>
            </table>
    <?php endif; ?>

    <script src="/assets/js/app.js"></script>
</body>
</html>
