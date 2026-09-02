<?php

ob_start();

?>

<h1>Modifier le joueur</h1>


<?php if (!empty($errors)): ?>

    <ul class="errors">

        <?php foreach ($errors as $error): ?>

            <li>
                <?= htmlspecialchars($error) ?>
            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>


<form method="POST" action="/joueur/update">

    <input
        type="hidden"
        name="joueur_id"
        value="<?= (int) $joueur['id'] ?>"
    >


    <div class="form-group">

        <label for="nom">
            Nom complet
        </label>

        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars(
                $_POST['nom'] ?? $joueur['nom']
            ) ?>"
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
                value="<?= htmlspecialchars(
                    $_POST['numero']
                    ?? $joueur['numero']
                    ?? ''
                ) ?>"
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

                $posteActuel =
                    $_POST['poste']
                    ?? $joueur['poste']
                    ?? '';

                ?>

                <?php foreach ($postes as $poste): ?>

                    <option
                        value="<?= htmlspecialchars($poste) ?>"
                        <?= $posteActuel === $poste
                            ? 'selected'
                            : '' ?>
                    >
                        <?= htmlspecialchars($poste) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>


    <button class="submit" type="submit">
        Enregistrer les modifications
    </button>

</form>


<p>

    <a href="/joueur?id=<?= (int) $joueur['id'] ?>">
        ← Annuler
    </a>

</p>


<?php

$title = "Modifier joueur";

$css = "create_edit.css";

$content = ob_get_clean();

require __DIR__ . '/../layout.php';