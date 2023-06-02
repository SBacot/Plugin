<?php
/*
 * Plugin Name: Temps de lecture
 * Plugin URI: https://sandra-bacot.fr
 * Description: Ajoute un temps de lecture aux articles de votre site Wordpress.
 * Version: 1.0.0
 * Author: Sandra Bacot
 * Author URI: https://sandra-bacot.fr
 * Text Domain: lecture
*/


function calcul_reading_time( $title, $id = null ) {
    // if not in the post page, nothing to do!
    if(!is_single()) {
        return $title;
    }
    // retrieve the current post
    $post = get_post();
    // retrieve the post content without html tags
    $content = strip_tags($post->post_content);
    // count the number of words in content
    $nb_words = str_word_count($content);

    // calcul the time needed to read the post
    // and round to the upper integer
    $time = ceil($nb_words / 183);

    // add the reading time in post title
    $title .= "<small> | $time minutes</small>";
    return $title;
}
add_filter( 'the_title', 'calcul_reading_time', 10, 2 );

// fire when display title in menu
function restore_menu_item_title( $title, $item ) {
    // remove filter to get the original post title
    remove_filter( 'the_title', 'calcul_reading_time', 10, 2 );
    remove_filter( 'the_title', 'calcul_reading_level', 10, 2 );
    // retrieve the original post title
    $title = get_the_title($item->object_id);
    // add the filter back
    add_filter( 'the_title', 'calcul_reading_time', 10, 2 );
    add_filter( 'the_title', 'calcul_reading_level', 10, 2 );
    // return the title to display
    return $title;
}


function calcul_reading_level($title, $id = null) {
    if(!is_single()){
        return $title;
    }

    $post = get_post($id);
    $content = strip_tags($post->post_content);
    // count the number of words in content
    $nb_letter = strlen($content);
    $nb_words = max(1,str_word_count(strip_tags($content)));

    // var_dump($nb_words);
    $nbSentences  = count(preg_split('/[.|!|?]/', $content));
    $average_letters= (($nb_letter/$nb_words) * 100);
    $average_words = (($nbSentences/$nb_words) * 100);
    $difficult= ceil((0.0588 * $average_letters) - (0.296 *  $average_words) - 15.8);

    //on fait le switch pour déterminer le niveau de 1 a 12 
    $difficulty = "";
   switch ($difficult) {
        case $difficult <= 1:
            $difficulty = "CP";
            break;
        case $difficult == 2:
            $difficulty = "CE1";
            break;
        case $difficult == 3:
            $difficulty = "CE2";
            break;
        case $difficult == 4:
            $difficulty = "CM1";
            break;
        case $difficult == 5:
            $difficulty = "CM2";
            break;
        case $difficult == 6:
            $difficulty = "6em";
            break;
        case $difficult == 7:
            $difficulty = "5em";
            break;
        case $difficult == 8:
            $difficulty = "4em";
            break;
        case $difficult == 9:
            $difficulty = "3em";
            break;
        case $difficult == 10:
            $difficulty = "SECONDE";
            break;
        case $difficult == 11:
            $difficulty = "PREMIERE";
            break;
     default:
            $difficulty = "TERMINALE";
            break;
    }

    // add the reading time in post title
    $title .= "<small> | $difficulty</small>";
    return $title;

}
add_filter( 'the_title', 'calcul_reading_level', 10, 2 );

function restore_menu_item_title_difficult( $title, $item ) {
    // remove filter to get the original post title
    remove_filter( 'the_title', 'calcul_reading_level', 10, 2 );
    // retrieve the original post title
    $title = get_the_title($item->object_id);
    // add the filter back
    add_filter( 'the_title', 'calcul_reading_level', 10, 2 );
    // return the title to display
    return $title;
}
add_filter( 'nav_menu_item_title', 'restore_menu_item_title', 10, 2 );
// add_filter( 'nav_menu_item_title', 'restore_menu_item_title_difficult', 10, 2 );
