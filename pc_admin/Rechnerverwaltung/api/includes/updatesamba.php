<?php
    function updateSambaServer() {
        global $database;
        $request = "SELECT * FROM groupfolders";
        $query = mysqli_query($database, $request);
        $data = array();
        while ($response = mysqli_fetch_assoc($query)) {
            if ($response['students'] == 1) {
                $students = true;
            } else {
                $students = false;
            }
            if ($response['teachers'] == 1) {
                $teachers = true;
            } else {
                $teachers = false;
            }
            if ($response['writeable'] == 1) {
                $writeable = true;
            } else {
                $writeable = false;
            }
            $object = (object) array(
                'name' => $response['name'],
                'path' => $response['path'],
                'writeable' => $writeable,
                'students' => $students,
                'teachers' => $teachers,
            );
            array_push($data, $object);
        }
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents('http://samba:8000', false, $context);
        if ($result === false) {
            return false;
        } elseif (strpos($result, 'Thanks, it worked') !== false) {
            return true;
        } else {
            return $result;
        }
    }
?>
