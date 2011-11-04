<?php
/**
 * Theme options
 * Credits: http://themeshaper.com/sample-theme-options/
 */
require_once ( get_template_directory() . '/theme-options.php' );

/**
 * Load theme options
 */

$blaskan_options = get_option('blaskan_options');

define( 'BLASKAN_SIDEBARS', $blaskan_options['sidebars'] );

if ( $blaskan_options['custom_sidebars_in_pages'] == 1 ) {
	define( 'BLASKAN_CUSTOM_SIDEBARS_IN_PAGES', TRUE );
} else {
	define( 'BLASKAN_CUSTOM_SIDEBARS_IN_PAGES', FALSE );
}

define( 'BLASKAN_SHOW_CONTENT_IN_LISTINGS', $blaskan_options['show_content_in_listings'] );
define( 'BLASKAN_HEADER_MESSAGE', $blaskan_options['header_message'] );
define( 'BLASKAN_FOOTER_MESSAGE', $blaskan_options['footer_message'] );
define( 'BLASKAN_SHOW_CREDITS', $blaskan_options['show_credits'] );

if ( empty( $blaskan_options['hide_site_title_header_message'] ) ) {
	$blaskan_options['hide_site_title_header_message'] = FALSE;
}

/**
 * Theme setup
 */
if ( ! function_exists( 'blaskan_setup' ) ):
function blaskan_setup() {
	global $blaskan_options;

	add_theme_support( 'automatic-feed-links' );
	
	add_theme_support( 'post-thumbnails' );
	
	load_theme_textdomain( 'blaskan', TEMPLATEPATH . '/languages' );
	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

  add_editor_style( 'editor-style.css' );
  
  add_custom_background();
  
	define( 'HEADER_TEXTCOLOR', '' );
	define( 'HEADER_IMAGE', '' );

	if ( empty ( $blaskan_options['header_image_height'] ) || !is_numeric( $blaskan_options['header_image_height'] ) ) {
		define( 'HEADER_IMAGE_HEIGHT', 160 );
	} else {
		define( 'HEADER_IMAGE_HEIGHT', $blaskan_options['header_image_height'] );
	}

	define( 'NO_HEADER_TEXT', true );
	
	add_custom_image_header( '', 'blaskan_custom_image_header_admin' );	
}
endif;
add_action( 'after_setup_theme', 'blaskan_setup' );

/**
 * Setup widths
 */
if ( ! function_exists( 'blaskan_setup_widths' ) ):
function blaskan_setup_widths() {
	if ( BLASKAN_SIDEBARS == 'one_sidebar') {
		set_post_thumbnail_size( 830, 9999, true );
		if ( ! isset( $content_width ) )
			$content_width = 830;
	} else {
		set_post_thumbnail_size( 540, 9999, true );
		if ( ! isset( $content_width ) )
			$content_width = 540;
	}

	if (
		( is_active_sidebar( 'primary-sidebar' ) && is_active_sidebar( 'secondary-sidebar' ) ) ||
		( is_active_sidebar( 'primary-page-sidebar' ) && is_active_sidebar( 'secondary-page-sidebar' ) )
	) {
		define( 'HEADER_IMAGE_WIDTH', 1120 );
	} elseif (
		( is_active_sidebar( 'primary-sidebar' ) || is_active_sidebar( 'secondary-sidebar' ) ) ||
		( is_active_sidebar( 'primary-page-sidebar' ) || is_active_sidebar( 'secondary-page-sidebar' ) )
	) {
		define( 'HEADER_IMAGE_WIDTH', 830 );
	} else {
		define( 'HEADER_IMAGE_WIDTH', 540 );
	}
}
endif;
add_action( 'after_setup_theme', 'blaskan_setup_widths' );

/**
 * Register menus
 */
if ( ! function_exists( 'blaskan_register_nav_menus' ) ):
function blaskan_register_nav_menus() {
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'blaskan' ),
		'footer' => __( 'Footer Navigation', 'blaskan' ),
	) );
}
endif;
add_action( 'after_setup_theme', 'blaskan_register_nav_menus' );

/**
 * JS init
 */
if ( ! function_exists( 'blaskan_js_init' ) ):
function blaskan_js_init() {
	if ( !is_admin() ) {
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/libs/modernizr.min.js' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mobile-boilerplate-helper', get_template_directory_uri() . '/js/mylibs/helper.js' );
		wp_enqueue_script( 'blaskan', get_template_directory_uri() . '/js/script.js' );
		wp_localize_script( 'blaskan', 'objectL10n', array( 'blaskan_navigation_title' => __( '- Navigation -', 'blaskan' ) ) );
	}
}
endif;
add_action( 'init', 'blaskan_js_init' );

/**
 * CSS init
 */
if ( ! function_exists( 'blaskan_css_init' ) ):
function blaskan_css_init() {
	if ( !is_admin() ) {
		wp_enqueue_style( 'blaskan-framework', get_template_directory_uri() . '/framework.css', array(), false, 'screen' );
		wp_enqueue_style( 'blaskan-style', get_template_directory_uri() . '/style.css', array(), false, 'screen' );
		wp_enqueue_style( 'blaskan-handheld', get_template_directory_uri() . '/css/handheld.css', array(), false, 'handheld' );
	}
}
endif;
add_action( 'init', 'blaskan_css_init' );

/**
 * Register widget areas. All are empty by default.
 */
if ( ! function_exists( 'blaskan_widgets_init' ) ):
function blaskan_widgets_init() {
	// Primary sidebar
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'blaskan' ),
		'id' => 'primary-sidebar',
		'description' => __( 'The primary sidebar', 'blaskan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="title">',
		'after_title' => '</h3>',
	) );
	
	if ( BLASKAN_SIDEBARS !== 'one_sidebar' ) {
		// Secondary sidebar
		register_sidebar( array(
			'name' => __( 'Secondary Widget Area', 'blaskan' ),
			'id' => 'secondary-sidebar',
			'description' => __( 'The secondary sidebar', 'blaskan' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="title">',
			'after_title' => '</h3>',
		) );
	}
	if ( BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === TRUE ) {
		// Primary page sidebar
		register_sidebar( array(
			'name' => __( 'Primary Page Widget Area', 'blaskan' ),
			'id' => 'primary-page-sidebar',
			'description' => __( 'The primary page sidebar', 'blaskan' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="title">',
			'after_title' => '</h3>',
		) );
	}
	if ( BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === TRUE && BLASKAN_SIDEBARS !== 'one_sidebar' ) {
		// Secondary page sidebar
		register_sidebar( array(
			'name' => __( 'Secondary Page Widget Area', 'blaskan' ),
			'id' => 'secondary-page-sidebar',
			'description' => __( 'The secondary page sidebar', 'blaskan' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="title">',
			'after_title' => '</h3>',
		) );
	}
	
	// Footer widgets
	register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'blaskan' ),
		'id' => 'footer-widget-area',
		'description' => __( 'The footer widget area', 'blaskan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="title">',
		'after_title' => '</h3>',
	) );
}
endif;
add_action( 'widgets_init', 'blaskan_widgets_init' );

/**
 * Head clean up
 */
function blaskan_head_cleanup() {
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init' , 'blaskan_head_cleanup' );

/**
 * Format the title
 */
if ( ! function_exists( 'blaskan_head_title' ) ):
function blaskan_head_title() {
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'blaskan' ), max( $paged, $page ) );
}
endif;

/**
 * Add content to wp_head()
 */
if ( ! function_exists( 'blaskan_head' ) ):
function blaskan_head() {
	echo '<link rel="pingback" href="'.get_bloginfo( 'pingback_url' ).'">'."\r";
	echo '<meta name="HandheldFriendly" content="True">'."\r";
	echo '<meta name="MobileOptimized" content="320">'."\r";
	echo '<meta name="viewport" content="width=device-width, target-densitydpi=160dpi, initial-scale=1">'."\r";
	echo '<meta http-equiv="cleartype" content="on">'."\r";
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">'."\r";
}
endif;
add_action( 'wp_head', 'blaskan_head' );

/**
 * Add body classes
 */
if ( ! function_exists( 'blaskan_body_class' ) ):
function blaskan_body_class($classes) {
	if ( get_theme_mod( 'background_image' ) || get_theme_mod( 'background_color' ) ) {
    $classes[] = 'background-image';
		if ( get_theme_mod( 'background_color' ) == 'FFFFFF' || get_theme_mod( 'background_color' ) == 'FFF' ) {
			$classes[] = 'background-white';
		}
  }
  
  if ( get_theme_mod( 'header_image' ) ) {
    $classes[] = 'header-image';
  }

  $nav = wp_nav_menu( array( 'theme_location' => 'primary', 'echo' => false, 'container' => false ) );
  $nav_links = substr_count( $nav, '<a' );
  $nav_lists = substr_count( $nav, '<ul' );
  if ( $nav_links == 0 ) {
  	$classes[] = 'no-menu';
  } elseif ( $nav_links < 9 && $nav_lists < 2 ) {
  	$classes[] = 'simple-menu';
  } else {
  	$classes[] = 'advanced-menu';
  }

	if ( BLASKAN_SHOW_CONTENT_IN_LISTINGS ) {
		$classes[] = 'show-content';
	} else {
		$classes[] = 'hide-content';
	}
	
	if ( BLASKAN_SIDEBARS == 'one_sidebar' ) {
		$classes[] = 'content-wide';
		
		if ( is_page() && BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === TRUE && is_active_sidebar( 'primary-page-sidebar' ) ) {
			$classes[] = 'sidebar';
			$classes[] = 'content-wide-sidebar';
		} elseif ( ( BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === FALSE || !is_page() ) && is_active_sidebar( 'primary-sidebar' ) ) {
			$classes[] = 'sidebar';
			$classes[] = 'content-wide-sidebar';
		} else {
			$classes[] = 'no-sidebars';
			$classes[] = 'content-wide-no-sidebars';
		}
	} else {
		if ( is_page() && BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === TRUE && ( is_active_sidebar( 'primary-page-sidebar' ) && is_active_sidebar( 'secondary-page-sidebar' ) ) ) {
			$classes[] = 'sidebars';
		} elseif ( is_page() && BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === TRUE && ( is_active_sidebar( 'primary-page-sidebar' ) || is_active_sidebar( 'secondary-page-sidebar' ) ) ) {
			$classes[] = 'sidebar';
		} elseif ( !is_page() && ( is_active_sidebar( 'primary-sidebar' ) && is_active_sidebar( 'secondary-sidebar' ) ) ) {
			$classes[] = 'sidebars';
		} elseif ( !is_page() && ( is_active_sidebar( 'primary-sidebar' ) || is_active_sidebar( 'secondary-sidebar' ) ) ) {
			$classes[] = 'sidebar';
		} elseif ( is_page() && BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === FALSE && ( is_active_sidebar( 'primary-sidebar' ) && is_active_sidebar( 'secondary-sidebar' ) ) ) {
			$classes[] = 'sidebars';
		} elseif ( is_page() && BLASKAN_CUSTOM_SIDEBARS_IN_PAGES === FALSE && ( is_active_sidebar( 'primary-sidebar' ) || is_active_sidebar( 'secondary-sidebar' ) ) ) {
			$classes[] = 'sidebar';
		} else {
			$classes[] = 'no-sidebars';
		}
	}
	
	if ( is_active_sidebar( 'footer-widget-area' ) ) {
		$classes[] = 'footer-widgets';
	}
	
	return $classes;
}
endif;
add_filter( 'body_class', 'blaskan_body_class' );

/**
 * Sets custom image header in admin
 */
function blaskan_custom_image_header_admin() {
?>
	<style type="text/css">
    #headimg {
      background-repeat: no-repeat;
      height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
      width: <?php echo HEADER_IMAGE_WIDTH; ?>px;  
    }
  </style>
<?php
}

/**
 * Blaskan top
 * Empty per default but could be used by child themes
 */
if ( ! function_exists( 'blaskan_top' ) ):
function blaskan_top() {
	return;
}
endif;

/**
 * Blaskan header structure
 */
if ( ! function_exists( 'blaskan_header_structure' ) ):
function blaskan_header_structure( $description = '' ) {
	global $blaskan_options;

	$output = '';

	if ( get_header_image() ):
		$output .= '<figure><a href="'.home_url( '/' ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home"><img src="'.get_header_image().'" alt="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'"></a></figure>';
	endif;

	if ( $blaskan_options['hide_site_title_header_message'] !== 1 ) {
		if ( is_front_page() ) {
			$header_element = 'h1';
		} else {
			$header_element = 'div';
		}
		$output .= '<'.$header_element.' id="site-name"><a href="'.home_url( '/' ).'" title="'. esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">'.get_bloginfo( 'name' ).'</a></'.$header_element.'>';
				
		$output .= blaskan_header_message( get_bloginfo( 'description' ) );	
	}
			
	$output .= blaskan_primary_nav();

	return $output;
}
endif;

/**
 * Checks if to display a header message
 */
if ( ! function_exists( 'blaskan_header_message' ) ):
function blaskan_header_message( $description = '' ) {
	if ( strlen( BLASKAN_HEADER_MESSAGE ) > 1 ) {
		return '<div id="header-message">' . nl2br( stripslashes( wp_filter_post_kses( BLASKAN_HEADER_MESSAGE ) ) ) . '</div>';
	} elseif ( !empty( $description ) ) {
		return '<div id="header-message">' . $description . '</div>';
	} else {
		return false;
	}
}
endif;

/**
 * Returns primary nav
 */
if ( ! function_exists( 'blaskan_primary_nav' ) ):
function blaskan_primary_nav() {
  $nav = wp_nav_menu( array( 'theme_location' => 'primary', 'echo' => false, 'container' => false ) );
  
  // Check nav for links
  if ( strpos( $nav, '<a' ) ) {
  	if ( strpos( $nav, 'div class="menu"' ) ) {
  		$nav_prepend = '';
  		$nav_append = '';
  	} else {
  		$nav_prepend = '<div class="menu">';
  		$nav_append = '</div>';
  		$nav = str_replace('class="menu"', '', $nav);
  	}

    return '<nav id="nav" role="navigation">' . $nav_prepend . $nav . $nav_append . '</nav>';
  } else {
    return; 
  }
}
endif;

/**
 * Blaskan footer structure
 */
if ( ! function_exists( 'blaskan_footer_structure' ) ):
function blaskan_footer_structure() {
  $output = '';

  $output .= get_sidebar( 'footer' );
	$output .= blaskan_footer_nav();
			
	if ( blaskan_footer_message() || blaskan_footer_credits() ) :
		$output .= '<div id="footer-info" role="contentinfo">';
		$output .= blaskan_footer_message();
		$output .= blaskan_footer_credits();
		$output .= '</div>';
	endif;

  return $output;
}
endif;

/**
 * Returns footer nav
 */
if ( ! function_exists( 'blaskan_footer_nav' ) ):
function blaskan_footer_nav() {
  $nav = wp_nav_menu( array( 'theme_location' => 'footer', 'depth' => 1, 'fallback_cb' => false, 'echo' => false, 'container' => false ) );

  // Check nav for links
  if ( strpos( $nav, '<a' ) ) {
    return '<nav id="footer-nav" role="navigation">' . $nav . '</nav>';
  } else {
    return; 
  }
}
endif;

/**
 * Add content to wp_footer()
 */
if ( ! function_exists( 'blaskan_footer' ) ):
function blaskan_footer() {
	// Unit PNG fix for IE 7
	echo '<!--[if lt IE 7]><script type="text/javascript" src="' . get_template_directory_uri() . '/js/libs/unitpngfix.js"></script><![endif]-->'."\r";

	// Selectivizr and Respond.js
	echo '<!--[if (lt IE 9) & (!IEMobile)]>'."\r";
	echo '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/libs/selectivizr.1.0.3b.js"></script>'."\r";
	echo '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/libs/respond.min.js"></script>'."\r";
	echo '<![endif]-->'."\r";
}
endif;
add_action( 'wp_footer', 'blaskan_footer' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 * Credits: http://wordpress.org/extend/themes/coraline
 */
function blaskan_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'blaskan_remove_recent_comments_style' );

/**
 * Root relative permalinks
 * Credits: http://www.456bereastreet.com/archive/201010/how_to_make_wordpress_urls_root_relative/
 */
function blaskan_root_relative_permalinks($input) {
    return preg_replace('!http(s)?://' . $_SERVER['SERVER_NAME'] . '/!', '/', $input);
}
add_filter( 'the_permalink', 'blaskan_root_relative_permalinks' );

/**
 * Remove empty span
 * Credits: http://nicolasgallagher.com/anatomy-of-an-html5-wordpress-theme/
 */
function blaskan_remove_empty_read_more_span($content) {
	return eregi_replace( "(<p><span id=\"more-[0-9]{1,}\"></span></p>)", "", $content );
}
add_filter( 'the_content', 'blaskan_remove_empty_read_more_span' );

/**
 * Remove more jump link
 * Credits: http://codex.wordpress.org/Customizing_the_Read_More#Link_Jumps_to_More_or_Top_of_Page
 */
function blaskan_remove_more_jump_link($link) { 
	$offset = strpos($link, '#more-');
	if ($offset) {
		$end = strpos($link, '"',$offset);
	}
	if ($end) {
		$link = substr_replace($link, '', $offset, $end-$offset);
	}
	return $link;
}
add_filter('the_content_more_link', 'blaskan_remove_more_jump_link');

/**
 * Use <figure> and <figcaption> in captions
 * Credits: http://wpengineer.com/917/filter-caption-shortcode-in-wordpress/
 */
if ( ! function_exists( 'blaskan_caption' ) ):
function blaskan_caption($attr, $content = null) {
	// Allow plugins/themes to override the default caption template.
	$output = apply_filters( 'img_caption_shortcode', '', $attr, $content );
	if ( $output != '' )
		return $output;

	extract( shortcode_atts ( array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr ) );

	if ( 1 > (int) $width || empty( $caption ) )
		return $content;

	if ( $id ) $id = 'id="' . $id . '" ';

	return '<figure ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . $width . 'px">'
	. do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $caption . '</figcaption></figure>';
}
endif;
add_shortcode( 'wp_caption', 'blaskan_caption' );
add_shortcode( 'caption', 'blaskan_caption' );

if ( ! function_exists( 'blaskan_comment' ) ) :
function blaskan_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<article>
			<header class="comment-header">
			  <?php echo blaskan_avatar( $comment ); ?>
  			<time><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s - %2$s', 'blaskan' ), get_comment_date(),  get_comment_time() ); ?></a></time>

  			<?php if ( $comment->user_id && !$comment->comment_author_url ): ?>
  			  <cite><a href="<?php echo get_author_posts_url( $comment->user_id ); ?>"><?php echo $comment->comment_author; ?></a></cite>
  			<?php else: ?>
  			  <?php printf( '<cite>%s</cite>', get_comment_author_link() ); ?>
  			<?php endif; ?>
  		</header>

  		<?php if ( $comment->comment_approved == '0' ) : ?>
  			<p class="moderation"><em><?php _e( 'Your comment is awaiting moderation.', 'blaskan' ); ?></em></p>
  		<?php endif; ?>

  		<?php comment_text(); ?>

  		<div class="reply">
  			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

  			<?php edit_comment_link( __( 'Edit', 'blaskan' ), ' ' ); ?>
  		</div><!-- .reply -->
		  </article>
	<?php
			break;
		case 'pingback'  :
	?>
	<li class="pingback">
		<time><?php printf( __( '%1$s - %2$s', 'blaskan' ), get_comment_date(),  get_comment_time() ); ?></time>
		<?php _e( 'Pingback:', 'blaskan' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('Edit', 'blaskan'), ' ' ); ?>
	<?php
			break;
		case 'trackback' :
	?>
	<li class="trackback">
		<time><?php printf( __( '%1$s - %2$s', 'blaskan' ), get_comment_date(),  get_comment_time() ); ?></time>
		<?php _e( 'Trackback:', 'blaskan' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('Edit', 'blaskan'), ' ' ); ?>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Display avatar
 */
if ( ! function_exists( 'blaskan_avatar' ) ):
function blaskan_avatar( $user ) {
	$avatar = get_avatar( $user, 40 );
	
	if ( !empty( $avatar ) ) {
		return '<figure>' . $avatar . '</figure>';
	} else {
		return;
	}
}
endif;

/**
 * Checks if to display a footer message
 */
if ( ! function_exists( 'blaskan_footer_message' ) ):
function blaskan_footer_message() {
	if ( strlen( BLASKAN_FOOTER_MESSAGE ) > 1 ) {
		return '<div id="footer-message">' . nl2br( stripslashes( wp_filter_post_kses( BLASKAN_FOOTER_MESSAGE ) ) ) . '</div>';
	} else {
		return false;
	}
}
endif;

/**
 * Checks if to display footer credits
 */
if ( ! function_exists( 'blaskan_footer_credits' ) ):
function blaskan_footer_credits() {
	if ( BLASKAN_SHOW_CREDITS ) {
		return '<div id="footer-credits">' . sprintf( __( 'Powered by <a href="%s">Blaskan</a> and <a href="%s">WordPress</a>.', 'blaskan' ), 'http://www.blaskan.net', 'http://www.wordpress.org' ) . '</div>';
	} else {
		return false;
	}
}
endif;