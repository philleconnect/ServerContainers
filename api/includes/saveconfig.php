<?php
    if ($client_request->saveconfig->mode == 'new') {
        $checkRequest = "SELECT COUNT(*) AS anzahl FROM configs WHERE name = '".mysqli_real_escape_string($database, $client_request->saveconfig->name)."'";
        $checkQuery = mysqli_query($database, $checkRequest);
        $checkResponse = mysqli_fetch_assoc($checkQuery);
        if ($checkResponse['anzahl'] > 0) {
            $client_response['saveconfig'] = 'ERR_NAME_EXISTS';
            $stop = true;
        } else {
            $request = "INSERT INTO configs (name, os, smbserver, driveone, drivetwo, drivethree, pathone, pathtwo, paththree, shutdown, dologin, loginpending, loginfailed, wrongcredentials, networkfailed, success, groupfolders, infotext, servicemode) VALUES ('".mysqli_real_escape_string($database, $client_request->saveconfig->name)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->os)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->smbserver)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->driveone)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->drivetwo)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->drivethree)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->pathone)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->pathtwo)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->paththree)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->shutdown)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->dologin)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->loginpending)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->loginfailed)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->wrongcredentials)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->networkfailed)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->success)."', '".mysqli_real_escape_string($database, json_encode($client_request->saveconfig->groupfolders))."', '".mysqli_real_escape_string($database, $client_request->saveconfig->infotext)."', '".mysqli_real_escape_string($database, $client_request->saveconfig->servicemode)."')";
        }
    } else if ($client_request->saveconfig->mode == 'delete') {
        if ($client_request->saveconfig->os == 'win') {
            $checkInUseRequest = "SELECT COUNT(*) AS anzahl FROM machines WHERE config_win = '".mysqli_real_escape_string($database, $client_request->saveconfig->name)."'";
        } else {
            $checkInUseRequest = "SELECT COUNT(*) AS anzahl FROM machines WHERE config_linux = '".mysqli_real_escape_string($database, $client_request->saveconfig->name)."'";
        }
        $checkInUseQuery = mysqli_query($database, $checkInUseRequest);
        $checkInUseResult = mysqli_fetch_assoc($checkInUseQuery);
        if ($checkInUseResult['anzahl'] > 0) {
            $client_response['saveconfig'] = 'ERR_IS_USED';
            $stop = true;
        } else {
            $request = "DELETE FROM configs WHERE id = '".mysqli_real_escape_string($database, $client_request->saveconfig->id)."'";
        }
    } else {
        $request = "UPDATE configs SET ";
        $first = true;
        foreach ($client_request->saveconfig as $element => $value) {
            if ($element != 'id' && $element != 'mode') {
                if ($first) {
                    if ($element == 'groupfolders') {
                        $request .= $element." = '".mysqli_real_escape_string($database, json_encode($value))."' ";
                    } else {
                        $request .= $element." = '".mysqli_real_escape_string($database, $value)."' ";
                    }
                    $first = false;
                } else {
                    if ($element == 'groupfolders') {
                        $request .= ", ".$element." = '".mysqli_real_escape_string($database, json_encode($value))."' ";
                    } else {
                        $request .= ", ".$element." = '".mysqli_real_escape_string($database, $value)."' ";
                    }
                }
            }
        }
        $request .= "WHERE id = ".mysqli_real_escape_string($database, $client_request->saveconfig->id);
    }
    if (!$stop) {
        $query = mysqli_query($database, $request);
        if ($query == true) {
            $client_response['saveconfig'] = 'SUCCESS';
        } else {
            $client_response['saveconfig'] = 'ERR_UPDATE_FAILED';
        }
    }
?>
