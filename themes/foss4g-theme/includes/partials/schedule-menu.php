<div class="schedule-menu-container">
  <div class="container">
    <?php 

      // siblings
      if($post->post_parent)
      $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
      else
      $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
      if ($children) { ?>
          <ul class="schedule-menu">
          <?php echo $children; ?>
          </ul>
    <?php } ?>
  </div>
</div>