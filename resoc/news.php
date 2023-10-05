<?php
include "session.php"
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
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
                <p>Sur cette page vous trouverez les derniers messages de
                    tous les utilisatrices du site.</p>
            </section>
        </aside>
        <main>
            <?php
            // Etape 1: Ouvrir une connexion avec la base de donnée.
            //localhost=>nom du serveur; root/root=>num user + mdp; socialnetwork=> base de données
            include "connect_database.php";
            //verification
            if ($mysqli->connect_errno) // Si absence d'erreur retourne 0 (false) sinon retourne le code erreur
            {
                echo "<article>";
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                echo "</article>";
                exit();
            }

            // Etape 2: Poser une question à la base de donnée et récupérer ses informations
            $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.id as userId, users.alias as author_name, count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
            include "query_database.php";

            // Vérification
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }

            // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
            // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
            while ($post = $lesInformations->fetch_assoc()) //fetch_assoc => associe le tableau nouvellement créé à une variable $post
            {

                // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <?php include "article.php"; ?>
            <?php
                // avec le <?php ci-dessus on retourne en mode php 
            } // cette accolade ferme et termine la boucle while ouverte avant.
            ?>

        </main>
    </div>
</body>

</html>