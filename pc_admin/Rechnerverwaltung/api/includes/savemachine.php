<?php
    if ($client_request->savemachine->mode == 'new') {
        $request = "INSERT INTO machines (room, machine, hardwareid, ip) VALUES ('".mysqli_real_escape_string($database, $client_request->savemachine->room)."', '".mysqli_real_escape_string($database, $client_request->savemachine->name)."', '".mysqli_real_escape_string($database, $client_request->savemachine->machine)."', '".mysqli_real_escape_string($database, $client_request->savemachine->ip)."')";
    } else {
        $request = "UPDATE machines SET ";
        $first = true;
        foreach ($client_request->savemachine as $element => $value) {
            if ($element != 'id' && $element != 'mode') {
                if ($first) {
                    $request .= $element." = '".mysqli_real_escape_string($database, $value)."' ";
                    $first = false;
                } else {
                    $request .= ", ".$element." = '".mysqli_real_escape_string($database, $value)."' ";
                }
            }
        }
        $request .= "WHERE id = ".mysqli_real_escape_string($database, $client_request->savemachine->id);
    }
    $query = mysqli_query($database, $request);
    if ($query == true) {
        $client_response['savemachine'] = 'SUCCESS';
    } else {
        $client_response['savemachine'] = 'ERR_UPDATE_FAILED';
    }
?>