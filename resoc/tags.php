<?php
include "session.php"
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Les message par mot-clé</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'navbar.php' ?>
    </header>
    <div id="wrapper">
        <?php
        /**
         * Cette page est similaire à wall.php ou feed.php 
         * mais elle porte sur les mots-clés (tags)
         */
        /**
         * Etape 1: Le mur concerne un mot-clé en particulier
         */
        $tagId = intval($_GET['tag_id']);
        ?>
        <?php
        /**
         * Etape 2: se connecter à la base de donnée
         */
        include "connect_database.php";
        ?>

        <aside>
            <img src="user.png" alt="Portrait de l'utilisatrice" />
            <p>Mots-clés populaires:</p>

            <?php
            /**
             * Etape 3: récupérer le nom du mot-clé
             */
            $laQuestionEnSql = "SELECT label, id FROM tags"; //WHERE id= '$tagId' 
            include "query_database.php";
            while ($tag = $lesInformations->fetch_assoc()) {
            ?>
                <li><a href="tags.php?tag_id=<?php echo $tag['id'] ?>"><?php echo $tag['label'] ?>></li>
            <?php } ?>


            <section>

            </section>
        </aside>
        <main>
            <?php
            /**
             * Etape 3: récupérer tous les messages avec un mot clé donné
             */
            $laQuestionEnSql = "
                    SELECT posts.content, users.id as userId,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            include "query_database.php";
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Etape 4: Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {

                // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <?php include "article.php"; ?>
            <?php } ?>


        </main>
    </div>
</body>

</html>