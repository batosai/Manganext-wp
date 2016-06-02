<?php

global $wpdb;


// $title = addslashes(htmlspecialchars("Hibi Chouchou - Edelweiss & Papillons"));
// echo $title;
// $volume = 'Vol.2';

//             $join = $where = '';
//             if($volume) {
//                 $join = "INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.id";
//                 $where = "AND (pm.meta_key='MN_volume' AND pm.meta_value='{$volume}')";
//             }

//             $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts p
//  $join
//  WHERE p.post_title = '{$title}' AND p.post_type='post' $where");

// var_dump( $id );
// var_dump(get_post($id));


// Doublon

// $sql   = "SELECT ID FROM mn_posts WHERE 1";
// $posts = $wpdb->get_results($sql);
//
// foreach ($posts as $post) {
//   $m     = array();
//   $ids   = array();
//   $meta = $wpdb->get_results("SELECT * FROM mn_postmeta WHERE post_id=" . $post->ID);
//
//   foreach ($meta as $me) {
//     if(!in_array($me->meta_key, $m)) {
//       $m[] = $me->meta_key;
//     }
//     else {
//       $ids[] = $me->meta_id;
//     }
//   }
//
//   if(count($ids))
//     $wpdb->get_results("DELETE FROM mn_postmeta WHERE meta_id IN (" . implode(',', $ids) . ")");
// }
