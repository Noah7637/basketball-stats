<?php

ob_start();

?>

<h1>Ajouter un joueur</h1>


<?php if (!empty($errors)): ?>

    <ul class="errors">

        <?php foreach ($errors as $error): ?>

            <li>
                <?= htmlspecialchars($error) ?>
            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>


<form method="POST" action="/joueurs/store">

    <div class="form-group">

        <label for="nom">
            Nom
        </label>

        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
            required
        >

    </div>


    <div class="form-row">

        <div class="form-group">

            <label for="numero">
                Numéro de maillot
            </label>

            <input
                type="number"
                id="numero"
                name="numero"
                min="0"
                value="<?= htmlspecialchars($old['numero'] ?? '') ?>"
            >

        </div>


        <div class="form-group">

            <label for="poste">
                Poste
            </label>

            <select id="poste" name="poste">

                <option value="">
                    —
                </option>

                <?php

                $postes = [
                    'Meneur',
                    'Arrière',
                    'Ailier',
                    'Ailier fort',
                    'Pivot'
                ];

                ?>

                <?php foreach ($postes as $poste): ?>

                    <option
                        value="<?= htmlspecialchars($poste) ?>"
                        <?= (($old['poste'] ?? '') === $poste)
                            ? 'selected'
                            : '' ?>
                    >
                        <?= htmlspecialchars($poste) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>


    <button type="submit">
        Ajouter le joueur
    </button>

</form>


<p>
    <a href="/">
        ← Retour à l'équipe
    </a>
</p>


<?php

$title = "Ajouter joueur";

$css = "create_edit.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';