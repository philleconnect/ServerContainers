<?php
    function addLogEntry($action, $user, $machine, $target) {
        global $database;
        $getMachine = "SELECT machine FROM machines WHERE hardwareid = '".mysqli_real_escape_string($database, $machine)."'";
        $getQuery = mysqli_query($database, $getMachine);
        $getResult = mysqli_fetch_assoc($getQuery);
        $request = "INSERT INTO log (action, user, machine, timestamp, target) VALUES (".$action.", '".mysqli_real_escape_string($database, $user)."', '".$getResult['machine']."', ".time().", '".mysqli_real_escape_string($database, $target)."')";
        if (mysqli_query($database, $request)) {
            return true;
        } else {
            return false;
        }
    }
?>