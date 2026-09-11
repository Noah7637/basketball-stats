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
    <button type="button" id="toggle-stats-panel"><img src="/assets/img/statistics.png" alt="Stats" class="live-circle"> Stats du match</button>
</div>

<div id="stats-panel" class="stats-panel" style="display:none;">
    <h2>Stats en direct (non enregistrées)</h2>
    <p class="stats-panel-note">Calculées à partir des actions du journal ci-dessous, pas encore sauvegardées en base.</p>

    <h3>Score par quart-temps</h3>
    <table class="borderless-header-table">
        <thead>
            <tr><th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th><th>Total</th></tr>
        </thead>
        <tbody>
            <tr id="stats-points-quart"><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
        </tbody>
    </table>

    <h3>Par joueur</h3>
    <table class="borderless-header-table">
        <thead>
            <tr><th>Joueur</th><th>Pts</th><th>2pts</th><th>3pts</th><th>LF</th><th>Passes</th><th>Duels</th><th>Sur le terrain</th></tr>
        </thead>
        <tbody id="stats-joueurs"></tbody>
    </table>
</div>

<div class="live-court-split">
    <div class="live-court-column">
        <h2><img src="/assets/img/green-circle.png" alt="" class="live-circle"> Sur le terrain</h2>
        <div class="live-players" id="joueurs-terrain">
            <p class="live-empty-note">Aucun joueur sur le terrain.</p>
        </div>
    </div>

    <div class="live-court-column">
        <h2><img src="/assets/img/empty-circle.png" alt="" class="live-circle"> Sur le banc</h2>
        <div class="live-players" id="joueurs-banc"></div>
    </div>
</div>

<div id="live-panel" class="live-panel" style="display:none;">
    <h2 id="live-player-name"></h2>

    <div class="live-action-group live-substitution-group">
        <div class="form-group">
            <label for="minute-match">Minute du match (pour l'entrée/sortie)</label>
            <input type="number" id="minute-match" min="0" max="60" value="0" style="width:100px">
        </div>
        <button type="button" id="btn-entree" class="live-substitution-btn"><img src="/assets/img/green-circle.png" alt="" class="live-circle"> Entrée sur le terrain</button>
        <button type="button" id="btn-sortie" class="live-substitution-btn"><img src="/assets/img/empty-circle.png" alt="" class="live-circle"> Sortie du terrain</button>
    </div>

    <p id="live-actions-warning" class="live-actions-warning" style="display:none;">
        ⚠️ Ce joueur est sur le banc — fais-le entrer sur le terrain avant d'ajouter une action.
    </p>

    <div id="zone-actions-joueur">
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
const tousLesJoueurs = <?= json_encode($joueurs) ?>;
const joueursNoms = <?= json_encode(array_column($joueurs, 'nom', 'id')) ?>;

let actions = [];
let joueurActifId = null;
let selection = { type: null, reussi: null };
let joueursSurLeTerrain = new Set();

const storageKey = `live-actions-match-${matchId}`;

const sauvegarde = localStorage.getItem(storageKey);
if (sauvegarde) {
    try {
        actions = JSON.parse(sauvegarde);
    } catch (e) {
        actions = [];
    }
}

const libellesType = {
    '2pts': '2 pts', '3pts': '3 pts', 'lf': 'Lancer franc',
    'possession': 'Nouvelle possession',
    'passe': 'Passe décisive', 'duel': 'Duel défensif gagné',
    'rebond_def': 'Rebond défensif (équipe)', 'rebond_off_adv': 'Rebond off. adverse (équipe)',
    'entree': 'Entrée sur le terrain', 'sortie': 'Sortie du terrain',
};
const libellesPossession = { transition: 'Transition', jeu_pose: 'Jeu posé', contre_attaque: 'Contre-attaque' };

function quartActif() {
    return parseInt(document.getElementById('quart-select').value, 10);
}

function possessionActive() {
    return document.getElementById('possession-select').value;
}

function minuteMatch() {
    return parseInt(document.getElementById('minute-match').value, 10) || 0;
}

function recalculerJoueursSurLeTerrain() {
    joueursSurLeTerrain = new Set();
    actions.forEach(a => {
        if (a.type === 'entree') joueursSurLeTerrain.add(a.joueur_id);
        if (a.type === 'sortie') joueursSurLeTerrain.delete(a.joueur_id);
    });
    renderJoueursColonnes();
}

// Affiche les joueurs répartis dans les deux colonnes (terrain / banc)
function renderJoueursColonnes() {
    const terrainEl = document.getElementById('joueurs-terrain');
    const bancEl = document.getElementById('joueurs-banc');

    const surLeTerrain = tousLesJoueurs.filter(j => joueursSurLeTerrain.has(j.id));
    const surLeBanc = tousLesJoueurs.filter(j => !joueursSurLeTerrain.has(j.id));

    terrainEl.innerHTML = surLeTerrain.length
        ? surLeTerrain.map(j => boutonJoueurHtml(j)).join('')
        : '<p class="live-empty-note">Aucun joueur sur le terrain.</p>';

    bancEl.innerHTML = surLeBanc.map(j => boutonJoueurHtml(j)).join('');

    attacherEcouteursJoueurs();

    // Réapplique le style "actif" si un joueur était sélectionné
    if (joueurActifId) {
        const btn = document.querySelector(`.live-player-btn[data-joueur-id="${joueurActifId}"]`);
        if (btn) btn.classList.add('active');
    }
}

function boutonJoueurHtml(j) {
    const numero = j.numero !== null ? ` (#${j.numero})` : '';
    return `<button type="button" class="live-player-btn" data-joueur-id="${j.id}">${j.nom}${numero}</button>`;
}

function attacherEcouteursJoueurs() {
    document.querySelectorAll('.live-player-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.live-player-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            joueurActifId = btn.dataset.joueurId;
            document.getElementById('live-player-name').textContent = joueursNoms[joueurActifId];
            document.getElementById('live-panel').style.display = 'block';
            resetSelection();
            updateSubstitutionButtons();
            updateActionsAvailability();
        });
    });
}

// Bloque les tirs/passes/duels tant que le joueur sélectionné n'est pas sur le terrain
function updateActionsAvailability() {
    const surLeTerrain = joueursSurLeTerrain.has(parseInt(joueurActifId, 10));
    const zoneActions = document.getElementById('zone-actions-joueur');

    zoneActions.classList.toggle('live-actions-disabled', !surLeTerrain);

    document.querySelectorAll('#zone-actions-joueur .live-toggle, #quick-passe, #quick-duel').forEach(btn => {
        btn.disabled = !surLeTerrain;
    });

    document.getElementById('live-actions-warning').style.display = surLeTerrain ? 'none' : 'block';

    if (!surLeTerrain) {
        document.getElementById('valider-action').disabled = true;
    } else {
        verifierCompletude();
    }
}

document.getElementById('nouvelle-possession').addEventListener('click', () => {
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: null,
        type: 'possession',
        reussi: null,
        type_possession: possessionActive(),
        valeur_temps: null,
    });
    render();
});

function updateSubstitutionButtons() {
    const surLeTerrain = joueursSurLeTerrain.has(parseInt(joueurActifId, 10));
    document.getElementById('btn-entree').disabled = surLeTerrain;
    document.getElementById('btn-sortie').disabled = !surLeTerrain;
}

document.getElementById('btn-entree').addEventListener('click', () => {
    const id = parseInt(joueurActifId, 10);
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: id,
        type: 'entree',
        reussi: null,
        type_possession: null,
        valeur_temps: minuteMatch(),
    });
    joueursSurLeTerrain.add(id);
    renderJoueursColonnes();
    updateSubstitutionButtons();
    updateActionsAvailability();
    render();
});

document.getElementById('btn-sortie').addEventListener('click', () => {
    const id = parseInt(joueurActifId, 10);
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: id,
        type: 'sortie',
        reussi: null,
        type_possession: null,
        valeur_temps: minuteMatch(),
    });
    joueursSurLeTerrain.delete(id);
    renderJoueursColonnes();
    updateSubstitutionButtons();
    updateActionsAvailability();
    render();
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
    if (!joueursSurLeTerrain.has(parseInt(joueurActifId, 10))) {
        alert('Ce joueur est sur le banc, fais-le entrer sur le terrain d\'abord.');
        return;
    }

    const typePossession = selection.type === 'lf' ? null : possessionActive();

    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: parseInt(joueurActifId, 10),
        type: selection.type,
        reussi: selection.reussi === '1',
        type_possession: typePossession,
        valeur_temps: null,
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
    if (!joueursSurLeTerrain.has(parseInt(joueurActifId, 10))) {
        alert('Ce joueur est sur le banc, fais-le entrer sur le terrain d\'abord.');
        return;
    }
    actions.push({
        id: Date.now() + Math.random(),
        quart_temps: quartActif(),
        joueur_id: parseInt(joueurActifId, 10),
        type: type,
        reussi: true,
        type_possession: null,
        valeur_temps: null,
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
        valeur_temps: null,
    });
    render();
}

document.getElementById('toggle-stats-panel').addEventListener('click', () => {
    const panel = document.getElementById('stats-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    if (panel.style.display === 'block') updateStatsPanel();
});

function updateStatsPanel() {
    const pointsParQuart = { 1: 0, 2: 0, 3: 0, 4: 0 };
    const parJoueur = {};

    actions.forEach(a => {
        if (a.joueur_id !== null && !parJoueur[a.joueur_id]) {
            parJoueur[a.joueur_id] = { points: 0, deux: 0, trois: 0, lf: 0, passes: 0, duels: 0 };
        }

        if ((a.type === '2pts' || a.type === '3pts' || a.type === 'lf') && a.reussi) {
            const pts = a.type === '2pts' ? 2 : (a.type === '3pts' ? 3 : 1);
            pointsParQuart[a.quart_temps] = (pointsParQuart[a.quart_temps] || 0) + pts;
            if (a.joueur_id !== null) {
                parJoueur[a.joueur_id].points += pts;
                if (a.type === '2pts') parJoueur[a.joueur_id].deux++;
                if (a.type === '3pts') parJoueur[a.joueur_id].trois++;
                if (a.type === 'lf') parJoueur[a.joueur_id].lf++;
            }
        }
        if (a.type === 'passe' && a.joueur_id !== null) parJoueur[a.joueur_id].passes++;
        if (a.type === 'duel' && a.joueur_id !== null) parJoueur[a.joueur_id].duels++;
    });

    const total = pointsParQuart[1] + pointsParQuart[2] + pointsParQuart[3] + pointsParQuart[4];
    document.getElementById('stats-points-quart').innerHTML =
        `<td>${pointsParQuart[1]}</td><td>${pointsParQuart[2]}</td><td>${pointsParQuart[3]}</td><td>${pointsParQuart[4]}</td><td><strong>${total}</strong></td>`;

    const tbody = document.getElementById('stats-joueurs');
    tbody.innerHTML = '';
    Object.keys(parJoueur).forEach(id => {
        const s = parJoueur[id];
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${joueursNoms[id]}</td>
            <td>${s.points}</td>
            <td>${s.deux}</td>
            <td>${s.trois}</td>
            <td>${s.lf}</td>
            <td>${s.passes}</td>
            <td>${s.duels}</td>
            <td>${joueursSurLeTerrain.has(parseInt(id, 10)) ? '<img src="/assets/img/green-circle.png" alt="" class="live-circle">' : '-'}</td>
        `;
        tbody.appendChild(tr);
    });
}

function render() {
    const tbody = document.getElementById('live-log');
    tbody.innerHTML = '';

    const actionsTriees = [...actions].sort((a, b) => a.quart_temps - b.quart_temps);
    let dernierQuart = null;

    actionsTriees.forEach(a => {
        if (a.quart_temps !== dernierQuart) {
            dernierQuart = a.quart_temps;
            const trSep = document.createElement('tr');
            trSep.className = 'live-quart-separator';
            trSep.innerHTML = `<td colspan="6">Quart-temps ${a.quart_temps}</td>`;
            tbody.appendChild(trSep);
        }

        const tr = document.createElement('tr');
        const nomJoueur = a.joueur_id !== null ? joueursNoms[a.joueur_id] : '— Équipe —';
        const sansResultat = ['passe', 'duel', 'rebond_def', 'rebond_off_adv', 'possession', 'entree', 'sortie'].includes(a.type);
        let resultat = '-';
        if (!sansResultat) resultat = a.reussi ? '✅' : '❌';
        if (a.type === 'entree' || a.type === 'sortie') resultat = `min. ${a.valeur_temps}`;

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
            recalculerJoueursSurLeTerrain();
            render();
        });
    });

    localStorage.setItem(storageKey, JSON.stringify(actions));

    if (document.getElementById('stats-panel').style.display !== 'none') {
        updateStatsPanel();
    }
}

recalculerJoueursSurLeTerrain();
render();

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
            localStorage.removeItem(storageKey);
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
$title = "Saisie live";
$css = "live.css";
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>