<?php
namespace Voce\Thermal\v21\Models;

class Posts {

    public function find( $args = array( ), &$length = null ) {

        $args['post_status'] = 'publish';
        $args['post_type']   = 'post';
        $args['orderby']     = 'meta_value';

        if(empty($args['s'])) {
            unset($args['s']);
        }

        $now = new \DateTime();
        $now->add(new \DateInterval('P1D'));

        $exclude = array (
              'key'     => 'MN_exclude',
              'value'   => 1,
              'compare' => '!='
        );

        if(isset($args['list']) && $args['list'] == 'next') {
            $args['meta_query'] = array(
                array (
                  'key'     => 'MN_publication_at',
                  'value'   => $now->format('Y-m-d'),
                  'compare' => '>'
                ),
                $exclude
            );
        }
        else {
            $args['meta_query'] = array(
                array (
                  'key'     => 'MN_publication_at',
                  'value'   => $now->format('Y-m-d'),
                  'compare' => '<='
                ),
                $exclude
            );
        }

        $wp_posts = new \WP_Query( $args );

        $args_length = $args;
        unset($args['posts_per_page']);

        $count_posts = wp_count_posts();
        $length = $count_posts->publish;

        $args_length = $args;
        unset($args_length['posts_per_page'], $args_length['offset']);
        $wp_all_posts = new \WP_Query( $args_length );
        $length = count($wp_all_posts->posts);

        // if(isset($args['s']) && !$length)
        // {
        //     $args['meta_query'] = array(
        //         'relation' => 'OR',
        //         array(
        //             'key' => 'MN_author',
        //             'value' => $args['s'],
        //             'compare' => 'LIKE'
        //         ),
        //         array(
        //             'key' => 'MN_type',
        //             'value' => $args['s'],
        //             'compare' => 'LIKE'
        //         ),
        //         array(
        //             'key' => 'MN_editor',
        //             'value' => $args['s'],
        //             'compare' => 'LIKE'
        //         ),
        //         $exclude
        //     );
        //
        //     unset($args['s']);
        //
        //     $wp_posts = new \WP_Query( $args );
        //
        //     $args_length = $args;
        //     unset($args_length['posts_per_page'], $args_length['offset']);
        //     $wp_all_posts = new \WP_Query( $args_length );
        //     $length = count($wp_all_posts->posts);
        // }

        if ( $wp_posts->have_posts() ) {
            return $wp_posts->posts;
        }
        return array();

    }

    public function findById($id) {
        return get_post($id);
    }

    public function _filter_posts_where_handleDateRange( $where, $wp_query ) {
        if ( ($before = $wp_query->get( 'before' ) ) && $beforets = strtotime( $before ) ) {
            if ( preg_match( '$:[0-9]{2}\s[+-][0-9]{2}$', $before ) || strpos( $before, 'GMT' ) !== false ) {
                //adjust to site time if a timezone was set in the timestamp
                $beforets += ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
            }

            $where .= sprintf( " AND post_date < '%s'", gmdate( 'Y-m-d H:i:s', $beforets ) );
        }
        if ( ($after = $wp_query->get( 'after' ) ) && $afterts = strtotime( $after ) ) {
            if ( preg_match( '$:[0-9]{2}\s[+-][0-9]{2}$', $after ) || strpos( $after, 'GMT' ) !== false ) {
                //adjust to site time if a timezone was set in the timestamp
                $afterts += ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
            }

            $where .= sprintf( " AND post_date > '%s'", gmdate( 'Y-m-d H:i:s', $afterts ) );
        }
        remove_filter('posts_search', array($this, __METHOD__));
        return $where;
    }

}
