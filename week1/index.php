<?php
/**
 * Controller
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

include 'model.php';

$db = connect_db('localhost:8889', 'ddwt21_week1', 'ddwt21', 'ddwt21');

$rows = count_series($db);

$series_list = get_series($db);

/* Landing page */
if (new_route('/DDWT21/week1/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Home' => na('/DDWT21/week1/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', True),
        'Overview' => na('/DDWT21/week1/overview/', False),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT21/week1/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', True),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_series_table($series_list);


    /* Choose Template */
    include use_template('main');
}

/* Single series */
elseif (new_route('/DDWT21/week1/series/', 'get')) {
    /* Get series from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);
    $series_name = $series_info['name'];
    $series_abstract = $series_info['abstract'];
    $nbr_seasons = $series_info['seasons'];
    $creators = $series_info['creator'];

    /* Page info */
    $page_title = $series_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview/', False),
        $series_name => na('/DDWT21/week1/series/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', True),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf('Information about %s', $series_name);
    $page_content = $series_abstract;

    /* Choose Template */
    include use_template('series');
}

/* Add series GET */
elseif (new_route('/DDWT21/week1/add/', 'get')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Add Series' => na('/DDWT21/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', False),
        'Add Series' => na('/DDWT21/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = 'Add Series';
    $form_action = '/DDWT21/week1/add/';

    /* Choose Template */
    include use_template('new');

}

/* Add series POST */
elseif (new_route('/DDWT21/week1/add/', 'post')) {
    $name = $_POST['Name'];
    $creator = $_POST['Creator'];
    $seasons = $_POST['Seasons'];
    $abstract = $_POST['Abstract'];

    $message = add_series($db, $name, $creator, $seasons, $abstract);
    echo get_error($message);

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Add Series' => na('/DDWT21/week1/add/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', False),
        'Add Series' => na('/DDWT21/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = 'Add Series';
    $form_action = '/DDWT21/week1/add/';

    $rows = count_series($db);

    include use_template('new');
}

/* Edit series GET */
elseif (new_route('/DDWT21/week1/edit/', 'get')) {
    /* Get series info from db */
    $series_id = $_GET['series_id'];
    $series_info = get_series_info($db, $series_id);

    $series_name = $series_info['name'];
    $series_abstract = $series_info['abstract'];
    $nbr_seasons = $series_info['seasons'];
    $creators = $series_info['creator'];

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        sprintf('Edit Series %s', $series_name) => na('/DDWT21/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', False),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf('Edit %s', $series_name);
    $page_content = 'Edit the series below.';
    $submit_btn = 'Edit Series';
    $form_action = '/DDWT21/week1/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit series POST */
elseif (new_route('/DDWT21/week1/edit/', 'post')) {
    /* Get series info from db */
    $series_id = $_POST['series_id'];
    $series_info = get_series_info($db, $series_id);
    $series_name = $series_info['name'];

    $newname = $_POST['Name'];
    $creators = $_POST['Creator'];
    $nbr_seasons = $_POST['Seasons'];
    $abstract = $_POST['Abstract'];

    $message = update_series($db, $series_name, $newname, $creators, $nbr_seasons, $abstract, $series_id);
    echo get_error($message);
    /**
    $series_name = $series_info['name'];
    $series_abstract = $series_info['abstract'];
    $nbr_seasons = $series_info['seasons'];
    $creators = $series_info['creator'];
    **/
    /* Page info */
    if($message['type'] == 'success') {
        $page_title = $newname;
    } else {
        $page_title = $series_name;
    }
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview/', False),
        $page_title => na('/DDWT21/week1/series/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', False),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf('Information about %s', $series_name);
    $page_content = $series_info['abstract'];

    /* Choose Template */
    include use_template('series');
}

/* Remove series */
elseif (new_route('/DDWT21/week1/remove/', 'post')) {
    /* Remove series in database */
    $series_id = $_POST['series_id'];
    $feedback = remove_series($db, $series_id);
    $error_msg = get_error($feedback);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT21' => na('/DDWT21/', False),
        'Week 1' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT21/week1/', False),
        'Overview' => na('/DDWT21/week1/overview', True),
        'Add Series' => na('/DDWT21/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $series_list = get_series($db);
    $left_content = get_series_table($series_list);
    $rows = count_series($db);

    /* Choose Template */
    include use_template('main');
}

else {
    http_response_code(404);
    echo '404 Not Found';
}