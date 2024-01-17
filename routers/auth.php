<?php

    function route($method, $url_list, $request_data) {
        if ($method == "POST") {
            global $Link;

            switch ($url_list[1]) {

                case "login":

                    $login = $request_data->body->login;
                    $password = hash("sha1",$request_data->body->password);
                    $user = $Link->query("SELECT id FROM users WHERE login='$login' AND password='$password'")->fetch_assoc();

                    if (!is_null($user)) {
                        $token = bin2hex(random_bytes(16));
                        $userID = $user['id'];
                        $token_insert_result = $Link->query("INSERT INTO tokens(value, userID) VALUES('$token', '$userID')");

                        if (!$token_insert_result) {
                            setHTTPStatus("400", "DB: Bad insert request (token): " . $Link->error);
                        }
                        else {
                            echo json_encode(['token' => $token]);
                        }
                    }
                    else {
                        setHTTPStatus("400", "Input data incorrect");
                    }

                break;

                case "logout":

                break;

                default:
                    setHTTPStatus("404", "Following path doesn't exist (auth/$url_list[1])");
                break;

            }
        }
        else {
            setHTTPStatus("400", "You can only use POST to 'auth/'");
        }

    }