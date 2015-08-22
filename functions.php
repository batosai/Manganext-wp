<?php

require_once('thermal-api/thermal-api.php');

remove_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
remove_action('do_feed_rss', 'do_feed_rss', 10, 1);
remove_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
remove_action('do_feed_atom', 'do_feed_atom', 10, 1);

// the_post_thumbnail();
// the_post_thumbnail('thumbnail');       // Thumbnail (default 150px x 150px max)
// the_post_thumbnail('medium');          // Medium resolution (default 300px x 300px max)
// the_post_thumbnail('large');           // Large resolution (default 640px x 640px max)
// the_post_thumbnail('full');            // Original image resolution (unmodified)
// the_post_thumbnail( array(100,100) );  // Other resolutions

add_filter('sanitize_file_name', 'remove_accents');

add_filter('rwmb_meta_boxes'            , 'registerMetaBoxes');
add_action('admin_menu'                 , 'removeEditorMenu');
add_action('wp_before_admin_bar_render' , 'adminBarRender');
add_action('admin_menu'                 , 'menuPages');
add_action('init'                       , 'block_administration');
add_action('after_setup_theme'          , 'wp_rpt_activation_hook');


function wp_rpt_activation_hook() {
    if ( function_exists('add_theme_support') ) {
        add_theme_support('post-thumbnails');

        add_image_size('thumbnail-450x625', 450, 625, true);
        add_image_size('thumbnail-215x300', 215, 300, true);
    }
}


function block_administration()
{
    global $pagenow;

    if(isset($_GET['loginFacebook'], $_GET['redirect'])) return;
    if($pagenow == 'admin-ajax.php') return;

    if ( $pagenow == 'wp-login.php' || is_admin() ){

        if(is_user_logged_in() && !current_user_can('administrator')) {
            wp_redirect( get_bloginfo('url') );
            exit();
        }
    }
}

function removeEditorMenu()
{
    remove_action('admin_menu', '_add_themes_utility_last', 101);
}

function menuPages()
{
    remove_menu_page('edit.php?post_type=page');
    // remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );   // Remove posts->tags submenu
    // remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );   // Remove posts->categories submenu
}

function adminBarRender()
{
    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('wp-logo');
}

function registerMetaBoxes($meta_boxes)
{
    $prefixe = 'MN_';

    $meta_boxes[] = array(
        'id' => 'data',
        'title' => 'Contenu',
        'pages' => array('post'),
        'context' => 'normal',
        'priority' => 'high',
        'autosave' => true,
        'fields' => array(
            array(
                'name' => 'Volume',
                'id'   => $prefixe . "volume",
                'type' => 'text'
            ),
            array(
                'name' => 'Titre VO',
                'id'   => $prefixe . "vo_title",
                'type' => 'text'
            ),
            array(
                'name' => 'Titre traduit',
                'id'   => $prefixe . "translate_title",
                'type' => 'text'
            ),
            array(
                'name' => 'Dessinateur(s)',
                'id'   => $prefixe . "designer",
                'type' => 'text'
            ),
            array(
                'name' => 'Auteurs',
                'id'   => $prefixe . "author",
                'type' => 'text'
            ),
            array(
                'name' => 'Collection',
                'id'   => $prefixe . "collection",
                'type' => 'text'
            ),
            array(
                'name' => 'Type',
                'id'   => $prefixe . "type",
                'type' => 'text'
            ),
            array(
                'name' => 'Genre',
                'id'   => $prefixe . "genre",
                'type' => 'text'
            ),
            array(
                'name' => 'Editeur',
                'id'   => $prefixe . "editor",
                'type' => 'text'
            ),
            array(
                'name' => 'Editeur VO',
                'id'   => $prefixe . "vo_editor",
                'type' => 'text'
            ),
            array(
                'name' => 'Prépublication',
                'id'   => $prefixe . "preprint",
                'type' => 'text'
            ),
            array(
                'name' => 'Date de publication',
                'id'   => $prefixe . "publication_at",
                'type' => 'date'
            ),
            array(
                'name' => 'Date de 1ere publication',
                'id'   => $prefixe . "first_publication_at",
                'type' => 'date'
            ),
            array(
                'name' => 'Illustration',
                'id'   => $prefixe . "illustration",
                'type' => 'text'
            ),
            array(
                'name' => 'Origine',
                'id'   => $prefixe . "origin",
                'type' => 'text'
            ),
            array(
                'name' => 'EAN',
                'id'   => $prefixe . "ean",
                'type' => 'text'
            ),
            array(
                'name' => 'Code prix',
                'id'   => $prefixe . "price_code",
                'type' => 'text'
            ),
            array(
                'name' => 'Prix',
                'id'   => $prefixe . "price",
                'type' => 'text'
            ),
            array(
                'name' => 'Source url',
                'id'   => $prefixe . "source_url",
                'type' => 'text'
            ),
            array(
                'name' => 'Image',
                'id'   => $prefixe . "image",
                'type' => 'text'
            ),
            array(
                'name' => 'Limite d\'age',
                'id'   => $prefixe . "age_number",
                'type' => 'text'
            ),
            array(
                'name' => 'Nombre de tomes',
                'id'   => $prefixe . "outputs_number",
                'type' => 'textarea'
            ),
            array(
                'name' => 'Exclure',
                'id'   => $prefixe . "exclude",
                'type' => 'checkbox'
            )
        )
    );

    return $meta_boxes;
}
