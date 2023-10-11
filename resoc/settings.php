<?php
include "session.php"
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Paramètres</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'navbar.php' ?>
    </header>
    <div id="wrapper" class='profile'>


        <aside>
            <img src="user.png" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les informations de l'utilisatrice
                    n° <?php echo intval($_GET['user_id']) ?></p>
            </section>
        </aside>
        <main>
            <?php
            include "connect_database.php";

            $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$connectedUserId' 
                    GROUP BY users.id
                    ";
            include "query_database.php";
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            $user = $lesInformations->fetch_assoc();

            ?>
            <article class='parameters'>
                <h3>Mes paramètres</h3>
                <dl>
                    <dt>Pseudo</dt>
                    <dd><?php echo $user['alias'] ?></dd>
                    <dt>Email</dt>
                    <dd><?php echo $user['email'] ?></dd>
                    <dt>Nombre de message</dt>
                    <dd><?php echo $user['totalpost'] ?></dd>
                    <dt>Nombre de "J'aime" donnés </dt>
                    <dd><?php echo $user['totalgiven'] ?></dd>
                    <dt>Nombre de "J'aime" reçus</dt>
                    <dd><?php echo $user['totalrecieved'] ?></dd>
                </dl>

            </article>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Désactivez les contraintes de clé étrangère temporairement
                $mysqli->query("SET foreign_key_checks = 0");

                // Effectuez des requêtes SQL pour supprimer tous les éléments de l'utilisateur
                $deleteFollowersQuery = "DELETE FROM followers WHERE follower_user_id = '$connectedUserId'";
                $deleteLikesQuery = "DELETE FROM likes WHERE user_id = '$connectedUserId'";
                $deleteUserPostsQuery = "DELETE FROM posts WHERE user_id = '$connectedUserId'";
                $deleteUserQuery = "DELETE FROM users WHERE id = '$connectedUserId'";

                // Exécutez les requêtes dans l'ordre inverse de la dépendance
                $okFollowers = $mysqli->query($deleteFollowersQuery);
                $okLikes = $mysqli->query($deleteLikesQuery);
                $okUserPosts = $mysqli->query($deleteUserPostsQuery);
                $okUser = $mysqli->query($deleteUserQuery);

                // Réactivez les contraintes de clé étrangère
                $mysqli->query("SET foreign_key_checks = 1");

                header("Location: logout.php");

                // Vérifiez si toutes les requêtes ont réussi
                if ($okFollowers && $okLikes && $okUserPosts && $okUser) {
                    echo "Ce compte a été supprimé avec succès";
                } else {
                    echo "Impossible de supprimer le compte : " . $mysqli->error;
                }
            }
            ?>
            <form method="post" action="">
                <input type="hidden" name="connectedUserId" value="<?php echo $connectedUserId ?>">
                <button type="submit">Supprimer son compte</button>
            </form>



        </main>
    </div>
</body>

</html>