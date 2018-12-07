<?php
    $checkrequest = "SELECT type FROM userdata WHERE username = '".$_SESSION['user']."'";
    $checkquery = mysqli_query($database, $checkrequest);
    $checkresult = mysqli_fetch_assoc($checkquery);
    if ($checkresult['type'] == 1) {
        $checkrequest = "SELECT COUNT(*) AS anzahl FROM userdata WHERE username = '".mysqli_real_escape_string($database, $client_request->changeadmin->username)."' AND id != ".mysqli_real_escape_string($database, $client_request->changeadmin->id);
        $checkquery = mysqli_query($database, $checkrequest);
        $checkresult = mysqli_fetch_assoc($checkquery);
        if ($checkresult['anzahl'] > 0) {
            $client_response['changeadmin'] = 'ERR_USER_EXISTS';
        } else {
            if ($client_request->changeadmin->pwd === $client_request->changeadmin->pwd2) {
                if ($client_request->changeadmin->pwd == '' && $client_request->changeadmin->pwd2 == '') {
                    $request = "UPDATE userdata SET type = ".mysqli_real_escape_string($database, $client_request->changeadmin->type).", username = '".mysqli_real_escape_string($database, $client_request->changeadmin->username)."' WHERE id = ".mysqli_real_escape_string($database, $client_request->changeadmin->id);
                } else {
                    $request = "UPDATE userdata SET type = ".mysqli_real_escape_string($database, $client_request->changeadmin->type).", username = '".mysqli_real_escape_string($database, $client_request->changeadmin->username)."', password = '".mysqli_real_escape_string($database, hash('sha512', $client_request->changeadmin->pwd))."' WHERE id = ".mysqli_real_escape_string($database, $client_request->changeadmin->id);
                }
                if (mysqli_query($database, $request)) {
                    $client_response['changeadmin'] = 'SUCCESS';
                } else {
                    $client_response['changeadmin'] = 'ERR_UPDATE_FAILED';
                }
            } else {
                $client_response['changeadmin'] = 'ERR_PASSWORDS_DIFFERENT';
            }
        }
    } else {
        $client_response['changeadmin'] = 'ERR_ACCESS_DENIED';
    }
?>
