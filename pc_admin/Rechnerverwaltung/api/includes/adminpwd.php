<?php
    if ($client_request->adminpwd->pwd === $client_request->adminpwd->pwd2) {
        if ($client_request->adminpwd->pwd === '') {
            $client_response['adminpwd'] = 'ERR_PASSWORD_EMPTY';
        } else {
            $checkrequest = "SELECT password FROM userdata WHERE username = '".$_SESSION['user']."'";
            $checkquery = mysqli_query($database, $checkrequest);
            $checkresult = mysqli_fetch_assoc($checkquery);
            if ($checkresult['password'] === hash('sha512', $client_request->adminpwd->old)) {
                $request = "UPDATE userdata SET password = '".mysqli_real_escape_string($database, hash('sha512', $client_request->adminpwd->pwd))."' WHERE username = '".$_SESSION['user']."'";
                $query = mysqli_query($database, $request);
                if ($query) {
                    $client_response['adminpwd'] = 'SUCCESS';
                } else {
                    $client_response['adminpwd'] = 'ERR_UPDATE_FAILED';
                }
            } else {
                $client_response['adminpwd'] = 'ERR_OLD_INCORRECT';
            }
        }
    } else {
        $client_response['adminpwd'] = 'ERR_PASSWORDS_DIFFERENT';
    }
?>
