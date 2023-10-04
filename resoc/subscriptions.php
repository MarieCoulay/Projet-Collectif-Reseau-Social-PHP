<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mes abonnements</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'navbar.php' ?>
    </header>
    <div id="wrapper">
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <p>
                    Sur cette page vous trouverez la liste des personnes dont
                    l'utilisatrice
                    n° <?php echo intval($_GET['user_id']) ?>
                    suit les messages
                </p>

            </section>
        </aside>
        <main class='contacts'>
            <?php
            // Etape 1: récupérer l'id de l'utilisateur
            $userId = intval($_GET['user_id']);
            // Etape 2: se connecter à la base de donnée
            include "connect_database.php";
            // Etape 3: récupérer le nom de l'utilisateur
            $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
            include "query_database.php";
            // Etape 4: boucle while de parcours des abonnés  
            ?>
            <?php
              while ($userId = $lesInformations->fetch_assoc())
            {

                // echo "<pre>" . print_r($userId, 1) . "</pre>";
            ?>
                <article>
                <img src="user.jpg" alt="blason" />
                <h3><?php echo $userId['alias'] ?></h3>
                <p>id:<?php echo $userId['id'] ?></p>
                </article>
            <?php
            } 
            ?>
            
        </main>
    </div>
</body>

</html>