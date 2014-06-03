<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="page">
  <div class="container">
    <?php the_title('<h1 class="title">','</h1>',true); ?>
    <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
    <ul id="day-sort" class="sorting">
      <li id="day-1" class="current">Wednesday</li>
      <li id="day-2">Thursday</li>
      <li id="day-3">Friday</li>
    </ul>   
    <ul id="session-sort" class="sorting">
      <li id="session-1" class="current">Session 1</li>
      <li id="session-2">Session 2</li>
      <li id="session-3">Session 3</li>
    </ul>   
    <?php get_schedule(); ?>
  </div>  
</section>

<?php get_footer(); ?>

<script>
// schedule sorting and everything

</script>