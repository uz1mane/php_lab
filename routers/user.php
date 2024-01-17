<?php
    include_once 'user/user_helper.php';

    function route($method, $url_list, $request_data) {
        global $Link;

        switch ($method) {

            case "GET":

                $token = ltrim(getallheaders()['Authorization'], "Bearer ");
                $userFromToken = $Link->query("SELECT userID FROM tokens WHERE value='$token'")->fetch_assoc();
                if (!is_null($userFromToken)) {
                    $userID = $userFromToken['userID'];
                    $user= $Link->query("SELECT * FROM users WHERE id='$userID'")->fetch_assoc();
                    echo json_encode($user);
                }
                else {
                    echo "400: input data incorrect";
                }

            break;

            case "POST":
                $is_validated = true;
                $validation_errors = [];

                if (!validate_password($request_data->body->password)) {
                    $is_validated = false;
                    $validation_errors[] = ["Password", "Password is less than 8 characters"];
                }
                $password = hash("sha1", $request_data->body->password);

                $name = $request_data->body->name;

                $login = $request_data->body->login;
                if (!validate_string_not_less($login, 4)) {
                    $is_validated = false;
                    $validation_errors[] = ["Login", "Login is less than 3 characters"];
                }

                if (!$is_validated) {
                    $validation_message = "";
                    foreach($validation_errors as $err) {
                        $validation_message .= "$err[0] : $err[1] \r\n";
                    }
                    setHTTPStatus("403", $validation_message);
                    return;
                }

                $user_insert_result = $Link->query("INSERT INTO users(name, login, password) VALUES('$name', '$login', '$password')");

                if (!$user_insert_result) {
                    if ($Link->errno == 1062) {
                        setHTTPStatus("409", "Login '$login' is already taken");
                        return;
                    }
                    setHTTPStatus("400", "DB: Bad insert request (create user): " . $Link->error);
                }
                else {
                    setHTTPStatus("200", "User '$login' was successfully created");
                }

            break;

            default:

            break;

        }
    }