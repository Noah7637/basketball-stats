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