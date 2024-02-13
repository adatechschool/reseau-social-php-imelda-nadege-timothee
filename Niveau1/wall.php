<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php 
        include './header.php'; 
        include './displayPosts.php';
        ?>
        <div id="wrapper">
            <?php
            session_start();
            $scheme = $_SERVER['REQUEST_SCHEME'];
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            $current_url = "$scheme://$host$uri";
            
            $userId = intval($_GET['user_id']);
            $connected_id = intval($_SESSION['connected_id']);

            ?>

            <?php

            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            ?>

            <aside>
                <?php
                
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous vos message</p>
                </section>

                <?php 
                    $addFollower = 'INSERT INTO followers (followed_user_id, following_user_id) '
                    . "VALUES ('$userId', '$connected_id')";

                    if ($userId != $connected_id && isset($_POST['follow'])){
                        $ok = $mysqli->query($addFollower);
                        if (! $ok) {echo "Il y a eu un problème lors du follow : " . $mysqli->error;}
                        else {echo "Vous avez follow avec succès";}
                    }
                ?>
                <?php if($userId != $connected_id){ ?>
                    <form action="wall.php?user_id=<?php echo $userId?>" method="post">
                        <p><input type="submit" value="Follow" name="follow"></p>
                    </form>
                <?php }else{ ?> 
                    <a href="usurpedpost.php"><p>Publier un message</p></a>
                <?php } ?>
            </aside>
            <main>
                <?php

                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, posts.id,
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
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }


                while ($post = $lesInformations->fetch_assoc()){
                    $post_id = $post['id'];

                    if (isset($_POST["like_$post_id"])) {
                        $addLike = $mysqli->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
                        $addLike->bind_param("ii", $connected_id, $post_id);
                        $addLike->execute();

                        $getNumLike = "SELECT COUNT(id) as like_number FROM likes WHERE post_id = $post_id";
                        $result = $mysqli->query($getNumLike);

                        if ($result) {
                            $newLikeCount = $result->fetch_assoc()['like_number'];
                            $post['like_number'] = $newLikeCount;
                        }
                    }

                    ?>                
                    <article>
                        <h3>
                            <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                        </h3>
                        <address>par <?php echo $post['author_name']?></address>
                        <div>
                            <p><?php echo $post['content']?></p>
                            
                        </div>                                            
                        <form action="<?php echo $current_url?>" method="post">
                            <footer>
                                <small>     
                                    <input type="submit" value="♥ <?php echo $post['like_number']?>" name="like_<?php echo $post_id?>">
                                </small>
                                <a href="tags.php?tag_id=<?php echo $tag['id']?>"><?php echo $post['taglist']?></a>,
                            </footer>
                        </form>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
