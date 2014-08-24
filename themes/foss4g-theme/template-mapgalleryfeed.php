<?php
/*
Template Name: Map Gallery Feed
*/

header('Content-type: application/json');

$subs = ninja_forms_get_all_subs( 3 );
$clean_subs = array();

foreach ($subs as $sub_text) {    
    //Dig out and unpack the submission data
    $sub_id = $sub_text['id'];  //Unique ID from database
    $sub_ser = $sub_text['data'];
    $sub = unserialize($sub_ser);
    //Image path
    $imgpath = "/wp-content/uploads/mapgallery/";
    //Pull out just the fields we need for the public gallery    

    $cs = array(
        'id' => $sub_id,
        'name' => $sub[1]['user_value'],
        'twitter' => $sub[3]['user_value'],
        'org' => $sub[4]['user_value'],
        'name2' => $sub[5]['user_value'],
        'desc' => $sub[6]['user_value'],
        'category' => $sub[7]['user_value'],
        'format' => $sub[8]['user_value'],
        'map_url' => $sub[9]['user_value'],
        'other_url' => $sub[10]['user_value'],
        'license' => $sub[11]['user_value'],
        'small' => $imgpath."small/".$sub_id.".jpg?dl=1",
        'med' => $imgpath."med/".$sub_id.".jpg?dl=1",
        'large' => $imgpath."large/".$sub_id.".jpg?dl=1"
    );    
    $clean_subs[] = $cs;
}

//Encodes with some extra fancy escaping as seen in ninja_forms_json_response in database.php in plugin folder
echo(json_encode($clean_subs, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_PRETTY_PRINT));

?>