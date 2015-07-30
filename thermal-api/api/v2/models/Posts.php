<?php
namespace Voce\Thermal\v2\Models;

class Posts {

    public function find( $args = array( ), &$length = null ) {

        //add filter for before/after handling, hopefully more complex date querying
        //will exist by wp3.7
        // if ( isset( $args['before'] ) || isset( $args['after'] ) ) {
        //     add_filter( 'posts_where', array( $this, '_filter_posts_where_handleDateRange' ), 10, 2 );
        // }

        // if( isset( $args['post_type'] ) && in_array('attachment', (array) $args['post_type'])) {
        //  if(empty($args['post_status'])) {
        //      $args['post_status'] = array('inherit');
        //  } else {
        //      $args['post_status'] = array_merge((array) $args['post_status'], array('inherit'));
        //  }
        // }

        // if( empty( $args['post_status'] ) ) {
        //  //a post_status is required
        //  return array();
        // }

        $args['post_status'] = 'publish';
        $args['post_type']   = 'post';
        $args['orderby']     = 'meta_value';
        // $args['meta_key']    = 'MN_publication_at';
        // $args['category__not_in'] = array(1486);

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

        if(isset($args['s']) && !$length)
        {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'MN_author',
                    'value' => $args['s'],
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'MN_type',
                    'value' => $args['s'],
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'MN_editor',
                    'value' => $args['s'],
                    'compare' => 'LIKE'
                ),
                $exclude
            );

            unset($args['s']);

            $wp_posts = new \WP_Query( $args );

            $args_length = $args;
            unset($args_length['posts_per_page'], $args_length['offset']);
            $wp_all_posts = new \WP_Query( $args_length );
            $length = count($wp_all_posts->posts);
        }



/*
        global $wpdb;
        $where = $join = '';
        $now   = new \DateTime();

        if(isset($args['list']) && $args['list'] == 'next') {
          $where = "( mt.meta_value!='Invalid date' AND CAST(mt.meta_value AS CHAR) > '{$now->format('Y-m-d')}' )";
        }
        else {
          $where = "( mt.meta_value!='Invalid date' AND CAST(mt.meta_value AS CHAR) <= '{$now->format('Y-m-d')}' )";
        }

        if(isset($args['s'])) {
            // $join   = "INNER JOIN mn_postmeta AS mt2 ON ( mn_posts.ID = mt2.post_id AND mt2.meta_key = 'MN_author') ";
            // $join  .= "INNER JOIN mn_postmeta AS mt3 ON ( mn_posts.ID = mt3.post_id AND mt3.meta_key = 'MN_type') ";
            // $join  .= "INNER JOIN mn_postmeta AS mt4 ON ( mn_posts.ID = mt4.post_id AND mt4.meta_key = 'MN_editor') ";

            // $where .= "AND ((mn_posts.post_title LIKE '%{$args['s']}%') OR (mn_posts.post_content LIKE '%{$args['s']}%') OR ( mt2.meta_value LIKE '%{$args['s']}%') OR ( mt3.meta_value LIKE '%{$args['s']}%') OR ( mt4.meta_value LIKE '%{$args['s']}%'))";
            $where .= "AND ((mn_posts.post_title LIKE '%{$args['s']}%') OR (mn_posts.post_content LIKE '%{$args['s']}%'))";
        }

        if(isset($args['per_page'])) {
            $args['posts_per_page'] = $args['per_page'];
            unset($args['per_page']);
        }

        $querystr = "SELECT mn_posts.id FROM mn_posts
        INNER JOIN mn_postmeta AS mt ON ( mn_posts.ID = mt.post_id AND mt.meta_key = 'MN_publication_at')
        $join
        WHERE 1=1 AND
        $where AND
        mn_posts.post_type = 'post' AND mn_posts.post_status = 'publish'
        GROUP BY mn_posts.ID
        ORDER BY mt.meta_value {$args['order']} LIMIT {$args['offset']}, {$args['posts_per_page']}";

        // $wp_posts = $wpdb->get_results($querystr, OBJECT);

        $ids = $wpdb->get_col($querystr);

        $a = array();
        if(count($ids)) {
            //  $a['post__in'] = $ids;
            //  $a['orderby'] = 'mt.meta_value';
            //  $a['order'] = $args['order'];
            $a = array(
              'meta_key' => 'MN_publication_at',
              'orderby' => 'meta_value',
              'order' => $args['order'],
              'post__in' => $ids,
              'meta_query' => array(
                   array(
                       'meta_key' => 'MN_publication_at',
                       'orderby' => 'meta_value',
                       'order' => $args['order'],
                   )
               ));
        }
        $wp_posts = new \WP_Query($a);
        var_dump($wp_posts->request);exit;

        $querystr = "SELECT mn_posts.ID FROM mn_posts
        INNER JOIN mn_postmeta AS mt ON ( mn_posts.ID = mt.post_id AND mt.meta_key = 'MN_publication_at')
        $join
        WHERE 1=1 AND
        $where AND
        mn_posts.post_type = 'post' AND mn_posts.post_status = 'publish'
        GROUP BY mn_posts.ID
        ORDER BY mt.meta_value {$args['order']}";

        $length = count($wpdb->get_col($querystr));
*/
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
