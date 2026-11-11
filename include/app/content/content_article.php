<div class="window">
    <div style="width: 90%; margin-left: 5%;">
        <?php
            require_once('modules/composer/vendor/autoload.php');
            $purifier_config = HTMLPurifier_Config::createDefault();
	        $purifier = new HTMLPurifier($purifier_config);

            $db_query = $pdo->prepare('SELECT * FROM ARTICLES WHERE contentset_pos=:cid');
            $db_query->execute(['cid' => filter_var($_GET['id'], FILTER_VALIDATE_INT)]);
            $article = $db_query->fetch();
            echo($purifier->purify($article['content']));
        ?>
    </div>
</div>