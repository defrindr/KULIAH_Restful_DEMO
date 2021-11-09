<?php
// disable error reporting
error_reporting(0);
// load config
require_once 'config/function_helper.php';
require_once 'config/db.php';
require 'config/QueryBuilder.php';
require 'config/Controller.php';

$prefix_action = "action";
$suffix_controller = "Controller.php";
$suffix_module = "Controller";
$list_modules = []; // dynamic load from controller in controllers directory
$list_actions = []; // dynamic load from selected controller in controllers directory

try {
    $controller_path = getcwd() . "/controllers";
    if (file_exists($controller_path) == false) response_api(["success" => false, "message" => "Internal Server Error: Path Controller not found", "code" => 500]);

    $list_file = scandir($controller_path);
    unset($list_file[0]); // current dir
    unset($list_file[1]); // upper dir

    foreach ($list_file as $item) {
        // dynamic load module
        require_once("controllers/" . $item);
        // add access route & module name to array
        $is_with_suffix = strpos($item, $suffix_controller);
        if ($is_with_suffix == true) {
            $module_name = str_replace($suffix_controller, "", $item);
            $route_access = pascal2camel($module_name);
            $list_modules[$route_access] = $module_name . $suffix_module;
        }
    }

    $module = null;
    if (isset($_GET['module'])) {
        $module = $list_modules[$_GET['module']];
    }

    /**
     * Handle module input
     * cek is module exist or not
     */
    if ($module == null) response_api(['success' => false, 'message' => "Module '{$_GET['module']}'  Not Found", "code" => 404]);
    $class = new $module($db);

    // get all public action from module
    $all_action_in_module = get_class_methods($module);
    //  add action route & route name to array
    foreach ($all_action_in_module as $action_name) {
        $is_with_action_prefix = substr($action_name, 0, 6) === $prefix_action;
        if ($is_with_action_prefix) {
            $action_route = pascal2camel(substr($action_name, 6));
            if (in_array($action_route, $class->unset_actions) == false)
                $list_actions[$action_route] = $action_name;
        }
    }

    $action = $list_actions[$_GET['action']];
    if ($action == null) response_api(['success' => false, 'message' => 'action not found', 'code' => 404]);

    $list_allowed_method = $class->getVerbs()[$_GET['action']];
    if ($list_allowed_method != null) {
        $access_method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        if (in_array($access_method, $list_allowed_method) == false)
            response_api(["success" => false, "message" => "Method Not Allowed: " . implode(', ', $list_allowed_method), 'code' => 405]);
    }

    unset($_GET['module']);
    unset($_GET['action']);
    $post_data = [];
    parse_str(file_get_contents("php://input"),$post_data);
    $class->$action($post_data);
} catch (\Throwable $th) {
    response_api(["success" => false, "message" => "Internal Server Error: " . $th->getMessage(), "code" => 500]);
}
