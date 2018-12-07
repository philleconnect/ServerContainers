<?php
    include 'updatesamba.php';
    include 'directoryFunctions.php';
    $checkrequest = "SELECT groupfolders FROM configs";
    $checkquery = mysqli_query($database, $checkrequest);
    $isUsed = 0;
    while ($checkresponse = mysqli_fetch_assoc($checkquery)) {
        $gfentry = json_decode($checkresponse['groupfolders']);
        foreach ($gfentry as $gfolder) {
            if ($gfolder[1] == $client_request->deletegroupfolder->id) {
                $isUsed++;
            }
        }
    }
    if ($isUsed > 0) {
        $client_response['deletegroupfolder'] = 'ERR_IS_USED';
    } else {
        $pathrequest = "SELECT path FROM groupfolders WHERE id = ".mysqli_real_escape_string($database, $client_request->deletegroupfolder->id);
        $pathquery = mysqli_query($database, $pathrequest);
        $pathresult = mysqli_fetch_assoc($pathquery);
        $request = "DELETE FROM groupfolders WHERE id = ".mysqli_real_escape_string($database, $client_request->deletegroupfolder->id);
        if (mysqli_query($database, $request)) {
            if (updateSambaServer() == true) {
                if ($client_request->deletegroupfolder->deletefolder) {
                    if (deleteDirectory($pathresult['path'])) {
                        $client_response['deletegroupfolder'] = 'SUCCESS';
                    } else {
                        $client_response['deletegroupfolder'] = 'ERR_DELETE_FOLDER';
                    }
                } else {
                    $client_response['deletegroupfolder'] = 'SUCCESS2';
                }
            } else {
                $client_response['deletegroupfolder'] = 'ERR_UPDATE_SAMBA';
            }
        } else {
            $client_response['deletegroupfolder'] = 'ERR_DATABASE_ERROR';
        }
    }
?>
