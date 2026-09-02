<?php ob_start(); ?>

<div class="auth-card">
    <h1>Créer un compte</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/register">
        <div class="form-group">
            <label for="nom_equipe">Nom de l'équipe</label>
            <input type="text" id="nom_equipe" name="nom_equipe"
                   value="<?= htmlspecialchars($old['nom_equipe'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe (8 caractères minimum)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <button type="submit">Créer mon compte</button>
    </form>

    <p class="auth-switch">Déjà un compte ? <a href="/login">Se connecter</a></p>
</div>

<?php
$title = "Inscription";
$content = ob_get_clean();
$css = "auth.css";
require __DIR__ . '/../layout.php';
?>