<?php
/**
 * Model
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

/* Enable error reporting */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Check if the route exists
 * @param string $route_uri URI to be matched
 * @param string $request_type Request method
 * @return bool
 *
 */
function new_route($route_uri, $request_type){
    $route_uri_expl = array_filter(explode('/', $route_uri));
    $current_path_expl = array_filter(explode('/',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    if ($route_uri_expl == $current_path_expl && $_SERVER['REQUEST_METHOD'] == strtoupper($request_type)) {
        return True;
    } else {
        return False;
    }
}

/**
 * Creates a new navigation array item using URL and active status
 * @param string $url The URL of the navigation item
 * @param bool $active Set the navigation item to active or inactive
 * @return array
 */
function na($url, $active){
    return [$url, $active];
}

/**
 * Creates filename to the template
 * @param string $template Filename of the template without extension
 * @return string
 */
function use_template($template){
    return sprintf("views/%s.php", $template);
}

/**
 * Creates breadcrumbs HTML code using given array
 * @param array $breadcrumbs Array with as Key the page name and as Value the corresponding URL
 * @return string HTML code that represents the breadcrumbs
 */
function get_breadcrumbs($breadcrumbs) {
    $breadcrumbs_exp = '<nav aria-label="breadcrumb">';
    $breadcrumbs_exp .= '<ol class="breadcrumb">';
    foreach ($breadcrumbs as $name => $info) {
        if ($info[1]){
            $breadcrumbs_exp .= '<li class="breadcrumb-item active" aria-current="page">'.$name.'</li>';
        } else {
            $breadcrumbs_exp .= '<li class="breadcrumb-item"><a href="'.$info[0].'">'.$name.'</a></li>';
        }
    }
    $breadcrumbs_exp .= '</ol>';
    $breadcrumbs_exp .= '</nav>';
    return $breadcrumbs_exp;
}

/**
 * Creates navigation bar HTML code using given array
 * @param array $navigation Array with as Key the page name and as Value the corresponding URL
 * @return string HTML code that represents the navigation bar
 */
function get_navigation($navigation){
    $navigation_exp = '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
    $navigation_exp .= '<a class="navbar-brand">Series Overview</a>';
    $navigation_exp .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
    $navigation_exp .= '<span class="navbar-toggler-icon"></span>';
    $navigation_exp .= '</button>';
    $navigation_exp .= '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
    $navigation_exp .= '<ul class="navbar-nav mr-auto">';
    foreach ($navigation as $name => $info) {
        if ($info[1]){
            $navigation_exp .= '<li class="nav-item active">';
        } else {
            $navigation_exp .= '<li class="nav-item">';
        }
        $navigation_exp .= '<a class="nav-link" href="'.$info[0].'">'.$name.'</a>';

        $navigation_exp .= '</li>';
    }
    $navigation_exp .= '</ul>';
    $navigation_exp .= '</div>';
    $navigation_exp .= '</nav>';
    return $navigation_exp;
}

/**
 * Pretty Print Array
 * @param $input
 */
function p_print($input){
    echo '<pre>';
    print_r($input);
    echo '</pre>';
}

/**
 * Creates HTML alert code with information about the success or failure
 * @param array $feedback Associative array with keys type and message
 * @return string
 */
function get_error($feedback){
    return '
        <div class="alert alert-'.$feedback['type'].'" role="alert">
            '.$feedback['message'].'
        </div>';
}

function connect_db($host, $database, $username, $password){
    $charset = 'utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $error) {
        echo 'Connection to database failed';
    }
}

function count_series($pdo){
    $rows = $pdo->prepare('SELECT id FROM series');
    $rows->execute();
    return $rows->rowCount($rows);
}

function get_series($pdo){
    $series = $pdo->prepare('SELECT * FROM series');
    $series->execute();
    $series_info = $series->fetchAll();
    $series_list = Array();
    foreach($series_info as $info){
        $series_list[htmlspecialchars($info['id'])] = array_map('htmlspecialchars', Array(
            'name' => $info['name'],
            'creator' => $info['creator'],
            'seasons' => $info['seasons'],
            'abstract' => $info['abstract']
        ));
    }
    return $series_list;
}

function get_series_table($series_list){
    $table = '';
    $table .= '<table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Series</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>';
    foreach($series_list as $key=>$value){
        $name = $value['name'];
        $table .= '<tr>
                        <th scope="row">';
        $table .= $name;
        $table .= '</th>
                    <td><a href="/DDWT21/week1/series/?series_id=';
        $table .= $key;
        $table .= '" role="button" class="btn btn-primary">More info</a></td></tr>';
    }
    $table .= '</tbody>
               </table>';
    return $table;
}

function get_series_info($pdo, $id){
    $series = $pdo->prepare('SELECT * FROM series WHERE id = ?');
    $series->execute([$id]);
    $type = $series->fetch();
    return $type;
}

function add_series($pdo, $name, $creator, $seasons, $abstract){
    $series = $pdo->prepare('SELECT * FROM series WHERE name = ?');
    $series->execute([$name]);
    $series_exist = $series->fetch();
    if ($series_exist){
        return [
            'type' => 'danger',
            'message' => 'This series already exists'
        ];
    } else {
        if (
            empty($name) or
            empty($creator) or
            empty($seasons) or
            empty($abstract)
        ) {
            return [
                'type' => 'danger',
                'message' => 'Please fill in all the fields'
            ];
        } elseif (!is_numeric($seasons)){
            return [
                'type' => 'danger',
                'message' => 'Please fill in a number in the Seasons field'
            ];
        } else {
            $added_series = $pdo->prepare('INSERT INTO series (name, creator, seasons, abstract) VALUES (?, ?, ?, ?)');
            $added_series->execute([
                $name,
                $creator,
                $seasons,
                $abstract,
            ]);
            $updated_db = $added_series->rowCount();
            if ($updated_db == 1) {
                return [
                    'type' => 'success',
                    'message' => 'Series was successfully added!'
                ];
            } else {
                return [
                    'type' => 'warning',
                    'message' => 'Adding series not successful'
                ];
            }
        }
    }
}

function update_series($pdo, $oldname, $newname, $creator, $seasons, $abstract, $id){
    $series = $pdo->prepare('SELECT * FROM series WHERE name = ?');
    $series->execute([$newname]);
    $series_exist = $series->fetch();
    if ($series_exist && $oldname != $newname) {
        return [
            'type' => 'danger',
            'message' => 'This series already exists'
        ];
    } else {
        $updated_series = $pdo->prepare('UPDATE series SET name = ?, creator = ?, seasons = ?, abstract = ? WHERE id = ?');
        $updated_series->execute([
            $newname,
            $creator,
            $seasons,
            $abstract,
            $id
        ]);
        $updated_db = $updated_series->rowCount();
        if ($updated_db == 1 or $newname == $oldname) {
            return [
                'type' => 'success',
                'message' => 'Success!'
            ];
        } else {
            return [
                'type' => 'warning',
                'message' => 'Editing series not successful'
            ];
        }
    }
}

function remove_series($pdo, $id){
    $series = $pdo->prepare('DELETE FROM series  WHERE id = ?');
    $series->execute([$id]);
    $deleted_series = $series->rowCount();
    if($deleted_series == 1){
        return [
            'type' => 'success',
            'message' => 'Successfully deleted series'
        ];
    } else {
        return [
            'type' => 'warning',
            'message' => 'Something went wrong'
        ];
    }
}
