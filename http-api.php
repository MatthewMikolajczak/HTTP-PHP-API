<?php
// api.php/{$action}/
$path = $_SERVER['PATH_INFO'];
$action = trim($path,"/");

// connect to the mysql database
$mysqli = new mysqli("", "", "", "", 3306);
if ($mysqli->connect_errno) {
    //Failed to connect to MySQL
    http_response_code(500);
}
else {
    $mysqli->set_charset('utf8');
    switch($action) {
        case 'get-user':
            $name = $_POST['name'];
            $res = $mysqli->query('SELECT * FROM `users` WHERE `name` = "' . "$name" . '"');
            if($res) {
                $row = $res->fetch_assoc();
                if($row) {
                    $rows = array();
                    $rows[] = $row;
                    echo(json_encode($rows));
                    http_response_code(200); // good result
                } else http_response_code(204); // empty result
            }
            else http_response_code(500); // query doesn't work
            break;
        case 'create-user':
            $name = $_POST['name'];
            $pass = $_POST['password'];
            $created = date("Y-m-d H:i:s");
            $sql = 'INSERT INTO users (name, password, created) VALUES ("' . $name . '", "' . $pass . '", "' . $created . '")';
            if ($mysqli->query($sql) === TRUE) {
                http_response_code(200); // success
            } else {
                http_response_code(500); // fail
            }
            break;
        case 'set-password':
            $user = $_POST['userID'];
            $oldpass = $_POST['oldpassword'];
            $pass = $_POST['password'];
            $sql = 'UPDATE users SET password="' . $pass . '" WHERE (ID=' . $user . ' AND password="' . $oldpass . '")';
            if ($mysqli->query($sql) === TRUE) {
                if($mysqli->affected_rows > 0) http_response_code(200); // success
                else http_response_code(401);
            } else {
                http_response_code(500); // fail
            }
            break;
        case 'set-last_login':
            $name = $_POST['name'];
            $lastlog = date("Y-m-d H:i:s");
            $sql = 'UPDATE `users` SET `last_login` = "' . $lastlog . '" WHERE `name` = "' . "$name" . '"';
            if ($mysqli->query($sql) === TRUE) {
                http_response_code(200); // success
            } else {
                http_response_code(500); // fail
            }
            break;
        case 'get-games':
            $user = $_POST['userID'];
            $res = $mysqli->query('SELECT * FROM `games` WHERE `userID` = "' . "$user" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                echo(json_encode($rows));
            }
            else http_response_code(500); // query doesn't work
            break;
        case 'get-stats':
            $game = $_POST['gameID'];
            $res = $mysqli->query('SELECT * FROM `games` WHERE `ID` = "' . "$game" . '"');
            if($res) {
                $row = $res->fetch_assoc();
                if($row) http_response_code(200); // good result
                else {
                    http_response_code(204); // empty result
                    break;
                }
            }
            else {
                http_response_code(500); // query doesn't work
                break;
            }

            $stats = array(); // array for all results

            $res = $mysqli->query('SELECT * FROM `players` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {

                    // array_push($stats, $rows);
                    $stats[players] = $rows;

                }
            }
            else http_response_code(500); // query doesn't work

            $res = $mysqli->query('SELECT * FROM `serve` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {

                    // array_push($stats, $rows);
                    $stats[serve] = $rows;

                }
            }
            else http_response_code(500); // query doesn't work

            $res = $mysqli->query('SELECT * FROM `reception` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {

                    // array_push($stats, $rows);
                    $stats[reception] = $rows;

                }
            }
            else http_response_code(500); // query doesn't work

            $res = $mysqli->query('SELECT * FROM `attack` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {

                    // array_push($stats, $rows);
                    $stats[attack] = $rows;

                }
            }
            else http_response_code(500); // query doesn't work

            $res = $mysqli->query('SELECT * FROM `block` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {

                    // array_push($stats, $rows);
                    $stats[block] = $rows;

                }
            }
            else http_response_code(500); // query doesn't work

            $res = $mysqli->query('SELECT * FROM `setting` WHERE `gameID` = "' . "$game" . '"');
            if($res) {
                $row = $res->fetch_assoc();
                if($row) {
                    $rows = array();
                    $rows[] = $row;

                    // array_push($stats, $rows);
                    $stats[setting] = $rows;

                    http_response_code(200); // good result
                } else http_response_code(204); // empty result
            }
            else http_response_code(500); // query doesn't work

            echo(json_encode($stats));

            break;
        case 'get-saved_players':
            $user = $_POST['userID'];
            $res = $mysqli->query('SELECT * FROM `saved_players` WHERE `userID` = "' . "$user" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $rows[] = $r;
                    http_response_code(200); // good result
                }
                if(!empty($rows)) {
                    echo(json_encode($rows));
                }
            }
            else {
                http_response_code(500); // query doesn't work
                break;
            }
            break;
        case 'set-saved_players':
            $user = $_POST['userID'];
            $json = $_POST['playersJSON'];
            $obj = json_decode($json);
            // deleting old saved players
            $sql = 'DELETE FROM `saved_players` WHERE `userID` = "' . "$user" . '"';

            if ($mysqli->query($sql) === TRUE) {
                http_response_code(200); // success
            } else {
                http_response_code(500); // fail
            }

            for($i = 0; $i < count($obj); $i++) {
                if($obj[$i]->{'id'} == '') $sql = 'INSERT INTO saved_players (userID, number, name) VALUES ("' . $user . '", NULL, "' . $obj[$i]->{'name'} . '")';
                else $sql = 'INSERT INTO saved_players (userID, number, name) VALUES ("' . $user . '", "' . $obj[$i]->{'id'} . '", "' . $obj[$i]->{'name'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }
            break;
        case 'create-stats':
            $user = $_POST['userID'];
            $gameName = $_POST['gameName'];
            $scoreAJSON = $_POST['scoreA'];//json
            $scoreA = json_decode($scoreAJSON);
            $scoreBJSON = $_POST['scoreB'];//json
            $scoreB = json_decode($scoreBJSON);
            $playersJSON = $_POST['players'];//json
            $players = json_decode($playersJSON);
            $serveJSON = $_POST['serve'];//json
            $serve = json_decode($serveJSON);
            $receptionJSON = $_POST['reception'];//json
            $reception = json_decode($receptionJSON);
            $attackJSON = $_POST['attack'];//json
            $attack = json_decode($attackJSON);
            $blockJSON = $_POST['block'];//json
            $block = json_decode($blockJSON);
            $settingJSON = $_POST['setting'];//json
            $setting = json_decode($settingJSON);
            $date = date("Y-m-d");

            $sql = 'INSERT INTO games (userID, name, date, set1a, set1b, set2a, set2b, set3a, set3b, set4a, set4b, set5a, set5b) VALUES ("' . $user . '", "' . $gameName . '", "' . $date . '", "' . $scoreA[0] . '", "' . $scoreB[0] . '", "' . $scoreA[1] . '", "' . $scoreB[1] . '", "' . $scoreA[2] . '", "' . $scoreB[2] . '", "' . $scoreA[3] . '", "' . $scoreB[3] . '", "' . $scoreA[4] . '", "' . $scoreB[4] . '")';
            if ($mysqli->query($sql) === TRUE) {
                http_response_code(200); // success
            } else {
                http_response_code(500); // fail
                break;
            }
            $gameID = null;
            $res = $mysqli->query('SELECT * FROM `games` WHERE `userID` = "' . "$user" . '" ORDER BY `ID` DESC');
            if($res) {
                $row = $res->fetch_assoc();
                if($row) {
                    $gameID = $row['ID'];
                    http_response_code(200); // good result
                } else http_response_code(500); // error
            }
            else http_response_code(500); // query doesn't work

            for($i = 0; $i < count($players); $i++) {
                $sql = 'INSERT INTO players (gameID, number, name) VALUES ("' . $gameID . '", "' . $players[$i]->{'number'} . '", "' . $players[$i]->{'name'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }

            // tu trzeba jakos pobrać id nadane dodanym zawodnikow: tablica jednowymiarowa playerID[]
            $res = $mysqli->query('SELECT * FROM `players` WHERE `gameID` = "' . "$gameID" . '"');
            if($res) {
                http_response_code(204); // empty result
                $rows = array();
                while($r = $res->fetch_assoc()) {
                    $playerID[] = $r['ID'];
                    http_response_code(200); // good result
                }
            }
            else {
                http_response_code(500); // query doesn't work
                break;
            }

            for($i = 0; $i < count($serve); $i++) {
                $sql = 'INSERT INTO serve (playerID, gameID, set10, set11, set12, set13, set20, set21, set22, set23, set30, set31, set32, set33, set40, set41, set42, set43, set50, set51, set52, set53) VALUES ("' . $playerID[$i] . '", "' . $gameID . '", "' . $serve[$i]->{'s1-0'} . '", "' . $serve[$i]->{'s1-1'} . '", "' . $serve[$i]->{'s1-2'} . '", "' . $serve[$i]->{'s1-3'} . '", "' . $serve[$i]->{'s2-0'} . '", "' . $serve[$i]->{'s2-1'} . '", "' . $serve[$i]->{'s2-2'} . '", "' . $serve[$i]->{'s2-3'} . '", "' . $serve[$i]->{'s3-0'} . '", "' . $serve[$i]->{'s3-1'} . '", "' . $serve[$i]->{'s3-2'} . '", "' . $serve[$i]->{'s3-3'} . '", "' . $serve[$i]->{'s4-0'} . '", "' . $serve[$i]->{'s4-1'} . '", "' . $serve[$i]->{'s4-2'} . '", "' . $serve[$i]->{'s4-3'} . '", "' . $serve[$i]->{'s5-0'} . '", "' . $serve[$i]->{'s5-1'} . '", "' . $serve[$i]->{'s5-2'} . '", "' . $serve[$i]->{'s5-3'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }

            for($i = 0; $i < count($reception); $i++) {
                $sql = 'INSERT INTO reception (playerID, gameID, set10, set11, set12, set13, set14, set20, set21, set22, set23, set24, set30, set31, set32, set33, set34, set40, set41, set42, set43, set44, set50, set51, set52, set53, set54) VALUES ("' . $playerID[$i] . '", "' . $gameID . '", "' . $reception[$i]->{'s1-0'} . '", "' . $reception[$i]->{'s1-1'} . '", "' . $reception[$i]->{'s1-2'} . '", "' . $reception[$i]->{'s1-3'} . '", "' . $reception[$i]->{'s1-4'} . '", "' . $reception[$i]->{'s2-0'} . '", "' . $reception[$i]->{'s2-1'} . '", "' . $reception[$i]->{'s2-2'} . '", "' . $reception[$i]->{'s2-3'} . '", "' . $reception[$i]->{'s2-4'} . '", "' . $reception[$i]->{'s3-0'} . '", "' . $reception[$i]->{'s3-1'} . '", "' . $reception[$i]->{'s3-2'} . '", "' . $reception[$i]->{'s3-3'} . '", "' . $reception[$i]->{'s3-4'} . '", "' . $reception[$i]->{'s4-0'} . '", "' . $reception[$i]->{'s4-1'} . '", "' . $reception[$i]->{'s4-2'} . '", "' . $reception[$i]->{'s4-3'} . '", "' . $reception[$i]->{'s4-4'} . '", "' . $reception[$i]->{'s5-0'} . '", "' . $reception[$i]->{'s5-1'} . '", "' . $reception[$i]->{'s5-2'} . '", "' . $reception[$i]->{'s5-3'} . '", "' . $reception[$i]->{'s5-4'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }

            for($i = 0; $i < count($attack); $i++) {
                $sql = 'INSERT INTO attack (playerID, gameID, set10, set11, set12, set13, set20, set21, set22, set23, set30, set31, set32, set33, set40, set41, set42, set43, set50, set51, set52, set53) VALUES ("' . $playerID[$i] . '", "' . $gameID . '", "' . $attack[$i]->{'s1-0'} . '", "' . $attack[$i]->{'s1-1'} . '", "' . $attack[$i]->{'s1-2'} . '", "' . $attack[$i]->{'s1-3'} . '", "' . $attack[$i]->{'s2-0'} . '", "' . $attack[$i]->{'s2-1'} . '", "' . $attack[$i]->{'s2-2'} . '", "' . $attack[$i]->{'s2-3'} . '", "' . $attack[$i]->{'s3-0'} . '", "' . $attack[$i]->{'s3-1'} . '", "' . $attack[$i]->{'s3-2'} . '", "' . $attack[$i]->{'s3-3'} . '", "' . $attack[$i]->{'s4-0'} . '", "' . $attack[$i]->{'s4-1'} . '", "' . $attack[$i]->{'s4-2'} . '", "' . $attack[$i]->{'s4-3'} . '", "' . $attack[$i]->{'s5-0'} . '", "' . $attack[$i]->{'s5-1'} . '", "' . $attack[$i]->{'s5-2'} . '", "' . $attack[$i]->{'s5-3'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }

            for($i = 0; $i < count($block); $i++) {
                $sql = 'INSERT INTO block (playerID, gameID, set10, set11, set20, set21, set30, set31, set40, set41, set50, set51) VALUES ("' . $playerID[$i] . '", "' . $gameID . '", "' . $block[$i]->{'s1-0'} . '", "' . $block[$i]->{'s1-1'} . '", "' . $block[$i]->{'s2-0'} . '", "' . $block[$i]->{'s2-1'} . '", "' . $block[$i]->{'s3-0'} . '", "' . $block[$i]->{'s3-1'} . '", "' . $block[$i]->{'s4-0'} . '", "' . $block[$i]->{'s4-1'} . '", "' . $block[$i]->{'s5-0'} . '", "' . $block[$i]->{'s5-1'} . '")';
                if ($mysqli->query($sql) === TRUE) {
                    http_response_code(200); // success
                } else {
                    http_response_code(500); // fail
                }
            }

            $sql = 'INSERT INTO setting (gameID, set11, set12, set13, set14, set15, set16, set21, set22, set23, set24, set25, set26, set31, set32, set33, set34, set35, set36, set41, set42, set43, set44, set45, set46, set51, set52, set53, set54, set55, set56) VALUES ("' . $gameID . '", "' . $setting[0]->{'s1-1'} . '","' . $setting[0]->{'s1-2'} . '","' . $setting[0]->{'s1-3'} . '","' . $setting[0]->{'s1-4'} . '","' . $setting[0]->{'s1-5'} . '","' . $setting[0]->{'s1-6'} . '","' . $setting[0]->{'s2-1'} . '","' . $setting[0]->{'s2-2'} . '","' . $setting[0]->{'s2-3'} . '","' . $setting[0]->{'s2-4'} . '","' . $setting[0]->{'s2-5'} . '","' . $setting[0]->{'s2-6'} . '","' . $setting[0]->{'s3-1'} . '","' . $setting[0]->{'s3-2'} . '","' . $setting[0]->{'s3-3'} . '","' . $setting[0]->{'s3-4'} . '","' . $setting[0]->{'s3-5'} . '","' . $setting[0]->{'s3-6'} . '","' . $setting[0]->{'s4-1'} . '","' . $setting[0]->{'s4-2'} . '","' . $setting[0]->{'s4-3'} . '","' . $setting[0]->{'s4-4'} . '","' . $setting[0]->{'s4-5'} . '","' . $setting[0]->{'s4-6'} . '","' . $setting[0]->{'s5-1'} . '","' . $setting[0]->{'s5-2'} . '","' . $setting[0]->{'s5-3'} . '","' . $setting[0]->{'s5-4'} . '","' . $setting[0]->{'s5-5'} . '","' . $setting[0]->{'s5-6'} . '")';

            if ($mysqli->query($sql) === TRUE) {
                http_response_code(200); // success
            } else {
                http_response_code(500); // fail
            }

            break;
        default:
            echo 'niepoprawna akcja';
            break;
        }
}
?>
