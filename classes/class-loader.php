<?php
class birdMashPlugloader {

    protected $actions;
    protected $filters;

    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }

    /*public function add_menu( $title, $options, $callback, $function, $dashicons) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
        add_menu_page( $title, $title, $options, $callback, $function, $dashicons);
    }*/

    public function add_filter( $hook, $component, $callback ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }

    private function add( $hooks, $hook, $component, $callback ) {

        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );

        return $hooks;

    }

    // ---- SHORTCODES

    public function run_shortcode($hook, $array){
        add_shortcode(''.$hook.'', $array);
    }

    // ---- CUSTOM POST TYPES

    

    public function register_post($type, $hook, $name, $supports, $singular , $dashicon, $menuorder, $rewrite){ //$menuposition
       
        register_post_type( ''.$type.'',
            array(
                    'labels' => array(
                        'name'               => __( ''.$name.'', ''.$hook.'' ),
                        'singular_name'      => __( ''.$singular.'', ''.$hook.'' ),
                        'all_items'          => __( 'All '.$name.'', ''.$hook.'' ),
                        'add_new'            => __( 'Add New', ''.$hook.'' ),
                        'add_new_item'       => __( 'Add New '.$name.'', ''.$hook.'' ),
                        'edit'               => __( 'Edit', ''.$hook.'' ),
                        'edit_item'          => __( 'Edit '.$name.'', ''.$hook.'' ),
                        'new_item'           => __( 'New '.$name.'', ''.$hook.'' ),
                        'view'               => __( 'View '.$name.'', ''.$hook.'' ),
                        'view_item'          => __( 'View '.$name.'', ''.$hook.'' ),
                        'search_items'       => __( 'Search '.$name.'', ''.$hook.'' ),
                        'not_found'          => __( 'No '.$name.' found', ''.$hook.'' ),
                        'not_found_in_trash' => __( 'No '.$name.' found in trash', ''.$hook.'' ),
                        'parent'             => __( 'Parent '.$name.'', ''.$hook.'' )
                    ),
                    'description'         => __( 'This is where you can add new '.$name.'.', ''.$hook.'' ),
                    'public'              => true,
                    'show_ui'             => true,
                    'map_meta_cap'        => true,
                    'menu_icon'           => ''.$dashicon.'',
                    'publicly_queryable'  => true,
                    'exclude_from_search' => false,
                    'hierarchical'        => false, 
                    'query_var'           => true,
                    'supports'            => $supports,
                    'has_archive'         => true,
                    'show_in_nav_menus'   => true,
                    'menu_position'       => $menuorder,
                    'taxonomies'          => array(''.$hook.'', 'post_tag'),
                    'rewrite'             => array(
                                            'slug'                       => ''.$rewrite.'',
                                            'with_front'                 => true,
                                            'hierarchical'               => true,
                                            )
                    
                )
            );
    flush_rewrite_rules();
    }

    public function register_scripts($name, $type){
       
        if ($type == 'style'):
            wp_enqueue_style ($name.'_style', plugin_dir_url( __FILE__ ). ''.$name.'.css' );
        endif;
        
    }

    // ---- CUSTOM TAXONOMIES

    public function register_tax($type, $hook, $name, $singular, $dashicon, $rewrite, $cptype) {
    
        $labels = array(
            'name'                       => _x( ''.$name.'', 'text_domain' ),
            'singular_name'              => _x( ''.$singular.'', 'text_domain' ),
            'menu_name'                  => __( ''.$name.'', 'text_domain' ),
            'all_items'                  => __( 'All Items', 'text_domain' ),
            'parent_item'                => __( 'Parent Item', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
            'new_item_name'              => __( 'New Item Name', 'text_domain' ),
            'add_new_item'               => __( 'Add New Item', 'text_domain' ),
            'edit_item'                  => __( 'Edit Item', 'text_domain' ),
            'update_item'                => __( 'Update Item', 'text_domain' ),
            'view_item'                  => __( 'View Item', 'text_domain' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
            'popular_items'              => __( 'Popular Items', 'text_domain' ),
            'search_items'               => __( 'Search Items', 'text_domain' ),
            'not_found'                  => __( 'Not Found', 'text_domain' ),
            'no_terms'                   => __( 'No items', 'text_domain' ),
            'items_list'                 => __( 'Items list', 'text_domain' ),
            'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( ''.$type.'', ''.$cptype.'', $args );
        
    }

    // ---- RUN FUNCTIONS

    public function run_save($postid, $field, $meta){
        if($postid){
                $save_url    = sanitize_text_field( $field );
                update_post_meta( $postid, ''.$meta.'', $save_url );
        } else { // for metaboxes saved external from custom post type
                $post = get_post();
            if($post){
                $save    = sanitize_text_field( $field );
                update_post_meta( $post->ID, ''.$meta.'', $save);
            }
        } 
    }

    public function run_meta_box($postid, $field, $meta, $type, $outputText){
           $metVal   = '';
           $selected = '';


            if($postid):
             $metVal   = get_post_meta($postid, ''.$meta.'', true ); 
            endif;
            //if($postType != ''):
               // $args    = array('post_type' => ''.$postType.'');
               // $value   = get_posts($args);   
           // endif;


            if ($type == 'links'){
                 $output = $outputText;
                 echo $metval;
                 $output .= '<br/><label>URL</label>';
                 $output .= '<input type="text" value="'.$metVal.'" name="bs_posts_links_one" placeholder="http://" class="input full" style="width:100%; padding:10px;">';
                
             }

             if ($type == 'text'){
                 $output = $outputText;
                 $output .= '<br/><label>Link Text</label>';
                 $output .= '<input type="text" value="'.$metVal.'" name="bs_posts_links_text" placeholder="" class="input full" style="width:100%; padding:10px;">';
                
             }

              if ($type == 'order'){
                 $output = $outputText;
                 $output .= '<br/><label>Order your Links</label>';
                 $output .= '<input type="text" value="'.$metVal.'" name="bs_posts_links_order" placeholder="" class="input full" style="width:100%; padding:10px;">';
                
             }

             if ($type == 'desc'){
                 $output = $outputText;
                 $output .= '<br/><label>Link Description</label>';
                 $output .= '<input type="text" value="'.$metVal.'" name="bs_posts_links_desc" placeholder="" class="input full" style="width:100%; padding:10px;">';
                
             }

             

            

            
            echo  $output;
           // echo 'hello';
    }


    public function run() {

        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }

    }

}
?>