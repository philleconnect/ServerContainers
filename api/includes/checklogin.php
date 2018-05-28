<?php
    $request = "SELECT password, type FROM userdata WHERE username = '".mysqli_real_escape_string($database, $client_request->login->uname)."'";
    $query = mysqli_query($database, $request);
    $result = mysqli_fetch_assoc($query);
    if ($result['password'] == hash('sha512', $client_request->login->passwd)) {
        session_start();
        $_SESSION['user'] = $client_request->login->uname;
        $_SESSION['type'] = $result['type'];
        $_SESSION['timeout'] = time();
        $client_response['login'] = 'SUCCESS';
        $client_response['type'] = $result['type'];
    } else {
        $client_response['login'] = 'ERR_WRONG_CREDENTIALS';
    }
?>