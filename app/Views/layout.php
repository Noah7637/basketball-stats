<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($title) ?></title>

    <!-- CSS commun à toutes les pages -->
    <link
        rel="stylesheet"
        href="/assets/css/layout.css"
    >

    <!-- CSS spécifique à la page -->
    <?php if (isset($css)): ?>

        <link
            rel="stylesheet"
            href="/assets/css/<?= htmlspecialchars($css) ?>"
        >

    <?php endif; ?>

</head>


<body>

    <header class="side-header">

        <h1>Coach Assisting</h1>

        <nav>

            <a href="/">
                Joueurs
            </a>

            <a href="/matches">
                Matchs
            </a>

        </nav>

        <div class="user-info">
            Connecté pour <?= htmlspecialchars($_SESSION['nom_equipe'] ?? '') ?>
            <form method="POST" action="/logout" style="display:inline">
                <button type="submit">Déconnexion</button>
            </form>
        </div>

    </header>


    <main>

        <?= $content ?>

    </main>

</body>

</html>

<?php

unset($_SESSION['error']);
unset($_SESSION['old']);