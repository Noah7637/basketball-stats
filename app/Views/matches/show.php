<?php

ob_start();

?>

<h1>
    Mon équipe
    vs
    <?= htmlspecialchars($match['adversaire']) ?>
</h1>


<div class="match-score">

    <p>Score final</p>

    <p class="score">
        <?= (int) $match['score_mon_equipe'] ?>
        -
        <?= (int) $match['score_adversaire'] ?>
    </p>

</div>


<div class="match-info">

    <p>
        <strong>Date :</strong>
        <?= htmlspecialchars($match['date_match']) ?>
    </p>

    <p>
        <strong>Lieu :</strong>
        <?= $match['domicile']
            ? 'Match à domicile'
            : 'Match à l’extérieur' ?>
    </p>

</div>
<br>
<h4>Stats collectives</h4> <br> <a href="/match/live?id=<?= (int) $match['id'] ?>">Saisie live des stats</a> <br>

<table class="borderless-header-table">

        <thead>
            <tr>
                <th>Pts/possession</th>
                <th>Pts/transition</th>
                <th>Pts/jeu posé</th>
                <th>LF</th>
                <th>%LF</th>
                <th>Contre attaque</th>
                <th>%Contre attaque</th>
                <th>Reb def</th>
                <th>Reb off adv</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= number_format($stats["pts_par_possession"], 2) ?></td>
                <td><?= number_format($stats["pts_par_transition"], 2) ?></td>
                <td><?= number_format($stats["pts_par_jeu_pose"], 2) ?></td>
                <td><?= $stats["lf_tentes"] ?>/<?= $stats["lf_reussis"] ?></td>
                <td><?= number_format($stats["pourcentage_lf"], 1) ?></td>
                <td><?= $stats["contre_attaques"] ?></td>
                <td><?= number_format($stats["pourcentage_contre_attaques"], 1) ?></td>
                <td><?= $stats["reb_def"] ?></td>
                <td><?= $stats["reb_off_adv"] ?></td>
            </tr>
        </tbody>
</table>

<br>
<section class="players-section">

    <h2>Joueurs présents</h2>

    <ul class="players-list">

        <?php foreach ($joueurs as $j): ?>

            <li>
                <?= htmlspecialchars($j['nom']) ?>
            </li>

        <?php endforeach; ?>

    </ul>

</section>


<a class="back-link" href="/matches">
    ← Retour à la liste des matchs
</a>


<?php

$title = "Détails du match";

$css = "show.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';