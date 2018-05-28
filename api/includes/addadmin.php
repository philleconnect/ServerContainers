<?php
    if ($_SESSION['type'] == '1') {
        if ($client_request->addadmin->pwd == $client_request->addadmin->pwd2) {
            if ($client_request->addadmin->pwd == '') {
				$client_response['addadmin'] = 'ERR_PASSWORD_EMPTY';
            } else {
                $testrequest = "SELECT COUNT(*) AS anzahl FROM userdata WHERE username = '".mysqli_real_escape_string($database, $client_request->addadmin->username)."'";
                $testquery = mysqli_query($database, $testrequest);
                $testresult = mysqli_fetch_assoc($testquery);
                if ($testresult['anzahl'] > 0) {
                    $client_response['addadmin'] = 'ERR_USER_EXISTS';
                } else {
                    $request = "INSERT INTO userdata (username, password, type) VALUES ('".mysqli_real_escape_string($database, $client_request->addadmin->username)."', '".mysqli_real_escape_string($database, hash('sha512', $client_request->addadmin->pwd))."', '".mysqli_real_escape_string($database, $client_request->addadmin->type)."')";
                    $query = mysqli_query($database, $request);
                    if ($query) {
                        $client_response['addadmin'] = 'SUCCESS';
                    } else {
                        $client_response['addadmin'] = 'ERR_UPDATE_FAILED';
                    }
                }
            }
        } else {
            $client_response['addadmin'] = 'ERR_PASSWORDS_DIFFERENT';
        }
    } else {
        $client_response['addadmin'] = 'ERR_ACCESS_DENIED';
    }
?>
