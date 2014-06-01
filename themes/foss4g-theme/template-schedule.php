<?php
/*
Template Name: Schedule
*/
?>

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
    <ul id="track-sort" class="sorting">
      <li id="track-1" class="current">Track 1</li>
      <li id="track-2">Track 2</li>
      <li id="track-3">Track 3</li>
      <li id="track-4">Track 4</li>
      <li id="track-5">Track 5</li>
      <li id="track-6">Track 6</li>
      <li id="track-7">Track 7</li>
      <li id="track-8">Track 8</li>
      <li id="track-9">Track 9</li>
    </ul>
    <?php get_schedule(); ?>
    <!-- <div id="d1t1" class="sched-block"><ul></ul></div>
    <div id="d1t2" class="sched-block"><ul></ul></div>
    <div id="d1t3" class="sched-block"><ul></ul></div>
    <div id="d1t4" class="sched-block"><ul></ul></div>
    <div id="d1t5" class="sched-block"><ul></ul></div>
    <div id="d1t6" class="sched-block"><ul></ul></div>
    <div id="d1t7" class="sched-block"><ul></ul></div>
    <div id="d1t8" class="sched-block"><ul></ul></div>
    <div id="d2t1" class="sched-block"><ul></ul></div>
    <div id="d2t2" class="sched-block"><ul></ul></div>
    <div id="d2t3" class="sched-block"><ul></ul></div>
    <div id="d2t4" class="sched-block"><ul></ul></div>
    <div id="d2t5" class="sched-block"><ul></ul></div>
    <div id="d2t6" class="sched-block"><ul></ul></div>
    <div id="d2t7" class="sched-block"><ul></ul></div>
    <div id="d2t8" class="sched-block"><ul></ul></div>    
    <div id="d3t1" class="sched-block"><ul></ul></div>
    <div id="d3t2" class="sched-block"><ul></ul></div>
    <div id="d3t3" class="sched-block"><ul></ul></div>
    <div id="d3t4" class="sched-block"><ul></ul></div>
    <div id="d3t5" class="sched-block"><ul></ul></div>
    <div id="d3t6" class="sched-block"><ul></ul></div>
    <div id="d3t7" class="sched-block"><ul></ul></div>
    <div id="d3t8" class="sched-block"><ul></ul></div>    
    <div id="d4t1" class="sched-block"><ul></ul></div>
    <div id="d4t2" class="sched-block"><ul></ul></div>
    <div id="d4t3" class="sched-block"><ul></ul></div>
    <div id="d4t4" class="sched-block"><ul></ul></div>
    <div id="d4t5" class="sched-block"><ul></ul></div>
    <div id="d4t6" class="sched-block"><ul></ul></div>
    <div id="d4t7" class="sched-block"><ul></ul></div>
    <div id="d4t8" class="sched-block"><ul></ul></div>    
    <div id="d5t1" class="sched-block"><ul></ul></div>
    <div id="d5t2" class="sched-block"><ul></ul></div>
    <div id="d5t3" class="sched-block"><ul></ul></div>
    <div id="d5t4" class="sched-block"><ul></ul></div>
    <div id="d5t5" class="sched-block"><ul></ul></div>
    <div id="d5t6" class="sched-block"><ul></ul></div>
    <div id="d5t7" class="sched-block"><ul></ul></div>
    <div id="d5t8" class="sched-block"><ul></ul></div>    
    <div id="d6t1" class="sched-block"><ul></ul></div>
    <div id="d6t2" class="sched-block"><ul></ul></div>
    <div id="d6t3" class="sched-block"><ul></ul></div>
    <div id="d6t4" class="sched-block"><ul></ul></div>
    <div id="d6t5" class="sched-block"><ul></ul></div>
    <div id="d6t6" class="sched-block"><ul></ul></div>
    <div id="d6t7" class="sched-block"><ul></ul></div>
    <div id="d6t8" class="sched-block"><ul></ul></div>    
    <div id="d7t1" class="sched-block"><ul></ul></div>
    <div id="d7t2" class="sched-block"><ul></ul></div>
    <div id="d7t3" class="sched-block"><ul></ul></div>
    <div id="d7t4" class="sched-block"><ul></ul></div>
    <div id="d7t5" class="sched-block"><ul></ul></div>
    <div id="d7t6" class="sched-block"><ul></ul></div>
    <div id="d7t7" class="sched-block"><ul></ul></div>
    <div id="d7t8" class="sched-block"><ul></ul></div>    
    <div id="d8t1" class="sched-block"><ul></ul></div>
    <div id="d8t2" class="sched-block"><ul></ul></div>
    <div id="d8t3" class="sched-block"><ul></ul></div>
    <div id="d8t4" class="sched-block"><ul></ul></div>
    <div id="d8t5" class="sched-block"><ul></ul></div>
    <div id="d8t6" class="sched-block"><ul></ul></div>
    <div id="d8t7" class="sched-block"><ul></ul></div>
    <div id="d8t8" class="sched-block"><ul></ul></div>    --> 
  </div>
</section>

<?php get_footer(); ?>

<script>
// schedule sorting and everything

</script>