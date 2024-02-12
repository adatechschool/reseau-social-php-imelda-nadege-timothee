<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php
        include './header.php';
        ?>
        <div id="wrapper">
            <aside>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>    
            <main>
                <?php
                $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
                if ($mysqli->connect_errno)
                {
                    echo "<article>";
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    echo "</article>";
                    exit();
                
                }
                

                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.id,
                    posts.created,
                    users.alias as author_name,
                    posts.user_id,  
                    count(likes.id) as like_number,  
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
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }

                while ($post = $lesInformations->fetch_assoc())
                {
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address>Par <a href="wall.php?user_id=<?php echo $post['user_id']?>"><?php echo $post['author_name'] ?> </a> </address>
                        <div>
                            <p><?php echo $post['content']?></p>
                        </div>

                        <?php
                            session_start();

                            $connected_id = intval($_SESSION['connected_id']);

                            $post_id = $post['id'];
                            
                            $addLike = 'INSERT INTO likes (user_id, post_id) '
                            . "VALUES ('$connected_id' , '$post_id')";
                            
                            if (isset($_POST["like_$post_id"])){ 
                                $mysqli->query($addLike); 
                                
                                $getNumLike = "SELECT COUNT(id) as like_number FROM likes WHERE post_id = $post_id";
                                
                                $mysqli->query($getNumLike);
                            }
                            ?>
                        <form action="" method="post">
                            <footer>
                                <small>     
                                <input type="submit" value="♥ <?php echo $post['like_number']?>" name="like_<?php echo $post_id?>">
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