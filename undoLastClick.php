
<?php
    $userID = $_GET['username'];

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $http_x = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    $remote_addr = "";
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $remote_addr = $_SERVER['REMOTE_ADDR'];
    }

    include_once("dbtools/connecttodb.php");

    $sql = 'DELETE FROM pins
            WHERe (pins.userID, pins.stamp) in (
                SELECT userId, stamp FROM (
                    SELECT userId, stamp from pins where userID= ? order by stamp desc limit 1
                ) as c
            )';

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param('s', $userID);
        $stmt->execute();
        $stmt->close();
    }

    $con->close();


?>