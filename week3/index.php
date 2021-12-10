<?php
/**
 * Controller
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

/*
list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
    explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
*/

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost:8889', 'ddwt21_week3', 'ddwt21', 'ddwt21');

/* Create Router instance */
$router = new \Bramus\Router\Router();

$router->mount('/api', function() use ($router){
    http_content_type('application/json');
    $db = connect_db('localhost:8889', 'ddwt21_week3', 'ddwt21', 'ddwt21');
    $cred = set_cred('ddwt21', 'ddwt21');
    /* Validate user */
    $router->before('GET|POST|PUT|DELETE', '/.*', function() use ($cred){
        if (!check_cred($cred)){
            echo 'Authentication failed';
            http_response_code(401);
            exit();
        }
    });
    /* Read all series */
    $router->get('/series', function() use ($db){
        $series = json_encode(get_series($db));
        echo $series;
    });
    /* Read one series */
    $router->get('/series/(\d+)', function($id) use ($db){
        $series_info = json_encode(get_series_info($db, $id));
        echo $series_info;
    });
    /* Delete a series */
    $router->delete('/series/(\d+)', function($id) use ($db){
        $delete = json_encode(remove_series($db, $id));
        echo $delete;
    });
    /* Add a series */
    $router->post('/series', function() use ($db){
        $add = json_encode(add_series($db, $_POST));
        echo $add;
    });
    /* Update a series */
    $router->put('/series/(\d+)', function($id) use ($db){
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $series_info = $_PUT + ['series_id' => $id];
        $update = json_encode(update_series($db, $series_info));
        echo $update;
    });
});

/* 404 route */
$router->set404(function(){
    echo "404 Not Found";
});

/* Run the router */
$router->run();