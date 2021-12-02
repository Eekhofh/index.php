<?php
/**
 * Controller
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost:8889', 'ddwt21_week2', 'ddwt21','ddwt21');
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

$navigation_array = Array(
    'Home' => '/DDWT21/week2/',
    'Overview' => '/DDWT21/week2/overview/',
    'My Account' => '/DDWT21/week2/myaccount/',
    'Register' => '/DDWT21/week2/register/',
    'Add Series' => '/DDWT21/week2/add/'
);

/* Landing page */
if (new_route('/DDWT21/week2/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Home' => na('/DDWT21/week2/', True)
    ]);
    $navigation = get_navigation($navigation_array, 'Home');

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT21/week2/overview/', 'get')) {
    /* Get Number of Series */
    $nbr_series = count_series($db);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', True)
    ]);
    $navigation = get_navigation($navigation_array, 'Overview');

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_series_table(get_series($db), $db);

    if(isset($_GET['error_msg'])){
        $error_msg = get_error(json_decode($_GET['error_msg'], True));
    }

    /* Choose Template */
    include use_template('main');
}

/* Single Series */
elseif (new_route('/DDWT21/week2/series/', 'get')) {
    /* Get Number of Series */
    $nbr_series = count_series($db);

    /* Get series from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);
    $added_by = get_user_name($db, $series_info['user']);
    session_start();
    if (isset($_SESSION['user_id']) and $series_info['user'] == $_SESSION['user_id']) {
        $display_buttons = True;
    } else {
        $display_buttons = False;
    }

    /* Page info */
    $page_title = $series_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview/', False),
        $series_info['name'] => na('/DDWT21/week2/series/?series_id='.$series_id, True)
    ]);
    $navigation = get_navigation($navigation_array, 'Series');

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $series_info['name']);
    $page_content = $series_info['abstract'];
    $nbr_seasons = $series_info['seasons'];
    $creators = $series_info['creator'];

    if(isset($_GET['error_msg'])){
        $error_msg = get_error(json_decode($_GET['error_msg'], True));
    }

    /* Choose Template */
    include use_template('series');
}

/* Add series GET */
elseif (new_route('/DDWT21/week2/add/', 'get')) {
    /* Check login */
    if (!check_login()) {
        redirect('/DDWT21/week2/login/');
    }

    /* Get Number of Series */
    $nbr_series = count_series($db);

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Add Series' => na('/DDWT21/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_array, 'Add Series');

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT21/week2/add/';

    if(isset($_GET['error_msg'])){
        $error_msg = get_error(json_decode($_GET['error_msg'], True));
    }

    /* Choose Template */
    include use_template('new');
}

/* Add series POST */
elseif (new_route('/DDWT21/week2/add/', 'post')) {
    /* Add series to database */
    $feedback = add_series($db, $_POST);

    if ($feedback['type'] == 'danger'){
        redirect(sprintf('/DDWT21/week2/add/?error_msg=%s', json_encode($feedback)));
    } else {
        redirect(sprintf('/DDWT21/week2/add/?error_msg=%s', json_encode($feedback)));
    }
}

/* Edit series GET */
elseif (new_route('/DDWT21/week2/edit/', 'get')) {
    /* Check login */
    if (!check_login()) {
        redirect('/DDWT21/week2/login/');
    }

    /* Get Number of Series */
    $nbr_series = count_series($db);

    /* Get series info from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        sprintf("Edit Series %s", $series_info['name']) => na('/DDWT21/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_array, 'Edit');

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $series_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT21/week2/edit/';

    if(isset($_GET['error_msg'])){
        $error_msg = get_error(json_decode($_GET['error_msg'], True));
    }

    /* Choose Template */
    include use_template('new');
}

/* Edit series POST */
elseif (new_route('/DDWT21/week2/edit/', 'post')) {
    /* Get series info from db */
    $series_id = $_POST['series_id'];
    $series_info = get_series_info($db, $series_id);
    $added_by = get_user_name($db, $series_info['user']);

    /* Update series in database */
    $feedback = update_series($db, $_POST, $series_info['user']);

    if ($feedback['type'] == 'success'){
        redirect(sprintf('/DDWT21/week2/series/?series_id=%s&error_msg=%s', $series_id, json_encode($feedback)));
    } else {
        redirect(sprintf('/DDWT21/week2/edit/?series_id=%s&error_msg=%s', $series_id, json_encode($feedback)));
    }
}

/* Remove series */
elseif (new_route('/DDWT21/week2/remove/', 'post')) {
    /* Check login */
    if (!check_login()) {
        redirect('/DDWT21/week2/login/');
    }

    /* Remove series in database */
    $series_id = $_POST['series_id'];
    $feedback = remove_series($db, $series_id);
    $error_msg = get_error($feedback);

    if ($feedback['type'] == 'success'){
        redirect(sprintf('/DDWT21/week2/overview/?error_msg=%s', json_encode($feedback)));
    } else {
        redirect(sprintf('/DDWT21/week2/overview/?error_msg=%s', json_encode($feedback)));
    }

}

/* my account */
elseif (new_route('/DDWT21/week2/myaccount/', 'get')) {
    /* Check login */
    if (!check_login()) {
        redirect('/DDWT21/week2/login/');
    }

    /* get user info */
    $user = get_user_name($db, $_SESSION['user_id']);

    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', False)
    ]);
    $navigation = get_navigation($navigation_array, 'My Account');

    /* Page content */
    $page_subtitle = 'Account';
    $page_content = 'Here you find information regarding your account.';

    /* Choose Template */
    include use_template('account');
}

/* register GET */
elseif (new_route('/DDWT21/week2/register/', 'get')) {
    /* Page info */
    $page_title = 'Registration';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', False)
    ]);
    $navigation = get_navigation($navigation_array, 'Register');

    /* Page content */
    $page_subtitle = 'Here you can register an account';

    /* Choose Template */
    include use_template('register');
}

/* register POST */
elseif (new_route('/DDWT21/week2/register/', 'post')) {
    /* Register user */
    $feedback = register_user($db, $_POST);
    $error_msg = get_error($feedback);

    /*
    if ($feedback['type'] == 'danger') {
        redirect(sprintf('/DDWT21/week2/register/?error_msg=%s',
            json_encode($feedback)));
    } else {
        redirect(sprintf('/DDWT21/week2/myaccount/?error_msg=%s',
            json_encode($feedback)));
    }
    */

    /* Page info */
    $page_title = 'Registration';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', False)
    ]);
    $navigation = get_navigation($navigation_array, 'Register');

    /* Page content */
    $page_subtitle = 'Here you can register an account';

    /* Choose Template */
    include use_template('register');
}

/* login GET */
elseif (new_route('/DDWT21/week2/login/', 'get')) {
    /* Check login */
    if (check_login()) {
        redirect('/DDWT21/week2/myaccount/');
    }

    /* Page info */
    $page_title = 'Log in';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', False)
    ]);
    $navigation = get_navigation($navigation_array, 'Login');

    /* Page content */
    $page_subtitle = 'Log in here';

    /* Choose Template */
    include use_template('login');
}

/* login POST */
elseif (new_route('/DDWT21/week2/login/', 'post')) {
    /* get user info */
    $feedback = login_user($db, $_POST);

    if ($feedback['type'] == 'success') {
        redirect('/DDWT21/week2/myaccount/');
    } else {
        redirect(sprintf('/DDWT21/week2/login/?error_msg=%s',
            json_encode($feedback)));
    }

    /* Page info */
    $page_title = 'Log in';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 2' => na('/DDWT21/week2/', False),
        'Overview' => na('/DDWT21/week2/overview', False)
    ]);
    $navigation = get_navigation($navigation_array, 'Login');

    /* Page content */
    $page_subtitle = 'Log in here';

    /* Choose Template */
    include use_template('login');
}

/* logout POST */
elseif (new_route('/DDWT21/week2/logout/', 'get')) {
    $feedback = logout_user();
    $error_msg = get_error($feedback);
    redirect('/DDWT21/week2/myaccount/');
}

else {
    http_response_code(404);
    echo '404 Not Found';
}
