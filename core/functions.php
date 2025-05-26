<?php
function withUserData($data = []) {
    if (isset($_SESSION['user'])) {
        $data['user'] = true;
        $data['profile_picture'] = '/tp/uploads/' . $_SESSION['user']['profile_picture'];
        $data['name'] = $_SESSION['user']['username'];
    }

    return $data;
}