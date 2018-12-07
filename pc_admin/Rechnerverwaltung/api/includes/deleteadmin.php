<?php
    $checkrequest = "SELECT password FROM userdata WHERE username = '".$_SESSION['user']."'";
    $checkquery = mysqli_query($database, $checkrequest);
    $checkresult = mysqli_fetch_assoc($checkquery);
    if ($checkresult['password'] === hash('sha512', $client_request->deleteadmin->adminpw)) {
        $lastrequest = "SELECT COUNT(*) AS anzahl FROM userdata";
        $lastquery = mysqli_query($database, $lastrequest);
        $lastresponse = mysqli_fetch_assoc($lastquery);
        if ($lastresponse['anzahl'] > 1) {
            $request = "DELETE FROM userdata WHERE id = ".mysqli_real_escape_string($database, $client_request->deleteadmin->id);
            if (mysqli_query($database, $request)) {
                $client_response['deleteadmin'] = 'SUCCESS';
            } else {
                $client_response['deleteadmin'] = 'ERR_DELETE_FAILED';
            }
        } else {
            $client_response['deleteadmin'] = 'ERR_IS_LAST';
        }
    } else {
        $client_response['deleteadmin'] = 'ERR_UNAUTHORIZED';
    }
?>
