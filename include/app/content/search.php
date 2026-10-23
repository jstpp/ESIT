<?php
    $key = $_GET['key'] ?? null;

    $results = array(
        'content' => array(),
        'contentsets' => array(),
        'channels' => array()
    );

    if(!$key || !has_permission('main.channels.search')) {
        echo(json_encode($results));
        die;
    }

    $db_query = $pdo->prepare('SELECT * FROM CONTENT WHERE title LIKE :title AND publish_time<:currenttime');
    $db_query->execute(['title' => '%'.$key.'%', 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);
    while($row = $db_query->fetch())
    {
        array_push($results['content'], $row);
    }

    $db_query = $pdo->prepare('SELECT * FROM PROBLEMSETS WHERE title LIKE :title AND publish_time<:currenttime');
    $db_query->execute(['title' => '%'.$key.'%', 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);
    while($row = $db_query->fetch())
    {
        array_push($results['contentsets'], $row);
    }

    $db_query = $pdo->prepare('SELECT * FROM CHANNELS WHERE title LIKE :title AND publish_time<:currenttime');
    $db_query->execute(['title' => '%'.$key.'%', 'currenttime' => date("Y-m-d H:i:s", strtotime("now"))]);
    while($row = $db_query->fetch())
    {
        array_push($results['channels'], $row);
    }

    echo(json_encode($results));
?>