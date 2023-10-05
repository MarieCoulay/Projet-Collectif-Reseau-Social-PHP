<?php
include "session.php"
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Flux</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'navbar.php' ?>
    </header>
    <div id="wrapper">
        <?php

        // Etape 1: Le mur concerne un utilisateur en particulier
        //$userId = intval($_GET['user_id']);
        $userId = intval($userId);
        ?>
        <?php
        /**
         * Etape 2: se connecter à la base de donnée
         */
        include "connect_database.php";
        ?>

        <aside>
            <?php
            /**
             * Etape 3: récupérer le nom de l'utilisateur
             */
            $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
            include "query_database.php";
            $user = $lesInformations->fetch_assoc();
            // echo "<pre>" . print_r($user, 1) . "</pre>";
            ?>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <p>Sur cette page vous trouverez tous les message des utilisatrices
                    auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                    (n° <?php echo $userId ?>)
                </p>

            </section>
        </aside>
        <main>
            <?php
            /**
             * Etape 3: récupérer tous les messages des abonnements
             */
            $laQuestionEnSql = "
                    SELECT posts.content, users.id as userId,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            include "query_database.php";

            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            ?>
            <?php

            while ($post = $lesInformations->fetch_assoc()) {
                // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <?php include "article.php"; ?>
            <?php
            }
            ?>


        </main>
    </div>
</body>

</html>