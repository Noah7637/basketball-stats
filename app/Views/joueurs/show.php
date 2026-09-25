<?php

ob_start();

?>

<div class="show-header">

    <div class="player-number">

        <?= $joueur['numero'] !== null
            ? (int) $joueur['numero']
            : '-' ?>

    </div>

    <div>

        <h1>
            <?= htmlspecialchars($joueur['nom']) ?>
        </h1>

        <p class="subtitle">
            <?= htmlspecialchars(
                $joueur['poste'] ?? 'Poste non renseigné'
            ) ?>
        </p>

    </div>

</div>

<div class="info-grid">

    <div class="info-card">
        <span class="info-label">Numéro</span>
        <span class="info-value">
            <?= $joueur['numero'] !== null
                ? '#' . (int) $joueur['numero']
                : '-' ?>
        </span>
    </div>

</div><br>

<section class="content-card">

    <h2>Moyennes du joueur</h2>

    <table class="borderless-header-table">
        <thead>
            <tr>
                <th>Matchs</th>
                <th>Pts / match</th>
                <th>Passes / match</th>
                <th>Pts / tentative</th>
                <th>% tirs</th>
                <th>Duels déf.</th>
                <th>Minutes / match</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= (int) $moyenne['matchs_joues'] ?></td>
                <td><?= number_format($moyenne['moyenne_points'], 1) ?></td>
                <td><?= number_format($moyenne['moyenne_passes'], 1) ?></td>
                <td><?= number_format($moyenne['points_par_tentative'], 2) ?></td>
                <td><?= number_format($moyenne['pourcentage_reussite_tirs'], 1) ?>%</td>
                <td><?= number_format($moyenne['duel_def'], 1) ?></td>
                <td><?= number_format($moyenne['minutes_jouees'], 1) ?></td>
            </tr>
        </tbody>
    </table>
<br>
    <h2>Stat pour chaque match</h2>

    <?php if (empty($stats_match)): ?>
        <p>Aucune statistique saisie pour ce joueur pour l'instant.</p>
    <?php else: ?>
        <table class="borderless-header-table">
            <thead>
                <tr>
                    <th>Adversaire</th>
                    <th>Date</th>
                    <th>Résultat</th>
                    <th>Pts</th>
                    <th>Passe</th>
                    <th>Tir 2pts</th>
                    <th>%2pts</th>
                    <th>Tir 3pts</th>
                    <th>%3pts</th>
                    <th>LF</th>
                    <th>%LF</th>
                    <th>Duels def</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats_match as $s): ?>
                    <tr>
                        <td><a href="/match?id=<?= (int) $s['match_id'] ?>"><?= htmlspecialchars($s['adversaire']) ?></a></td>
                        <td><?= htmlspecialchars($s['date']) ?></td>
                        <td>
                            <?php if ($s['score_mon_equipe'] > $s['score_adversaire']): ?>
                                <span class="result-win">V</span>
                            <?php elseif ($s['score_mon_equipe'] < $s['score_adversaire']): ?>
                                <span class="result-loss">D</span>
                            <?php else: ?>
                                <span class="result-draw">...</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $s['points'] ?></td>
                        <td><?= (int) $s['passes_decisives'] ?></td>
                        <td><?= (int) $s['tirs_2pts_reussis'] ?> / <?= (int) $s['tirs_2pts_tentes'] ?></td>
                        <td><?= $s['tirs_2pts_tentes'] > 0 ? number_format($s['tirs_2pts_reussis'] / $s['tirs_2pts_tentes'] * 100, 1) . '%' : '-' ?></td>
                        <td><?= (int) $s['tirs_3pts_reussis'] ?> / <?= (int) $s['tirs_3pts_tentes'] ?></td>
                        <td><?= $s['tirs_3pts_tentes'] > 0 ? number_format($s['tirs_3pts_reussis'] / $s['tirs_3pts_tentes'] * 100, 1) . '%' : '-' ?></td>
                        <td><?= (int) $s['lancers_francs_reussis'] ?> / <?= (int) $s['lancers_francs_tentes'] ?></td>
                        <td><?= $s['lancers_francs_tentes'] > 0 ? number_format($s['lancers_francs_reussis'] / $s['lancers_francs_tentes'] * 100, 1) . '%' : '-' ?></td>
                        <td><?= (int) $s['duels_defensifs_gagnes'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</section>

<a class="back-link" href="/">
    ← Retour à l'équipe
</a>

<?php

$title = htmlspecialchars($joueur['nom']);

$css = "show.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';