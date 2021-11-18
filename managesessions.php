
<head>
  <style>
    table, td, th {  
      border: 1px solid #ddd;
    }

    table {
      border-collapse: collapse;
      width: 80%;
      margin-left: 10%;
    }

    th, td {
      padding: 15px;
    }
  </style>
</head>

<table style="text-align: center;">

  <tr>
    <th>username</th>
    <th>REMOTE_ADDR</th>
    <th>access_datetime</th>
    <th>expire_datetime</th>
  </tr>

  <?php
    ini_set( 'session.cookie_httponly' );
    ini_set( 'session.use_strict_mode', '1' );
    ini_set( 'session.use_only_cookies', '1' );
    ini_set( 'session.use_trans_sid', '0' );

      ini_set("display_errors", 1);
      ini_set("track_errors", 1);
      ini_set("html_errors", 1);
      error_reporting(E_ALL);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    session_start();    
    include_once("dbtools/connecttodb.php");

    $now = date('Y-m-d H:i:s');
    
    if ($stmt = $con->prepare('SELECT username, REMOTE_ADDR, access_datetime, expire_datetime FROM managesessions WHERE expire_datetime > ?')) {
      // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
      $stmt->bind_param('s', $now);
      $stmt->execute();
      // Store the result so we can check if the account exists in the database.
      $stmt->store_result();
      $stmt->bind_result($username, $REMOTE_ADDR, $access_datetime, $expire_datetime);


      while($stmt->fetch()){
        echo "<tr>";
        echo "<td>".$username."</td><td>".$REMOTE_ADDR."</td><td>".$access_datetime."</td><td>".$expire_datetime."</td>";
        echo "</tr>";
      }


      $stmt->close();
    }

    $con->close();

  ?>


</table>

