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

