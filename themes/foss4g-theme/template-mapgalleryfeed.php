<?php
/*
Template Name: Map Gallery Feed
*/

header('Content-type: application/json');

$subs = ninja_forms_get_all_subs( 3 );
$clean_subs = array();

//print_r(unserialize($subs[32]['data']));

foreach ($subs as $sub_text) {    
    //Dig out and unpack the submission data
    $sub_id = $sub_text['id'];  //Unique ID from database
    $sub_ser = $sub_text['data'];
    $sub = unserialize($sub_ser);

    //Image path
    $imgpath = "/wp-content/uploads/mapgallery/";

    //Create sanitized submission record for public
    $pubsub = array();
    foreach ($sub as $sub_value) {    
        
        $pubsub['id'] = $sub_id;
        $pubsub['small'] = $imgpath."small/".$sub_id.'.jpg';
        $pubsub['medium'] = $imgpath."medium/".$sub_id.'.jpg';
        $pubsub['large'] = $imgpath."large/".$sub_id.'.jpg';
        $pubsub['orig'] = $imgpath."orig/".$sub_id.'.png';

        switch ($sub_value['field_id']) {
            case 30:
                $pubsub['title'] = $sub_value['user_value'];
                break;
            case 17:
                $pubsub['category'] = $sub_value['user_value'];
                foreach ($pubsub['category'] as &$cat) {
                    //Trim extra white space
                    $cat = trim($cat);
                }
                break;
            case 18:
                $pubsub['format'] = $sub_value['user_value'];
                break;
            case 19:
                $pubsub['name'] = $sub_value['user_value'];
                break;                
            case 20:
                $pubsub['org'] = $sub_value['user_value'];
                break;                
            case 21:
                $pubsub['name2'] = $sub_value['user_value'];
                break;                
            case 22:
                $pubsub['desc'] = $sub_value['user_value'];
                break;       
            case 24:
                $pubsub['map_url'] = $sub_value['user_value'];
                break;                         
            case 26:
                $pubsub['twitter'] = $sub_value['user_value'];
                break;                
            case 27:
                $pubsub['license'] = $sub_value['user_value'];
                break; 
            case 29:
                $pubsub['other_url'] = $sub_value['user_value'];
                break; 
        }
    }
 
    $clean_subs[] = $pubsub;
}

//Encodes with some extra fancy escaping as seen in ninja_forms_json_response in database.php in plugin folder
echo(json_encode($clean_subs, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_PRETTY_PRINT));

?>