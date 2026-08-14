<?php

ob_start();

?>

<h1>Mon équipe</h1>

<p>
    <a href="/joueurs/create">+ Ajouter un joueur</a>
</p>


<?php if (empty($joueurs)): ?>

    <p class="empty-message">
        Aucun joueur pour le moment. Commence par en ajouter un.
    </p>

<?php else: ?>

    <table class="borderless-header-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Poste</th>
                <th>Matchs</th>
                <th>Pts / match</th>
                <th>Passes / match</th>
                <th>Pts / tentative</th>
                <th>% tirs</th>
                <th>Duels déf.</th>
                <th>Supprimer</th>
                <th>Modifier</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($joueurs as $joueur): ?>

            <tr>

                <td class="number">
                    <?= $joueur['numero'] !== null
                        ? (int) $joueur['numero']
                        : '-' ?>
                </td>

                <td>
                    <a
                        class="player-name"
                        href="/joueur?id=<?= (int) $joueur['id'] ?>"
                    >
                        <?= htmlspecialchars($joueur['nom']) ?>
                    </a>
                </td>

                <td class="position">
                    <?= htmlspecialchars($joueur['poste'] ?? '-') ?>
                </td>

                <td>
                    <?= (int) $joueur['matchs_joues'] ?>
                </td>

                <td>
                    <?= (int) $joueur['moyenne_points'] ?>
                </td>

                <td>
                    <?= (int) $joueur['moyenne_passes'] ?>
                </td>

                <td>
                    <?= (int) $joueur['points_par_tentative'] ?>
                </td>

                <td>
                    <?= (int) $joueur['pourcentage_reussite_tirs'] ?>%
                </td>

                <td>
                    <?= (int) $joueur['duel_def'] ?>
                </td>

                <td>
                    <a
                        class="action-delete"
                        href="/joueur/delete?id=<?= (int) $joueur['id'] ?>"
                    >
                        Supprimer
                    </a>
                </td>

                <td>
                    <a
                        class="action-edit"
                        href="/joueur/edit?id=<?= (int) $joueur['id'] ?>"
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

$title = "Mon équipe";

$css = "index.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';