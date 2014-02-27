<?php

function weighted_sponsor_join($join)
{
$join .= "JOIN wp_term_relationships ON wp_posts.ID=wp_term_relationships.object_id
  JOIN wp_terms ON wp_term_relationships.term_taxonomy_id=wp_terms.term_id";
return $join;
}

function weighted_sponsor( $orderby )
{
     return " 
       CASE slug 
         WHEN 'platinum' then 10
         WHEN 'gold' then log(6) * rand()
         WHEN 'silver' then log(5) * rand()
         WHEN 'bronze' then log(4) * rand()
         WHEN 'supporter' then log(3) * rand()
         WHEN 'media' then log(2) * rand()
         ELSE rand() END
         DESC";
}
?>
