<?php ob_start(); ?>

<h1>Saisie live — vs <?= htmlspecialchars($match['adversaire']) ?></h1>

<div class="live-toolbar">
    <div class="form-group">
        <label for="quart-select">Quart-temps</label>
        <select id="quart-select">
            <option value="1">Q1</option>
            <option value="2">Q2</option>
            <option value="3">Q3</option>
            <option value="4">Q4</option>
        </select>
    </div>

    <div class="form-group">
        <label for="possession-select">Type de possession en cours</label>
        <select id="possession-select">
            <option value="transition">Transition</option>
            <option value="jeu_pose">Jeu posé</option>
            <option value="contre_attaque">Contre-attaque</option>
        </select>
    </div>

    <button type="button" id="nouvelle-possession">+ Nouvelle possession</button>
</div>

<h2>Joueur</h2>
<div class="live-players">
    <?php foreach ($joueurs as $j): ?>
        <button type="button" class="live-player-btn" data-joueur-id="<?= (int) $j['id'] ?>">
            <?= htmlspecialchars($j['nom']) ?>
            <?= $j['numero'] !== null ? '(#' . (int) $j['numero'] . ')' : '' ?>
        </button>
    <?php endforeach; ?>
</div>

<div id="live-panel" class="live-panel" style="display:none;">
    <h2 id="live-player-name"></h2>

    <div class="live-action-group">
        <span class="live-action-label">Type de tir</span>
        <button type="button" class="live-toggle" data-group="type" data-value="2pts">2 pts</button>
        <button type="button" class="live-toggle" data-group="type" data-value="3pts">3 pts</button>
        <button type="button" class="live-toggle" data-group="type" data-value="lf">Lancer franc</button>
    </div>

    <div class="live-action-group">
        <span class="live-action-label">Résultat</span>
        <button type="button" class="live-toggle" data-group="reussi" data-value="1">Réussi</button>
        <button type="button" class="live-toggle" data-group="reussi" data-value="0">Manqué</button>
    </div>

    <button type="button" id="valider-action" disabled>Valider l'action</button>

    <div class="live-quick-actions">
        <span class="live-action-label">Actions rapides</span>
        <button type="button" id="quick-passe">+1 Passe décisive</button>
        <button type="button" id="quick-duel">+1 Duel défensif gagné</button>
    </div>
</div>

<h2>Stats d'équipe (quart-temps en cours)</h2>
<div class="live-team-actions">
    <button type="button" id="quick-reb-def">+1 Rebond défensif</button>
    <button type="button" id="quick-reb-off-adv">+1 Rebond offensif adverse</button>
</div>

<h2>Journal des actions (<span id="nb-actions">0</span>)</h2>
<table class="borderless-header-table">
    <thead>
        <tr>
            <th>Q</th>
            <th>Joueur</th>
            <th>Action</th>
            <th>Résultat</th>
            <th>Possession</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="live-log"></tbody>
</table>

<p>
    <button type="button" id="save-all" disabled>Enregistrer le match</button>
    <span id="save-status"></span>
</p>

<p><a href="/match?id=<?= (int) $match['id'] ?>">Retour au match (sans enregistrer)</a></p>

<script>
const matchId = <?= (int) $match['id'] ?>;
const joueursNoms = <?= json_encode(array_column($joueurs, 'nom', 'id')) ?>;

let actions = [];
let joueurActifId = null;
let selection = { type: null, reussi: null };

const libellesType = {
    '2pts': '2 pts', '3pts': '3 pts', 'lf': 'Lancer franc',
    'possession': 'Nouvelle possession',
    'passe': 'Passe décisive', 'duel': 'Duel défensif gagné',
    'rebond_def': 'Rebond défensif (équipe)', 'rebond_off_adv': 'Rebond off. adverse (équipe)',
};
const libellesPossession = { transition: 'Transition', jeu_pose: 'Jeu posé', contre_attaque: 'Contre-attaque' };

function quartActif() {
    return parseInt(document.getElementById('quart-select').value, 10);
}

function possessionActive() {
    return document.getElementById('possession-select').value;
}

document.getElementById('nouvelle-possession').addEventListener('click', () => {
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: null,
        type: 'possession',
        reussi: null,
        type_possession: possessionActive(),
    });
    render();
});

document.querySelectorAll('.live-player-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.live-player-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        joueurActifId = btn.dataset.joueurId;
        document.getElementById('live-player-name').textContent = joueursNoms[joueurActifId];
        document.getElementById('live-panel').style.display = 'block';
        resetSelection();
    });
});

document.querySelectorAll('.live-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const group = btn.dataset.group;
        document.querySelectorAll(`.live-toggle[data-group="${group}"]`).forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selection[group] = btn.dataset.value;
        verifierCompletude();
    });
});

function resetSelection() {
    selection = { type: null, reussi: null };
    document.querySelectorAll('.live-toggle').forEach(b => b.classList.remove('active'));
    document.getElementById('valider-action').disabled = true;
}

function verifierCompletude() {
    document.getElementById('valider-action').disabled = !(selection.type !== null && selection.reussi !== null);
}

document.getElementById('valider-action').addEventListener('click', () => {
    // Un lancer franc n'a pas de type de possession (ne fait pas partie d'une possession classée)
    const typePossession = selection.type === 'lf' ? null : possessionActive();

    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: parseInt(joueurActifId, 10),
        type: selection.type,
        reussi: selection.reussi === '1',
        type_possession: typePossession,
    });

    resetSelection();
    render();
});

document.getElementById('quick-passe').addEventListener('click', () => ajouterActionSimple('passe'));
document.getElementById('quick-duel').addEventListener('click', () => ajouterActionSimple('duel'));
document.getElementById('quick-reb-def').addEventListener('click', () => ajouterActionEquipe('rebond_def'));
document.getElementById('quick-reb-off-adv').addEventListener('click', () => ajouterActionEquipe('rebond_off_adv'));

function ajouterActionSimple(type) {
    if (!joueurActifId) {
        alert('Sélectionne d\'abord un joueur.');
        return;
    }
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: parseInt(joueurActifId, 10),
        type: type,
        reussi: true,
        type_possession: null,
    });
    render();
}

function ajouterActionEquipe(type) {
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: null,
        type: type,
        reussi: true,
        type_possession: null,
    });
    render();
}

function render() {
    const tbody = document.getElementById('live-log');
    tbody.innerHTML = '';

    actions.forEach(a => {
        const tr = document.createElement('tr');
        const nomJoueur = a.joueur_id !== null ? joueursNoms[a.joueur_id] : '— Équipe —';
        const sansResultat = ['passe', 'duel', 'rebond_def', 'rebond_off_adv', 'possession'].includes(a.type);
        const resultat = sansResultat ? '-' : (a.reussi ? '✅' : '❌');
        tr.innerHTML = `
            <td>Q${a.quart_temps}</td>
            <td>${nomJoueur}</td>
            <td>${libellesType[a.type]}</td>
            <td>${resultat}</td>
            <td>${a.type_possession ? libellesPossession[a.type_possession] : '-'}</td>
            <td><button type="button" class="delete-action" data-id="${a.id}">Annuler</button></td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('nb-actions').textContent = actions.length;
    document.getElementById('save-all').disabled = actions.length === 0;

    document.querySelectorAll('.delete-action').forEach(btn => {
        btn.addEventListener('click', () => {
            actions = actions.filter(a => a.id != btn.dataset.id);
            render();
        });
    });
}

document.getElementById('save-all').addEventListener('click', async () => {
    const statusEl = document.getElementById('save-status');
    statusEl.textContent = 'Enregistrement...';

    try {
        const res = await fetch('/match/live/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `match_id=${matchId}&actions=${encodeURIComponent(JSON.stringify(actions))}`
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = '/match?id=' + matchId;
        } else {
            statusEl.textContent = 'Erreur : ' + (data.error || 'inconnue');
        }
    } catch (e) {
        statusEl.textContent = 'Erreur réseau, réessaie.';
    }
});
</script>

<?php
$pageTitle = "Saisie live";
$content = ob_get_clean();
$css = "live.css";
require __DIR__ . '/../layout.php';
?>