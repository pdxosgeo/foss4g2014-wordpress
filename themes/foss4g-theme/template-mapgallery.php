<?php
/*
Template Name: Map Gallery
*/
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
?>

<?php get_header(); ?>
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
            <?php the_content();
            $subs = ninja_forms_get_all_subs( 3 );
            foreach ($subs as $sub_text) {
              echo("<br/><br/>");
              $sub = unserialize($sub_text['data']);
            ?>
              <table width=600>
              <tr><td>Name</td><td>Title</td><td>Description></td><td>Twitter</td></tr>
              <tr>
                <td><?php print($sub[1]['user_value']); ?></td>
                <td><?php print($sub[4]['user_value']); ?></td>
                <td><?php print($sub[6]['user_value']); ?></td>
                <td><?php print($sub[3]['user_value']); ?></td>
              </tr>
              </table>
            <?php              
              echo("<br/><br/>");
              print("<pre>".print_r($sub,true)."</pre>");
            }
            ?>
        <?php endwhile; // end of the loop. ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

