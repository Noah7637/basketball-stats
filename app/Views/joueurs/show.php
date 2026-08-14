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

        <span class="info-label">
            Numéro
        </span>

        <span class="info-value">

            <?= $joueur['numero'] !== null
                ? '#' . (int) $joueur['numero']
                : '-' ?>

        </span>

    </div>


    <div class="info-card">

        <span class="info-label">
            Poste
        </span>

        <span class="info-value">
            <?= htmlspecialchars($joueur['poste'] ?? '-') ?>
        </span>

    </div>

</div>


<section class="content-card">

    <h2>Record dans la saison</h2>

    <p class="muted">
        Les statistiques de la saison seront disponibles ici.
    </p>

</section>


<a class="back-link" href="/">
    ← Retour à l'équipe
</a>


<?php

$title = "Détails joueur";

$css = "show.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';