<?php
    $request = "DELETE FROM machines WHERE hardwareid = '".mysqli_real_escape_string($database, $client_request->deletemachine->hardwareid)."'";
    $query = mysqli_query($database, $request);
    if ($query == true) {
        $client_response['deletemachine'] = 'SUCCESS';
    } else {
        $client_response['deletemachine'] = 'ERR_UPDATE_FAILED';
    }
?>
