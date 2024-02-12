<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php
        include './redirection.php';
        include './header.php';
        ?>
        <div id="wrapper">
            <?php
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            ?>

            <aside>
                <?php
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?> 
                        (n° <?php echo $userId ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.id,
                    posts.created,
                    posts.user_id,
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
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($post = $lesInformations -> fetch_assoc()){                
                ?> 

                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13' ><?php echo $post['created'] ?> </time>
                    </h3>
                    <address>par <a href="wall.php?user_id=<?php echo $post['user_id']?>"><?php echo $post['author_name'] ?> </address> </a>
                    <div>
                        <p><?php echo $post['content'] ?> </p>
                    </div>                                            
                    <?php
                            session_start();

                            $connected_id = intval($_SESSION['connected_id']);

                            $post_id = $post['id'];
                            
                            $addLike = 'INSERT INTO likes (user_id, post_id) '
                            . "VALUES ('$connected_id' , '$post_id')";
                            
                            if (isset($_POST['like'])){ 
                                $mysqli->query($addLike); 
                                
                                $getNumLike = "SELECT COUNT(id) as like_number FROM likes WHERE post_id = $post_id";
                                
                                $mysqli->query($getNumLike);
                            }
                            ?>
                        <form action="" method="post">
                            <footer>
                                <small>     
                                <input type="submit" value="♥ <?php echo $post['like_number']?>" name="like">
                                </small>
                                <a href="tags.php?tag_id=<?php echo $tag['id']?>"><?php echo $post['taglist']?></a>,
                            </footer>
                        </form>
                </article>
                <?php
                }
                ?>


            </main>
        </div>
    </body>
</html>
