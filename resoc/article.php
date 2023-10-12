<article>
    <h3>
        <time><?php echo $post['created'] ?></time>
    </h3>
    <address>par <a href="wall.php?user_id=<?php echo $post['userId'] ?>"><?php echo $post['author_name'] ?></a></address>
    <div>
        <p><?php echo $post['content'] ?></p>
    </div>
    <footer>
        <small><img id="like_image" src="like_image.png"> <?php echo $post['like_number'] ?></small>
        <a href="#">#<?php echo $post['taglist'] ?></a>
    </footer>
    <?php
           if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérez l'ID du post à aimer ou ne plus aimer depuis les données POST
            $postIdToLike = $post['id'];
            // Effectuez une requête SQL pour vérifier si l'utilisateur a déjà aimé le post
            $checkLikeQuery = $postIdToLike;
            $result = $mysqli->query($checkLikeQuery);

            if ($result->num_rows > 0) {
                // L'utilisateur a déjà aimé le post, effectuez la requête pour ne plus aimer
                $requestUnlike = "DELETE FROM likes WHERE post_id = '$postIdToLike' AND user_id = '$connectedUserId'";
                $ok = $mysqli->query($requestUnlike);

                if (!$ok) {
                    echo "Impossible de ne plus aimer le post " . $mysqli->error;
                } else {
                    echo "Post retiré des posts aimés";
                }
            } else {
                // L'utilisateur n'a pas encore aimé le post, effectuez la requête pour aimer
                $requestLike = "INSERT INTO likes (post_id, user_id) VALUES ('$postIdToLike', '$connectedUserId')";
                $ok = $mysqli->query($requestLike);

                if (!$ok) {
                    echo "Impossible d'aimer le post " . $mysqli->error;
                } else {
                    echo "Post aimé";
                }
            }
        }
        // Déterminer le texte du bouton en fonction de l'état d'aimer ou ne plus aimer
        $checkLikeQuery = "SELECT * FROM likes WHERE post_id = '$postIdToLike' AND user_id = '$connectedUserId'";
        $result = $mysqli->query($checkLikeQuery);
        $buttonText = ($result->num_rows > 0) ? "Je n'aime plus" : "J'aime: W.I.P";
    ?>

    <form method="post" action="">
        <input type="hidden" name="<?php echo $postIdToLike ?>" value="<?php echo $postIdToLike ?>">
        <button type="submit"><?php echo $buttonText; ?></button>
    </form>
</article>
