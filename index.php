<?php
    include_once 'helpers/headers.php';
    include_once 'helpers/validation.php';

    global $Link, $Upload_dir;

    function get_data($method) {
        $data = new stdClass();
        if ($method != "GET") {
            $data->body = json_decode(file_get_contents('php://input'));
        }
        $data->parameters = [];
        $data_get = $_GET;
        foreach ($data_get as $key => $value) {
            if ($key != "q") {
                $data->parameters[$key] = $value;
            }
        }
        return $data;
    }

    function get_method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    header('Content-type: application/json');
    $Link = mysqli_connect("127.0.0.1", "backend_demo_1", "123", "backend_demo_1");
    $Upload_dir = "uploads";

    if (!$Link) {
        setHTTPStatus("500", "DB connection error: " . mysqli_connect_error());
        exit;
    }

    $url = isset($_GET['q']) ? $_GET['q'] : '';
    $url = rtrim($url, '/');
    $url_list = explode('/', $url);

    $router = $url_list[0];
    $method = get_method();
    $request_data = get_data($method);
    $path = realpath(dirname(__FILE__)).'/routers/' . $router . '.php';

    if (file_exists($path)) {
        include_once 'routers/' . $router . '.php';
        route($method, $url_list, $request_data);
    }
    else {
        setHTTPStatus("404", "Following path doesn't exist ($path)");
    }

    mysqli_close($Link);
    return;
