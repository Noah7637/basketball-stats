<?php

ob_start();

?>

<form
    class="selection-form"
    method="POST"
    action="/match/selection"
>

    <h1>Sélection des joueurs</h1>

    <input
        type="hidden"
        name="match_id"
        value="<?= (int) $match['id'] ?>"
    >


    <?php foreach ($joueurs as $j): ?>

        <label class="selection-player">

            <input
                type="checkbox"
                name="joueurs[]"
                value="<?= (int) $j['id'] ?>"
            >

            <?= htmlspecialchars($j['nom']) ?>

            <?php if ($j['numero'] !== null): ?>

                (#<?= (int) $j['numero'] ?>)

            <?php endif; ?>

        </label>

    <?php endforeach; ?>


    <button type="submit">
        Valider la sélection
    </button>

</form>


<?php

$title = "Sélection des joueurs";

$css = "selection.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';