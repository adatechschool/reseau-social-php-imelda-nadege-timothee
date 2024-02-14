<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php
    session_start();
        include './header.php';
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $current_url = "$scheme://$host$uri";
        ?>
        <div id="wrapper">
            <?php
            $tagId = intval($_GET['tag_id']);
            ?>
            <?php
            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            ?>

            <aside>
                <?php

                $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <strong> <?php echo $tag['label']?> </strong>
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
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($post = $lesInformations->fetch_assoc())
                {

                    ?>                
                    <article>
                        <h3>
                            <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                        </h3>
                        <address>par <a href="wall.php?user_id=<?php echo $post['user_id']?>"><?php echo $post['author_name']?></address></a>
                        <div>
                            <p><?php echo $post['content']?></p>   
                            <?php

                            $connected_id = intval($_SESSION['connected_id']);
                            $post_id = $post['id'];
                            $addLike = 'INSERT INTO likes (user_id, post_id) '
                            . "VALUES ('$connected_id' , '$post_id')";
                            
                            if (isset($_POST["like_$post_id"])){ 
                                $mysqli->query($addLike); 
                                
                                $getNumLike = "SELECT COUNT(id) as like_number FROM likes WHERE post_id = $post_id";
                                $like_num = $mysqli->query($getNumLike);
                                if ($like_num){
                                    $newLikeCount = $like_num->fetch_assoc()['like_number'];
                                    $post['like_number'] = $newLikeCount;
                                }
                            }
                            ?>
                        <form action="<?php echo $current_url ?>" method="post">
                            <footer>
                                <small>     
                                <input class="like" type="submit" value="♥ <?php echo $post['like_number']?>" name="like_<?php echo $post_id?>">
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