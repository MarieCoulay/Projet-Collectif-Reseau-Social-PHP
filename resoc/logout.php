<?php
include "session.php";
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'navbar.php' ?>
    </header>
    <?php
    $_SESSION['connected_id'] = 0;
    $_USER = 0;
    ?>
    <div id="wrapper">

        <aside>
            <h2>Veuillez vous connecter.</h2>
            <a href="login.php">Connectez-vous ici.</a>
        </aside>
    </div>
</body>