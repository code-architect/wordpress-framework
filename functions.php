<?php 
 /**
  * functions.php
  * 
  * The themes functions.
  */

 /**
  * ----------------------------------------------------------------------------------------------------------
  * 1.0 - Define constants.
  * ----------------------------------------------------------------------------------------------------------
  */
 define( 'THEMEROOT', get_stylesheet_directory_uri() );
 define( 'IMAGES', THEMEROOT .'/images' );
 define( 'SCRIPTS', THEMEROOT .'/js' );
 define( 'FRAMEWORK', get_template_directory() . '/framework' );


 /**
  * ----------------------------------------------------------------------------------------------------------
  * 2.0 - Load Framework.
  * ----------------------------------------------------------------------------------------------------------
  */
 require_once(FRAMEWORK .'/init.php');

/**
 * ----------------------------------------------------------------------------------------------------------
 * 3.0 - Set up the content width value based on the theme's design.
 * ----------------------------------------------------------------------------------------------------------
 */
 if( !isset( $content_width ) ){
     $content_width = 800;
 }


/**
 * ----------------------------------------------------------------------------------------------------------
 * 4.0 - Set up theme default and register various supported features and functions.
 * ----------------------------------------------------------------------------------------------------------
 */
if(!function_exists('ca_setup')){
    function ca_setup(){
        //make the theme available for translation
        $lang_dir = THEMEROOT . '/languages';
        load_theme_textdomain( 'architect', $lang_dir );

        // Post formats
        add_theme_support( 'post-formats',
            array(
                'gallery',
                'link',
                'image',
                'quote',
                'video',
                'audio',
                'aside'
            )
        );

        // Support for automatic feed links
        add_theme_support( 'automatic-feed-links' );

        // Support for post thumbnails
        add_theme_support( 'post-thumbnails' );

        // TODO: Add support for custom header , background
        // TODO: see Theme_Features in wordpress.org

        // Register Nav menus
        register_nav_menus(
            array(
                'main-menu' => __('Main Menu', 'architect')
            )
        );

        // Support for theme markup
        add_theme_support( 'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'widgets'
            )
        );
    }

    add_action('after_setup_theme', 'ca_setup');
}


/**
 * ----------------------------------------------------------------------------------------------------------
 * 5.0 - Display meta information for specific post
 * ----------------------------------------------------------------------------------------------------------
 */
if( !function_exists('ca_post_meta') ){
    function ca_post_meta(){
        echo '<ul class="list-inline entry-meta">';

        // check if the post type is post, not a page
        if(get_post_type() === 'post'){
            // If there is a sticky post, mark it.
            if( is_sticky() ){
                echo '<li class="meta-featured-post"><i class="fa fa-thumb-tack"></i>' . __('Sticky', 'architect') .'</li>';
            }
            // Get the post author details.
            printf(
                '<li class="meta-author"><a href="%1$s" rel="author">%2$s</a></li>',
                esc_url(get_author_posts_url( get_the_author_meta('ID') )),
                get_the_author()
            );
            // The date
            echo '<li class="meta-date">'. get_the_date() .'</li>';

            // The categories
            $cat_list = get_the_category_list( ', ' );
            if($cat_list){
                echo '<li class="meta_categories">'. $cat_list .'</li>';
            }

            // The tags
            $tag_list = get_the_tag_list( '',', ' );
            if($tag_list){
                echo '<li class="meta_tags">'. $tag_list .'</li>';
            }

            // Comments Link
            if( comments_open() ) {
                echo '<li>';
                echo '<span class="meta-reply">';
                comments_popup_link(__('Leave a comment', 'architect'),
                                    __('One comment so far', 'architect'),
                                    __('View all % comments', 'architect')
                );
                echo '</span>';
                echo '</li>';
            }

            // Edit Link
            if( is_user_logged_in() ){
                echo '<li>';
                edit_post_link( __('Edit', 'architect'), '<span class="meta-edit">','</span>' );
                echo '</li>';
            }
        }
    }
}


/**
 * ----------------------------------------------------------------------------------------------------------
 * 6.0 - Display navigation to the next/previous posts.
 * ----------------------------------------------------------------------------------------------------------
 */
if(!function_exists('ca_paging_nav')){
    function ca_paging_nav(){ ?>

        <ul>
            <!-- Previous post link -->
            <?php if(get_previous_posts_link()){ ?>
            <li class="next">
                <?php previous_posts_link( __('Newer Post &rarr;', 'architect') ); ?>
            </li>
            <?php } ?>

            <!-- Next post link -->
            <?php if(get_next_posts_link()){ ?>
                <li class="previous">
                    <?php next_posts_link( __('&larr; Older Post', 'architect') ); ?>
                </li>
            <?php } ?>
        </ul>
<?php    }
}


/**
 * ----------------------------------------------------------------------------------------------------------
 * 7.0 - Register The widget Area / Sidebar
 * ----------------------------------------------------------------------------------------------------------
 */

 if(!function_exists( 'ca_widget_init' )){
     function ca_widget_init(){
         if(function_exists( 'register_sidebar' )){

             register_sidebar(
                 array(
                     'name'          => __( 'Main Widget Area', 'architect' ),
                     'id'            => 'sidebar-1',
                     'description'   => __('The widget area, appears on pages and posts', 'architect'),
                     'before_widget' => '<div id="%1$s" class="widget %2$s">',
                     'after_widget'  => '</li>',
                     'before_title'  => '<h5 class="widgettitle">',
                     'after_title'   => '</h5>'
                 )
             );

             register_sidebar(
                 array(
                     'name'          => __( 'Footern Widget Area', 'architect' ),
                     'id'            => 'sidebar-2',
                     'description'   => __('This appears on the footer', 'architect'),
                     'before_widget' => '<div id="%1$s" class="widget col-sm-3 %2$s">',
                     'after_widget'  => '</li>',
                     'before_title'  => '<h5 class="widgettitle">',
                     'after_title'   => '</h5>'
                 )
             );

         }
     }

     add_action('widgets_init', 'ca_widget_init');
 }


/**
 * ----------------------------------------------------------------------------------------------------------
 * 8.0 - Function that's validates a length
 * ----------------------------------------------------------------------------------------------------------
 */
if( !function_exists( 'ca_length_check' ) ){
    function ca_length_check( $fieldValue, $minLength ){
        // Remove trailing and leading whitespace
        return ( strlen( trim( $fieldValue ) ) > $minLength );
    }
}

/**
 * ----------------------------------------------------------------------------------------------------------
 * 9.0 - Include css in the page for header.
 * ----------------------------------------------------------------------------------------------------------
 */
if(!function_exists('ca_wp_head_style')){
    function ca_wp_head_style(){
        // Get the logo
        $logo = IMAGES.'/iconified/logo.jpg';

        $logo_size = getimagesize($logo);
        ?>
        <!-- Inline Css for logo -->
        <style type="text/css">
            .site-log a{
                background: transparent url( <?php echo $logo; ?> ) 0 0 no-repeat;
                width: <?php echo $logo_size[0] ?>px;
                height: <?php echo $logo_size[1] ?>px;
                display: inline-block;
            }
        </style>
        <?php

    }

    add_action('wp_head', 'ca_wp_head_style');
}

/**
 * ----------------------------------------------------------------------------------------------------------
 * 10.0 - Add all the css and javascript for this theme if needed
 * ----------------------------------------------------------------------------------------------------------
 */
if(!function_exists('ca_add_scripts_and_styles')){
    function ca_add_scripts_and_styles(){
        // Adds Support for pages with threaded comments
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }

        // Register scripts
        wp_register_script('bootstrap.min.js', SCRIPTS.'/bootstrap.min.js', array('jquery'), false, true);
        wp_register_script('custom-js', SCRIPTS.'/custom.js', array('jquery'), false, true);

        // Load scripts
        wp_enqueue_script('bootstrap.min.js');
        wp_enqueue_script('custom-js');

        // Load the stylesheets
        wp_enqueue_style('bootstrap-style', THEMEROOT .'/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap-responsive-style', THEMEROOT .'/css/bootstrap-responsive.min.css');
    }

    add_action('wp_enqueue_scripts', 'ca_add_scripts_and_styles');
}