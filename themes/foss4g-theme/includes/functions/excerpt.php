<?php
// new excerpt script based on core excerpt, but better and keeps <p> tags
function better_excerpt($text) {
  global $post;
  if ( '' == $text ) {
    $text = get_the_content('');
    $text = apply_filters('the_content', $text);
    $text = str_replace('\]\]\>', ']]&gt;', $text);
    $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
    $text = strip_tags($text, '<p>');
    $excerpt_length = 100;
    $words = explode(' ', $text, $excerpt_length + 1);
    if (count($words)> $excerpt_length) {
      array_pop($words);
      array_push($words, '[...]');
      $text = implode(' ', $words);
    }
  }
return $text;
}
// remove core excerpt trim script
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
// add the new one above
add_filter('get_the_excerpt', 'better_excerpt');