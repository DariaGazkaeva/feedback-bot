<?php class_exists('app\core\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Feedback </title>
    
<link rel="stylesheet" type="text/css" href="../web/css/message.css">

</head>
<body>
   
    <?php foreach ($messages as $m) {
            $text = $m->getText();
            $date = $m->getDate();
            $id = $m->getId();
            echo "<div class='message'><div class='message_field'>$text</div><div class='message_field'>$date</div></div>";
            echo "<form action='' method='post'><textarea name='text'></textarea><input type='hidden' name='user-message-id' value='$id'><input type='submit' value='Send'></form>";
        } ?>

<a href="update">Update</a>


</body>
</html>







