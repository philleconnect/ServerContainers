<?php
    if ($client_request->logging->mode == 'clear') {
        $request = "DELETE FROM log";
    } else {
        $request = "DELETE FROM log WHERE id = ".$client_request->logging->id;
    }
    if (mysqli_query($database, $request)) {
        $client_response['logging'] = 'SUCCESS';
    } else {
        $client_response['logging'] = 'ERR_UPDATE_FAILED';
    }
?>