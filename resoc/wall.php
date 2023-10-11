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
            // var_dump($_GET);
            // userId stocke l'id de l'utilisateur dont on visionne le mur  
            $userId = $_GET['user_id'];

            if ($connectedUserId != $userId) {
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId'";
            } else {
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$connectedUserId'";
            }
            include "query_database.php";
            $user = $lesInformations->fetch_assoc();
            ?>
            <img src="user.png" alt="Portrait de l'utilisatrice" />
            <section>
                <!-- ici c'est authorId qui doit être affiché -->
                <?php
                if ($connectedUserId != $userId) {
                ?>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                        (n°<?php echo $userId ?>)
                    </p>
                <?php } else { ?>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                        (n°<?php echo $connectedUserId ?>)
                    </p>
                <?php } ?>
            </section>
            <!-- S'ABONNER -->
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Récupérez l'ID de l'utilisateur à suivre depuis les données POST
                    $userIdToFollow = $_POST["user_id_to_follow"];

                    // Effectuez une requête SQL pour vérifier si l'utilisateur est déjà abonné
                    $checkFollowQuery = "SELECT * FROM followers WHERE followed_user_id = '$userIdToFollow' AND following_user_id = '$connectedUserId'";
                    $result = $mysqli->query($checkFollowQuery);

                    if ($result->num_rows > 0) {
                        // L'utilisateur est déjà abonné, effectuez la requête pour le désabonner
                        $requestUnfollow = "DELETE FROM followers WHERE followed_user_id = '$userIdToFollow' AND following_user_id = '$connectedUserId';";
                        $ok = $mysqli->query($requestUnfollow);

                        if (!$ok) {
                            echo "Impossible de se désabonner : " . $mysqli->error;
                        } else {
                            echo "Vous êtes désabonné";
                        }
                    } else {
                        // L'utilisateur n'est pas abonné, effectuez la requête pour l'abonner
                        $requestFollow = "INSERT INTO followers (followed_user_id, following_user_id) VALUES ('$userIdToFollow','$connectedUserId');";
                        $ok = $mysqli->query($requestFollow);

                        if (!$ok) {
                            echo "Impossible de s'abonner : " . $mysqli->error;
                        } else {
                            echo "Vous êtes abonné";
                        }
                    }
                }

                // Déterminer le texte du bouton en fonction de l'état d'abonnement
                $checkFollowQuery = "SELECT * FROM followers WHERE followed_user_id = '$userId' AND following_user_id = '$connectedUserId'";
                $result = $mysqli->query($checkFollowQuery);
                $buttonText = ($result->num_rows > 0) ? "Se désabonner" : "S'abonner";
                ?>

                <form method="post" action="">
                    <input type="hidden" name="user_id_to_follow" value="<?php echo $userId ?>">
                    <button type="submit"><?php echo $buttonText; ?></button>
                </form>

        </aside>
        <main>
            <!-- Ajout d'un post sur le mur -->
            <?php $enCoursDeTraitement = isset($connectedUserId);
            if ($enCoursDeTraitement && isset($_POST['message']) && !empty($_POST['message'])) {
                //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                //var_dump($_POST);
                $postContent = $_POST['message'];
                $postContent = $mysqli->real_escape_string($postContent);
                header("refresh: 0");

                $lInstructionSql = "INSERT INTO posts(id, user_id, content, created)
                VALUES (NULL, $connectedUserId, '$postContent', NOW());";

                $ok = $mysqli->query($lInstructionSql);
                if (!$ok) {
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                } else {
                    echo "Message posté";
                }
            }
            ?>
            <!-- Bloc d'input du post à ajouter -->
            <?php if ($connectedUserId == $userId) {
            ?>
                <article>
                    <form action="wall.php?user_id=<?php echo $connectedUserId ?>" method="post">
                        <dl>
                            <dt><label for='message' id="posterMessage">Postez un message:</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit'>
                        <!-- onchange="location.reload()" -->
                    </form>
                </article>
            <?php } ?>

            <?php
            // génère le mur de l'utilisateur
            if ($connectedUserId != $userId) {
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
            } else {
                $laQuestionEnSql = "
                SELECT posts.content, posts.created, users.alias as author_name, users.id as userId,
                COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                FROM posts
                JOIN users ON  users.id=posts.user_id
                LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                LEFT JOIN likes      ON likes.post_id  = posts.id 
                WHERE posts.user_id='$connectedUserId' 
                GROUP BY posts.id
                ORDER BY posts.created DESC  
                ";
            }
            include "query_database.php";
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            while ($post = $lesInformations->fetch_assoc()) {
            ?>
                <?php include "article.php"; ?>
            <?php } ?>
        </main>
    </div>
</body>

</html>