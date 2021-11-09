<?php

/**
 * dd
 * function to dump data & die
 * [FOR DEVELOPMENT PURPOSE ONLY]
 */
if (!function_exists("dd")) {
    function dd($variable)
    {
        echo "<pre>";
        print_r($variable);
        die;
    }
}

/**
 * response_api
 * function to convert data to be json response
 * 
 */
if (!function_exists("response_api")) {
    function response_api($variable)
    {
        $default_response = [
            "success" => true,
            "message" => "Action Success",
            "data" => null,
            "code" => 200
        ];

        $variable = array_merge($default_response, $variable);

        $path_log = getcwd() . "/runtime/access.log";
        $log_file = [];
        if (file_exists($path_log) == false) $log_file = fopen($path_log, "wr")  or die("Unable to create file!");
        else $log_file = fopen($path_log, "a")  or die("Unable to open file!");
        // dd($log_file);
        $date = date("Y-m-d H:i:s");
        $route_access = $_SERVER['REQUEST_URI'];
        $schema = $_SERVER['HTTP_HOST'];
        $full_path = $schema . $route_access;
        $log = "[{$variable['code']}] [$date] $full_path : " . json_encode($variable) . "\n";
        // dd($log);
        fwrite($log_file, $log);
        fclose($log_file);

        header('Content-Type: application/json; charset=utf-8');
        http_response_code($variable['code']);
        echo json_encode($variable);
        die;
    }
}

/**
 * url
 * helper to replace index.php
 */
if (!function_exists("url")) {
    function url($variable, $schema =  false)
    {
        $schema = $_SERVER['HTTP_HOST'];
        $suffix = str_replace("server.php", '', $_SERVER['SCRIPT_NAME']);
        if ($schema) $suffix = $schema . $suffix;
        return $suffix . $variable;
    }
}

if (!function_exists("pascal2camel")) {
    function pascal2camel($input)
    {
        $output = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
        return $output;
    }
}

if (!function_exists('dq')) {
    function dq($query, $params)
    {
        $keys = array();

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }
        }
        // dd($keys);

        $query = preg_replace($keys, $params, $query, 1, $count);
        dd($query);
    }
}
