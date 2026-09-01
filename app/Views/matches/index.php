<?php

ob_start();

?>

<h1>Matchs</h1>

<p>
    <a href="/match/create">+ Créer un match</a>
</p>

<h4>Stats/match</h4>

<table class="borderless-header-table">

        <thead>
            <tr>
                <th>Matchs</th>
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
                <td><?= $moyenne["nb_matchs"] ?></td>
                <td><?= number_format($moyenne["pts_par_possession"], 2) ?></td>
                <td><?= number_format($moyenne["pts_par_transition"], 2) ?></td>
                <td><?= number_format($moyenne["pts_par_jeu_pose"], 2) ?></td>
                <td><?= $moyenne["lf_tentes"] ?>/<?= $moyenne["lf_reussis"] ?></td>
                <td><?= number_format($moyenne["pourcentage_lf"], 1) ?></td>
                <td><?= number_format($moyenne["contre_attaques"], 1) ?></td>
                <td><?= number_format($moyenne["pourcentage_contre_attaques"], 1) ?></td>
                <td><?= number_format($moyenne["reb_def"], 1) ?></td>
                <td><?= number_format($moyenne["reb_off_adv"], 1) ?></td>
            </tr>
        </tbody>
</table>
<br>
<h4>Liste matchs</h4>
<?php if (empty($matches)): ?>

    <p class="empty-message">
        Aucun match pour le moment.
    </p>

<?php else: ?>

    <table class="borderless-header-table">

        <thead>
            <tr>
                <th>Détails</th>
                <th>Adversaire</th>
                <th>Score</th>
                <th>Date</th>
                <th>Résultat</th>
                <th>Supprimer</th>
                <th>Modifier</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($matches as $match): ?>

            <tr>

                <td>
                    <a href="/match?id=<?= (int) $match['id'] ?>">
                        Détails
                    </a>
                </td>


                <td>
                    <?= htmlspecialchars($match['adversaire']) ?>
                </td>


                <td>
                    <?= (int) $match['score_mon_equipe'] ?>
                    -
                    <?= (int) $match['score_adversaire'] ?>
                </td>


                <td>
                    <?= htmlspecialchars($match['date_match']) ?>
                </td>


                <td>

                    <?php if ($match['score_mon_equipe'] > $match['score_adversaire']): ?>

                        <span class="result-win">V</span>

                    <?php elseif ($match['score_mon_equipe'] < $match['score_adversaire']): ?>

                        <span class="result-loss">D</span>

                    <?php else: ?>

                        <span class="result-draw">...</span>

                    <?php endif; ?>

                </td>


                <td>
                    <a
                        class="action-delete"
                        href="/match/delete?id=<?= (int) $match['id'] ?>"
                    >
                        Supprimer
                    </a>
                </td>


                <td>
                    <a
                        class="action-edit"
                        href="/match/edit?id=<?= (int) $match['id'] ?>"
                    >
                        Modifier
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>


<?php

$title = "Accueil matchs";

$css = "index.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';