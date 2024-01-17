<?php

    function route($method, $url_list, $request_data) {
        global $Link, $Upload_dir;

        switch ($method) {

            case 'GET':

            break;

            case 'POST':
                switch ($url_list[1]) {
                    case "postupload":

                        $file = $_FILES['input'];
                        if ($file['type'] == "image/jpeg" || $file['type'] == "text/plain") {

                            $uploading_path = $Upload_dir . "/upload_" . time() . "_" . basename($file['name']);
                            move_uploaded_file($file['tmp_name'], $uploading_path);
                            $file_insert_result = $Link->query("INSERT INTO uploads(path) VALUES('$uploading_path')");

                            if (!$file_insert_result) {
                                setHTTPStatus("500", "Something went wrong (DB save failed)");
                                return;
                            }
                            else {
                                setHTTPStatus("200", null);
                                echo json_encode(["path" => $uploading_path]);
                            }

                        }
                        else {
                            setHTTPStatus("403", "wrong file type");
                        }

                    break;

                    case "jsonupload":
                        $base64 = $request_data->body->file;
                        if ($base64) {
                            $file = base64_decode($base64);
                            $uploading_path = "$Upload_dir" . "/frombase/" . "upload_" . time() . "_" . $request_data->body->file_name;
                            $file_uploader = fopen("$uploading_path", "wb");
                            fwrite($file_uploader, $file);
                            fclose($file_uploader);

                            $file_insert_result = $Link->query("INSERT INTO uploads(path) VALUES('$uploading_path')");

                            if (!$file_insert_result) {
                                setHTTPStatus("500", "Something went wrong (DB save failed)");
                                return;
                            }
                            else {
                                setHTTPStatus("200", null);
                                echo json_encode(["path" => $uploading_path]);
                            }
                        }
                        else {
                            setHTTPStatus("400", "Input data incorrect (no file was added)");
                        }
                    break;

                    default:
                        setHTTPStatus("404", "Following path doesn't exist (files/$url_list[1])");
                    break;
                }
            break;

            default:

            break;
        }
    }
