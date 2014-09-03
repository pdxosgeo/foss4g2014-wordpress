<?php
/*
Template Name: Map Gallery Vote
*/

if ($_GET['action'] && $_GET['action'] == 'check') {
    header('Content-type: application/json');
    $foo = array(['a'=>1],['b'=>2],['c'=>3]);

    //Encodes with some extra fancy escaping as seen in ninja_forms_json_response in database.php in plugin folder
    echo(json_encode($foo, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_PRETTY_PRINT));    
} else {
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