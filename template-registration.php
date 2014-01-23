<?php get_header(); ?>
<section class="page-header">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <?php endwhile; // end of the loop. ?>
    </div>
</section>
<section class="page-content">
<div class="container">
    <div class="row">
        <div class="col-md-8 content">
        
        <?php
            add_action('register_form','myplugin_add_registration_fields');

            function myplugin_add_registration_fields() {

                //Get and set any values already sent
                $user_extra = ( isset( $_POST['user_extra'] ) ) ? $_POST['user_extra'] : '';
                ?>

                <p>
                    <label for="user_extra"><?php _e('Extra Field','mydomain') ?><br />
                    <input type="text" name="user_extra" id="user_extra" class="input" value="<?php echo esc_attr(stripslashes($user_extra)); ?>" size="25" /></label>
                </p>

                <?php
            }
            ?>





        </div>
        <div class="col-md-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>