<?php
/*ini_set( 'session.cookie_httponly' );
ini_set( 'session.use_strict_mode', '1' );
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );


    ini_set("display_errors", 1);
    ini_set("track_errors", 1);
    ini_set("html_errors", 1);
    error_reporting(E_ALL);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$now = time();

if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();
    header('Location: session_expired.php');
    exit;
}
else {

    include_once("dbtools/connecttodb.php");
    
    if ( !isset($_SESSION['name']) ) {
        // Could not get the data that should have been sent.
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $now_date = date('Y-m-d H:i:s');
    
    if ($stmt = $con->prepare('SELECT id, HTTP_X_FORWARDED_FOR, REMOTE_ADDR, expire_datetime from managesessions WHERE username = ? and expire_datetime > ?')) {
        $username = $_SESSION['name'];
        $stmt->bind_param( 'ss', $username, $now_date );
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();
        //$stmt->bind_result($res_id, $http_x, $rem, $expire_datetime);


        if ($stmt->num_rows == 0) {
            session_unset();
            session_destroy();
            $stmt->close();
            $con ->close();
            header('Location: session_expired.php');
            exit;
        }
        $stmt->close();
        $con->close();
    }


    // either new or old, it should live at most for another hour
    $_SESSION['discard_after'] = $now + 1000;

    // If the user is not logged in redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        die();
    }
}*/
?>

<?php
    $userID = $_GET['id'];
    $vote = intval($_GET['vote']);
    $gpsLat = floatval($_GET['GPSlatitude']);
    $gpsLong = floatval($_GET['GPSlongitude']);
    $now = date('Y-m-d H:i:s');
    $http_x = "";
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $http_x = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    $remote_addr = "";
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $remote_addr = $_SERVER['REMOTE_ADDR'];
    }

    include_once("dbtools/connecttodb.php");

    $sql="INSERT INTO pins(userID, vote, gpsLat, gpsLong, stamp, HTTP_X_FORWARDED_FOR, REMOTE_ADDR) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param('siddsss', $userID, $vote, $gpsLat, $gpsLong, $now, $http_x, $remote_addr);
        $stmt->execute();
        $stmt->close();
    }

    $con->close();


?>