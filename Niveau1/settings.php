<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Paramètres</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php
        include './redirection.php';
        include './header.php';
        ?>
        <div id="wrapper" class='profile'>


            <aside>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez des informations vous concernant.</p>

                    <form action="logout.php" method="post">
                        <input type="submit" value="Logout">
                    </form>
                </section>
            </aside>
            <main>
                <?php
                $userId = intval($_GET['user_id']);


                $mysqli = new mysqli("localhost", "root", "", "socialnetwork");


                $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$userId' 
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                $user = $lesInformations->fetch_assoc();


                ?>                
                <article class='parameters'>
                    <h3>Mes paramètres</h3>
                    <dl>
                        <dt>Pseudo</dt>
                        <a href="wall.php?user_id=<?php echo $userId ?>"><dd><?php echo $user['alias']?></dd></a>
                        <dt>Email</dt>
                        <dd><?php echo $user['email']?></dd>
                        <dt>Nombre de message</dt>
                        <dd><?php echo $user['totalpost']?></dd>
                        <dt>Nombre de "J'aime" donnés </dt>
                        <dd><?php echo $user['totalgiven']?></dd>
                        <dt>Nombre de "J'aime" reçus</dt>
                        <dd><?php echo $user['totalgiven']?></dd>
                    </dl>

                </article>
            </main>
        </div>
    </body>
</html>
