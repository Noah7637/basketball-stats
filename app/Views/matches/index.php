<?php

ob_start();

?>

<h1>Matchs</h1>

<p>
    <a href="/match/create">+ Créer un match</a>
</p>


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