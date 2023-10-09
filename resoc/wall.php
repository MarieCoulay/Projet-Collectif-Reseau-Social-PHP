<?php
include "session.php"
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
    <div id="wrapper">
        <?php
        include "connect_database.php";
        ?>

        <aside>
            <?php
            $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId'";
            include "query_database.php";
            $user = $lesInformations->fetch_assoc();
            ?>
            <img src="user.png" alt="Portrait de l'utilisatrice" />
            <section>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                    (n°<?php echo $userId ?>)
                </p>
            </section>
            <?php 
            $connectedUser = $_SESSION['connected_id'];
            $requestFollow = "INSERT INTO followers(id, followed_user_id, following_user_id) VALUES (NULL, '$userId', '$connectedUser');";
            $lesInformationsFollow = $mysqli->query($requestFollow);
            ?>
            <form method="post" action="wall.php?user_id=<?php echo $followerId ?>">
                <button type=submit >S'abonner</button>
            </form>
        </aside>
        <main>
            <?php
            // génère le mur de l'utilisateur
            $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, users.id as userId,
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            include "query_database.php";
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            while ($post = $lesInformations->fetch_assoc()) {
            ?>
                <?php include "article.php"; ?>
            <?php } ?>

            <!-- Ajout d'un post sur le mur -->
            <?php $enCoursDeTraitement = isset($userId);
            if ($enCoursDeTraitement && isset($_POST['message']) && !empty($_POST['message'])) {
                echo "<pre>" . print_r($_POST, 1) . "</pre>";
                $postContent = $_POST['message'];
                $postContent = $mysqli->real_escape_string($postContent);
                header("refresh: 0");

                $lInstructionSql = "INSERT INTO posts(id, user_id, content, created)
            VALUES (NULL, $userId, '$postContent', NOW());";

                $ok = $mysqli->query($lInstructionSql);
                if (!$ok) {
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                } else {
                    echo "Message posté";
                }
            }
            ?>
            <!-- Bloc d'input du post à ajouter -->
            <form action="wall.php" method="post">
                <dl>
                    <dt><label for='message'>Message</label></dt>
                    <dd><textarea name='message'></textarea></dd>
                </dl>
                <input type='submit' onchange="location.reload()">
            </form>
        </main>
    </div>
</body>

</html>