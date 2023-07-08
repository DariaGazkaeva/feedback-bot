<?php class_exists('app\core\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Feedback </title>
    
<link rel="stylesheet" type="text/css" href="../web/css/reset.css">
<link rel="stylesheet" type="text/css" href="../web/css/main.css">
<link rel="stylesheet" type="text/css" href="../web/css/message.css">
<link rel="stylesheet" type="text/css" href="../web/css/modal.css">
<script src="../web/js/main.js"></script>

</head>
<body>
   
<div class="header">
    <a href="" class="update-button">Update</a>
</div>
<div class="feedback-container">
    <?php foreach ($messages as $m) {
            $text = $m->getText();
            $media = $m->getMedia();
            $date = $m->getDate();
            $id = $m->getId();

            echo "<div class='message' id='$id'>";
            if ($text !== '' and $text !== null) {
                echo "
                    <div class='message_field'>
                        $text
                    </div>";
            }
            if ($media !== null and $media !== '') {
                $filePath = '../web/media/'.$media;

                if (str_ends_with($filePath, '.jpg') or str_ends_with($filePath, '.jpeg') or str_ends_with($filePath, '.png') or str_ends_with($filePath, '.gif')) {
                    echo "
                    <div class='message_field'>
                        <img src='$filePath'  alt='media'/>
                    </div>";
                } else if (str_ends_with($filePath, '.mp4')) {
                    echo "<div class='message_field'>
                        <video autoplay muted loop src='$filePath'>
                            Your browser does not support the video.
                        </video>
                    </div>";
                }
            }
            echo "
                    <div class='message_field'>
                        $date
                    </div>
                    <div class='message_field'>
                        <button class='reply-button'>Reply</button>
                        <button class='see-button'>See answers</button>
                    </div>
                </div>
            ";
        } ?>
    <div class="modal reply_modal">
        <div class="content_modal">
            <button class="reply-close">Close</button>
            <form action='' method='post' class="reply-form">
                <label for="textarea">Your answer:</label>
                <textarea name='text' id="textarea" rows="5" cols="20" required></textarea>
                <br>
                <input type='submit' value='Send'>
            </form>
        </div>
    </div>

    <div class="modal see_modal">
        <div class="content_modal">
            <button class="see-close">Close</button>
        </div>
    </div>
</div>


</body>
</html>






