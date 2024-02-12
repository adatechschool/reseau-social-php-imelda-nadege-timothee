<?php session_start();
    $userId = $_SESSION['connected_id'];
    

?>

<html>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/> 
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo $userId?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $userId?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>

                <?php if (isset($userId)){ ?>
                    <ul>
                        <li><a href="settings.php?user_id=<?php echo $userId?>">Paramètres</a></li>
                        <li><a href="followers.php?user_id=<?php echo $userId?>">Mes suiveurs</a></li>
                        <li><a href="subscriptions.php?user_id=<?php echo $userId?>">Mes abonnements</a></li>
                        <li><a href="login.php">Connectez vous</a></li>
                    </ul>
                <?php } else { ?>
                    <ul>
                        <li><a href="login.php">Connectez vous</a></li>
                    </ul>

                    <?php } ?>


            </nav>
        </header>
</html>