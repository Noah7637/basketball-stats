<?php ob_start(); ?>

<div class="auth-card">
    <h1>Connexion</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/login">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <button type="submit">Se connecter</button>

    </form>

    <p class="auth-switch">
        Pas encore de compte ?
        <a href="/register">Créer un compte</a>
    </p>
</div>

<?php
$title = "Connexion";
$content = ob_get_clean();
$css = "auth.css";
require __DIR__ . '/../layout.php';
?>