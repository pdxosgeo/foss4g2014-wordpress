<?php
/*
Template Name: Map Gallery Vote
*/

if ($_GET['action'] && $_GET['action'] == 'fetch') {
    doFetch();   
} else {
    doForm();
}

/* 
Fetch votes in last hour by IP address and return as JSON 
*/
function doFetch() {
    header('Content-type: application/json');

    //Get IP address of client
    $ip = $_SERVER['REMOTE_ADDR']?:
    getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED');

    //Generate nonce (number used once) for allowing voting via Ajax
    $nonce = wp_create_nonce('nf_form_'+absint(4));

    //Get all votes
    $vote_recs = ninja_forms_get_all_subs(4);
    $votes = array();

    //Package vote info we care about
    foreach ($vote_recs as $vote_rec) {
        $vote_vals = unserialize($vote_rec['data']);
        $vote = array();
        foreach ($vote_vals as $vote_val) { 
            switch ($vote_val['field_id']) {
                case 31:
                    $vote['mapid'] = intval($vote_val['user_value']);
                    break;
                case 32:
                    $vote['timestamp'] = intval($vote_val['user_value']);
                    break;
                case 33:
                    $vote['ip'] = $vote_val['user_value'];
                    break;
            }            
        }
        if ($vote['ip'] == $ip) {
            $votes[] = $vote;
        }
    }

    $result = array('nonce'=>$nonce, 'ip'=>$ip, 'votes'=>$votes);

    //Encodes with some extra fancy escaping as seen in ninja_forms_json_response in database.php in plugin folder
    echo(json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_PRETTY_PRINT)); 
}

/*
Print default template content
*/
function doForm() {
    get_header(); 
?>

    <section class="page-header">
        <div class="container">
            <?php while ( have_posts() ) : the_post(); ?>
            <h1><?php the_title(); ?></h1>
        </div>
    </section>
    <section class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 content">
                <?php the_content(); ?>
            <?php endwhile; // end of the loop. ?>
            </div>
        </div>
    </div>

<?php
    get_footer();
}
?>