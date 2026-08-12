<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon équipe - Stats joueurs</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h1>Mon équipe</h1>

    <div class="top-performers">
        <div class="top-card">
            <span class="top-label">Meilleur marqueur</span>
            <?php if ($top['marqueur']): ?>
                <span class="top-value"><?= htmlspecialchars($top['marqueur']['nom']) ?> — <?= (int) $top['marqueur']['total'] ?> pts</span>
            <?php else: ?>
                <span class="top-value top-empty">Aucune stat pour l'instant</span>
            <?php endif; ?>
        </div>

        <div class="top-card">
            <span class="top-label">Meilleur passeur</span>
            <?php if ($top['passeur']): ?>
                <span class="top-value"><?= htmlspecialchars($top['passeur']['nom']) ?> — <?= (int) $top['passeur']['total'] ?> passes</span>
            <?php else: ?>
                <span class="top-value top-empty">Aucune stat pour l'instant</span>
            <?php endif; ?>
        </div>

        <div class="top-card">
            <span class="top-label">Meilleur rebondeur</span>
            <?php if ($top['rebondeur']): ?>
                <span class="top-value"><?= htmlspecialchars($top['rebondeur']['nom']) ?> — <?= (int) $top['rebondeur']['total'] ?> rebonds</span>
            <?php else: ?>
                <span class="top-value top-empty">Aucune stat pour l'instant</span>
            <?php endif; ?>
        </div>
    </div>

    <p>
        <a href="/joueurs/create">+ Ajouter un joueur</a>
        &nbsp;|&nbsp;
        <a href="/matches">Gérer les matchs</a>
    </p>

    <?php if (empty($joueurs)): ?>
        <p>Aucun joueur pour le moment. Commence par en ajouter un.</p>
    <?php else: ?>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Poste</th>
                    <th>Matchs</th>
                    <th>Points</th>
                    <th>Rebonds</th>
                    <th>Passes</th>
                    <th>Interceptions</th>
                    <th>Contres</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($joueurs as $joueur): ?>
                    <tr>
                        <td><?= $joueur['numero'] !== null ? (int) $joueur['numero'] : '-' ?></td>
                        <td><a href="joueur?id=<?= (int) $joueur['id'] ?>">  <?= htmlspecialchars($joueur['nom']) ?></a></td>
                        <td><?= htmlspecialchars($joueur['poste'] ?? '-') ?></td>
                        <td><?= (int) $joueur['matchs_joues'] ?></td>
                        <td><?= (int) $joueur['total_points'] ?></td>
                        <td><?= (int) $joueur['total_rebonds'] ?></td>
                        <td><?= (int) $joueur['total_passes'] ?></td>
                        <td><?= (int) $joueur['total_interceptions'] ?></td>
                        <td><?= (int) $joueur['total_contres'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <script src="/assets/js/app.js"></script>
</body>
</html>
