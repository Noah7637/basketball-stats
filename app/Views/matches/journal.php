<?php ob_start(); ?>

<h1>Journal du match - vs <?= htmlspecialchars($match['adversaire']) ?></h1>

<p><a href="/match?id=<?= (int) $match['id'] ?>">← Retour au match</a></p>

<?php if (empty($journal)): ?>
    <p>Aucune action enregistrée pour ce match pour l'instant.</p>
<?php else: ?>
    <?php
    $libellesType = [
        '2pts' => '2 pts', '3pts' => '3 pts', 'lf' => 'Lancer franc',
        'possession' => 'Nouvelle possession',
        'passe' => 'Passe décisive', 'duel' => 'Duel défensif gagné',
        'rebond_def' => 'Rebond défensif (équipe)', 'rebond_off_adv' => 'Rebond off. adverse (équipe)',
    ];
    $libellesPossession = ['transition' => 'Transition', 'jeu_pose' => 'Jeu posé', 'contre_attaque' => 'Contre-attaque'];
    $sansResultat = ['passe', 'duel', 'rebond_def', 'rebond_off_adv', 'possession'];

    $dernierQuart = null;
    ?>

    <table class="borderless-header-table">
        <thead>
            <tr>
                <th>Q</th>
                <th>Joueur</th>
                <th>Action</th>
                <th>Résultat</th>
                <th>Possession</th>
                <th>Saisi le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($journal as $a): ?>
                <?php if ($a['quart_temps'] !== $dernierQuart): ?>
                    <?php $dernierQuart = $a['quart_temps']; ?>
                    <tr class="live-quart-separator">
                        <td colspan="7">Quart-temps <?= (int) $a['quart_temps'] ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Q<?= (int) $a['quart_temps'] ?></td>
                    <td><?= $a['joueur_nom'] !== null ? htmlspecialchars($a['joueur_nom']) : '— Équipe —' ?></td>
                    <td><?= htmlspecialchars($libellesType[$a['type']] ?? $a['type']) ?></td>
                    <td>
                        <?php if (in_array($a['type'], $sansResultat, true)): ?>
                            -
                        <?php else: ?>
                            <?= $a['reussi'] ? '✅' : '❌' ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $a['type_possession'] ? htmlspecialchars($libellesPossession[$a['type_possession']] ?? $a['type_possession']) : '-' ?></td>
                    <td><?= htmlspecialchars($a['created_at']) ?></td>
                    <td>
                        <form method="POST" action="/match/journal/delete-action"
                              onsubmit="return confirm('Supprimer cette action ? Les totaux du match seront recalculés.');">
                            <input type="hidden" name="action_id" value="<?= (int) $a['id'] ?>">
                            <input type="hidden" name="match_id" value="<?= (int) $match['id'] ?>">
                            <button type="submit" class="delete-action">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$title = "Journal du match";
$css = "live.css";
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>