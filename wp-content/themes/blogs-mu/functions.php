<?php
define('TEMPLATE_DOMAIN', 'blogs-mu');
define('TD', 'blogs-mu');
define('EDITOR_BG_ENABLE', 'no'); //should be yes or no...
define('USE_NEW_COMMENT_FORM','no');
define('USE_CUSTOM_BP_COMPONENT','yes');
define('HIDE_REDUNDANT_OPTIONS_CUSTOMIZER','no');
////////////////////////////////////////////////////////////////////////////////
// Load text domain
////////////////////////////////////////////////////////////////////////////////
load_theme_textdomain( TEMPLATE_DOMAIN, TEMPLATEPATH . '/languages/' );
///////////////////////////////////////////////////////////////////////////////
// Load Theme Styles and Javascripts
///////////////////////////////////////////////////////////////////////////////
/*---------------------------load styles--------------------------------------*/
if ( ! function_exists( 'devtheme_load_styles' ) ) :
function devtheme_load_styles() {
global $theme_version, $bp_existed;
wp_enqueue_style( 'dev-base', get_template_directory_uri() . '/_inc/css/base.css', array(), $theme_version );

if($bp_existed):
wp_enqueue_style( 'dev-bp-base', get_template_directory_uri() . '/_inc/css/bp-default.css', array( 'dev-base' ), $theme_version );
wp_enqueue_style( 'dev-bp-css', get_template_directory_uri() . '/_inc/css/bp-css.css', array( 'dev-base' ), $theme_version );
wp_enqueue_style( 'dev-bp-adminbar', get_template_directory_uri() . '/_inc/css/adminbar.css', array( 'dev-base' ), $theme_version );
endif;

if( is_rtl() ):
wp_enqueue_style( 'dev-rtl', get_template_directory_uri() . '/_inc/css/rtl.css', array( 'dev-base' ), $theme_version );
endif;

if( file_exists( TEMPLATEPATH . '/_inc/css/custom.css' ) ):
wp_enqueue_style( 'dev-custom', get_template_directory_uri() . '/_inc/css/custom.css', array( 'dev-base' ), $theme_version );
endif;

// If the current theme is a child theme, enqueue its stylesheet
if ( is_child_theme() && 'blogs-mu' == get_template() ) {
if( file_exists( STYLESHEETPATH . '/_inc/css/child-style.css' ) ):
wp_enqueue_style( 'dev-base-child', get_stylesheet_directory_uri() . '/_inc/css/child-style.css', array( 'dev-base' ), $theme_version );
endif;
}
?>

<?php
}
endif;
add_action( 'wp_enqueue_scripts', 'devtheme_load_styles' );

/*---------------------------load js scripts--------------------------------------*/
if ( ! function_exists( 'devtheme_load_scripts' ) ) :
function devtheme_load_scripts() {
global $theme_version, $bp_existed;
$feat_style = get_option('tn_blogsmu_featured_blk_option');
wp_enqueue_script("jquery");
wp_enqueue_script('dev-dropmenu-js', get_template_directory_uri() . '/_inc/js/dropmenu.js', array( 'jquery' ), $theme_version );
wp_enqueue_script('modernizr', get_template_directory_uri() . '/_inc/js/modernizr.js', array("jquery"), $theme_version );
if(($feat_style == 'Featured Slider Posts') || ($feat_style == 'Featured Slider Categories') || ($feat_style == 'BP Album Rotate')):
wp_enqueue_script('dev-nivo-slider', get_template_directory_uri() . '/_inc/js/jquery.nivo.slider.js', array("jquery"), $theme_version );
endif;
if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) wp_enqueue_script( 'comment-reply' );
}
endif;
add_action( 'wp_enqueue_scripts', 'devtheme_load_scripts' );


///////////////////////////////////////////////////////////////////////////
// Update Notifications Notice
///////////////////////////////////////////////////////////////////////////
if ( !function_exists( 'wdp_un_check' ) ) {
add_action( 'admin_notices', 'wdp_un_check', 5 );
add_action( 'network_admin_notices', 'wdp_un_check', 5 );
function wdp_un_check() {
if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
}
}

////////////////////////////////////////////////////////////////////
// Browser Detect
///////////////////////////////////////////////////////////////////
add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
if($is_lynx) $classes[] = 'lynx';
elseif($is_gecko) $classes[] = 'gecko';
elseif($is_opera) $classes[] = 'opera';
elseif($is_NS4) $classes[] = 'ns4';
elseif($is_safari) $classes[] = 'safari';
elseif($is_chrome) $classes[] = 'chrome';
elseif($is_IE) $classes[] = 'ie';
else $classes[] = 'unknown';
if($is_iphone) $classes[] = 'iphone';
return $classes;
}

////////////////////////////////////////////////////////////////////
// IE CSS Hacks
///////////////////////////////////////////////////////////////////
function wp_add_css_ie_tweak() {
global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
if( $is_IE ) { ?>
<?php print "<style type='text/css'>"; ?>
.picture-activity-thumb { width: 100px; height: 100px; display: block; }
img.feat-thumb { width: auto; max-width: 600px; height:auto !important; }
<?php print "</style>"; ?>
<?php }
}
add_action('wp_print_styles','wp_add_css_ie_tweak');

////////////////////////////////////////////////////////////////////////////////
// Get Featured Post Image
////////////////////////////////////////////////////////////////////////////////
function wp_custom_post_thumbnail($the_post_id='', $with_wrap='', $wrap_w='', $wrap_h='', $title='', $fetch_size='',$fetch_w='', $fetch_h='',$alt_class='') {
// do global first
global $wpdb, $post, $posts;
$detect_post_id = $the_post_id;
if($with_wrap == 'yes') {
$before_wrap = "<div style='width: $wrap_w; height: $wrap_h; overflow: hidden;'>";
$after_wrap = "</div>";
}
ob_start();
?>

<a href="<?php echo the_permalink(); ?>">

<?php if(get_the_post_thumbnail() != "") : ?>

<?php
$image_id = get_post_thumbnail_id();
if($fetch_size == 'original') {
$image_url = wp_get_attachment_image_src($image_id,'large');
} else {
$image_url = wp_get_attachment_image_src($image_id,array($fetch_w,$fetch_h));
}
$image_url = $image_url[0];
?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-thumbnail <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $image_url; ?>">
<?php echo $after_wrap; ?>


<?php else: ?>

<?php
$images = get_children(array(
'post_parent' => $the_post_id,
'post_type' => 'attachment',
'numberposts' => 1,
'post_mime_type' => 'image')); ?>
<?php if ($images) : ?>
<?php foreach($images as $image) :
if($fetch_size == 'original') {
$attachment= wp_get_attachment_image_src($image->ID,'large');
} else {
$attachment= wp_get_attachment_image_src($image->ID, array($fetch_w,$fetch_h));
} ?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-attachment <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $attachment[0]; ?>">
<?php echo $after_wrap; ?>
<?php endforeach; ?>


<?php elseif( !$images ): ?>

<?php
$get_post_attachment = $wpdb->get_var("SELECT guid FROM " . $wpdb->prefix . "posts WHERE post_parent = '" . $detect_post_id . "' AND post_type = 'attachment' ORDER BY menu_order ASC LIMIT 1");
// If images exist for this page

if($get_post_attachment) {  ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-wp <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $get_post_attachment; ?>">

<?php } else { ?>

<?php
$first_img = '';
ob_start();
ob_end_clean();

$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
$first_img = (isset($matches[1][0])) ? $matches[1][0] : false; ?>

<?php if($first_img) { ?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-regex <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $first_img; ?>">
<?php echo $after_wrap; ?>
<?php } else {
    //no image found anywhere, so do not output the a tag
    ob_end_clean(); //discard the output
    return;
} ?>
<?php } ?>

<?php endif; ?>

<?php endif; ?>

</a>

<?php
echo ob_get_clean();
}

///////////////////////////////////////////////////////////////////////////
// Custom footer code
///////////////////////////////////////////////////////////////////////////
if ( ! function_exists( 'wp_network_footer' ) ) :
function wp_network_footer() {
global $blog_id, $current_site, $current_blog;
if( is_multisite() ) {
$current_site = get_current_site();
$current_network_site = get_current_site_name(get_current_site());
if ( function_exists( 'bp_exists' ) ) {
$current_network_domain = bp_get_root_domain();
} else {
if(function_exists('network_home_url')) {
$current_network_domain = network_home_url();
} else {
$current_network_domain = 'http://' . $current_site->domain . $current_site->path;
}
}
if( BLOG_ID_CURRENT_SITE != $current_blog->blog_id && ( defined('BP_ROOT_BLOG') && BP_ROOT_BLOG != $current_blog->blog_id ) ) { ?>
<?php _e('Hosted by', TEMPLATE_DOMAIN); ?> <a target="_blank" title="<?php echo $current_network_site->site_name; ?>" href="<?php echo $current_network_domain; ?>"><?php echo $current_network_site->site_name; ?></a>
<?php } ?>
<?php
}
}
endif;





////////////////////////////////////////////////////////////////////////////////
// new code for wp 3.0+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
    add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 200, 150, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 600, 9999 ); // Permalink thumbnail size

    // Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

    if(EDITOR_BG_ENABLE == 'yes') {
    // This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
    // This theme allows users to set a custom background
	add_theme_support( 'custom-background' );
    }

    add_theme_support( 'menus' ); // new nav menus for wp 3.0
    if ( ! isset( $content_width ) ) $content_width = 500;
    }


if ( function_exists( 'register_nav_menus' ) ) {
// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
'logged-in-nav' => __( 'Logged in Navigation',TEMPLATE_DOMAIN ),
'not-logged-in-nav' => __( 'Not Logged in Navigation',TEMPLATE_DOMAIN )
) );




///////////////////////////////////////////////////////////////////////////////
// custom walker nav for mobile navigation
///////////////////////////////////////////////////////////////////////////////
class description_custom_walker extends Walker_Nav_Menu {
function start_el(&$output, $item, $depth, $args) {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
           $class_names = $value = '';
           $classes = empty( $item->classes ) ? array() : (array) $item->classes;
           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';
           $output .= $indent . '';
           $prepend = '';
           $append = '';
//$description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

           if($depth != 0) { $description = $append = $prepend = ""; }
           $item_output = $args->before;
           $item_output .= "<option value='" . $item->url . "'>" . $item->title . "</option>";
           $item_output .= $args->after;

           $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
           }
}



function bp_wp_custom_mobile_nav_menu($get_custom_location='', $get_default_menu=''){
$options = array('walker' => new description_custom_walker(), 'theme_location' => "$get_custom_location", 'menu_id' => '', 'echo' => false, 'container' => false, 'container_id' => '', 'fallback_cb' => "$get_default_menu");
$menu = wp_nav_menu($options);
$menu_list = preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
return $menu_list;
}

///////////////////////////////////////////////////////////////////////////////
// remove open ul to fit the custom bp navigation.php
///////////////////////////////////////////////////////////////////////////////
function bp_wp_custom_nav_menu($get_custom_location='', $get_default_menu=''){
$options = array('theme_location' => "$get_custom_location", 'menu_id' => '', 'echo' => false, 'container' => false, 'container_id' => '', 'fallback_cb' => "$get_default_menu");
$menu = wp_nav_menu($options);
$menu_list = preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
return $menu_list;
}

function revert_wp_menu_page($args) { //revert back to normal if in wp 3.0 and menu not set
global $bp_existed;
?>
<?php
	global $bp;
	$pages_args = array(
		'depth'      => 0,
		'echo'       => false,
		'exclude'    => '',
		'title_li'   => ''
	);
	$menu = wp_page_menu( $pages_args );
	$menu = str_replace( array( '<div class="menu"><ul>', '</ul></div>' ), array( '', '' ), $menu );
	echo $menu;
    if($bp_existed):
    do_action( 'bp_nav_items' );
    endif;
 ?>
<?php }


if ( !function_exists( 'wp_dtheme_page_menu_args' ) ) :
function wp_dtheme_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wp_dtheme_page_menu_args' );
endif;

function revert_wp_mobile_menu_page() {
global $wpdb;
$qpage = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "posts WHERE post_type='page' AND post_status='publish' ORDER by ID");
foreach ($qpage as $ipage ) {
echo "<option value='" . get_permalink( $ipage->ID ) . "'>" . $ipage->post_title . "</option>";
}
}


function revert_wp_menu_cat() { //revert back to normal if in wp 3.0 and menu not set ?>
<?php wp_list_categories('orderby=id&show_count=0&use_desc_for_title=0&title_li='); ?>
<?php }
}
// end register_nav_menus check


function get_mobile_navigation($type='', $nav_name='') {
   $id = "{$type}-dropdown";
  $js =<<<SCRIPT
<script type="text/javascript">
 jQuery(document).ready(function($){
  $("select#{$id}").change(function(){
    window.location.href = $(this).val();
  });
 });
</script>
SCRIPT;
    echo $js;
  echo "<select name=\"{$id}\" id=\"{$id}\">";
  echo "<option>Where to?</option>"; ?>
<?php echo bp_wp_custom_mobile_nav_menu($get_custom_location=$nav_name, $get_default_menu='revert_wp_mobile_menu_page'); ?>
<?php echo "</select>"; }


////////////////////////////////////////////////////////////////////////////////
// wp 2.7 wp_list_comment
////////////////////////////////////////////////////////////////////////////////

function list_comments($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<li class="box-com" id="comment-<?php comment_ID() ?>">
<div <?php comment_class('blog-box'); ?>>
<div class="blog-avatar">
<a href="<?php echo get_comment_author_url() ?>" rel="nofollow">
<?php if(function_exists('bp_core_fetch_avatar')) { ?>
<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => 48, 'height' => 48, 'email' => $comment->comment_author_email ) ); ?>
<?php } else { ?>
<?php if (function_exists('get_avatar')) { echo get_avatar( $comment , 48 ); } ?>
<?php } ?>
</a>
</div>
<div class="blog-user"><?php comment_author_link(); ?>&nbsp;<span class="blog-date"><?php comment_date('F jS Y') ?> <?php _e("at",TEMPLATE_DOMAIN); ?> <?php comment_time() ?>&nbsp;&nbsp;&nbsp;<a href="#comment-<?php comment_ID() ?>">#</a>&nbsp;&nbsp;<?php edit_comment_link(__("edit",TEMPLATE_DOMAIN), '',''); ?>&nbsp;&nbsp;&nbsp;<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
</div>
</div>
<div class="blog-text">
<?php if ($comment->comment_approved == '0') : ?>
<strong><?php _e("Your Comment Is Under Moderation ",TEMPLATE_DOMAIN); ?></strong>
<?php else: ?>
<?php comment_text(); ?>
<?php endif; ?>
</div>
<?php
}

///////////////////////////////////////////////////////////////////////////////
// custom recent comments
//////////////////////////////////////////////////////////////////////////////
function get_custom_recent_comments() {
global $wpdb;
$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
comment_post_ID, comment_author, comment_date_gmt, comment_approved,
comment_type,comment_author_url,
SUBSTRING(comment_content,1,38) AS com_excerpt
FROM $wpdb->comments
LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
$wpdb->posts.ID)
WHERE comment_approved = '1' AND comment_type = '' AND
post_password = ''
ORDER BY comment_date_gmt DESC
LIMIT 10";
$comments = $wpdb->get_results($sql);
$output = ( isset($pre_HTML) ) ? $pre_HTML : '';
$output .= "\n<ul>";
foreach ($comments as $comment) {
$output .= "\n<li>".strip_tags($comment->comment_author)
.":&nbsp;&nbsp;" . "<a href=\"" . get_permalink($comment->ID) .
"#comment-" . $comment->comment_ID . "\" title=\"on " .
$comment->post_title . "\">" . strip_tags($comment->com_excerpt)
."...</a></li>";
}
$output .= "\n</ul>";
$output .= ( isset($post_HTML) ) ? $post_HTML : '';
echo $output;
}

////////////////////////////////////////////////////////////////////////////////
// wp 2.7 wp_list_ping
////////////////////////////////////////////////////////////////////////////////

function list_pings($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php }
add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
global $id;
$comments_by_split = get_comments('post_id=' . $id);
$comments_by_type = &separate_comments($comments_by_split);
return count($comments_by_type['comment']);
}

////////////////////////////////////////////////////////////////////////////////
// hide members-login tab
////////////////////////////////////////////////////////////////////////////////
function hide_members_login_tab() {
global $wpdb;
$get_page_login = $wpdb->get_var("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_value = 'page-template-custom-login.php' AND meta_key = '_wp_page_template'");
?>
<?php echo "<style type=\"text/css\">"; ?>
.page-item-<?php echo $get_page_login; ?> { display: none !important; }
<?php echo "</style>";
}
add_action('wp_head', 'hide_members_login_tab');

////////////////////////////////////////////////////////////////////////////////
// get members-login slug
////////////////////////////////////////////////////////////////////////////////
function get_members_login_slug(){
global $wpdb;
$get_page_login = $wpdb->get_var("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_value = 'page-template-custom-login.php' AND meta_key = '_wp_page_template'");
$get_page_login_slug = $wpdb->get_var("SELECT post_name FROM " . $wpdb->prefix . "posts WHERE ID = '" . $get_page_login . "'");
if(!$get_page_login_slug) {
echo 'you-need-to-create-login-page-in-page';
} else {
echo $get_page_login_slug;
}
}

////////////////////////////////////////////////////////////////////////////////
// prevent Script Insertion Vulnerability
////////////////////////////////////////////////////////////////////////////////
function clean_script_process($var) {
if( preg_match("'[<]script.*?/script[>]'is", $var) ) {
return true;
} else {
return false;
}
}
function clean_sql_process($var) {
if(preg_match("/('|`|union|select|schema|information|users|admin)/", $var)) {
return true;
}  else {
return false;
}
}


////////////////////////////////////////////////////////////////////////////////
// includes
////////////////////////////////////////////////////////////////////////////////
include( TEMPLATEPATH . '/_inc/functions/conditional-functions.php' );
include( TEMPLATEPATH . '/_inc/functions/widgets-functions.php' );
include( TEMPLATEPATH . '/_inc/functions/options-functions.php' );
include( TEMPLATEPATH . '/_inc/functions/customizer-functions.php' );

////////////////////////////////////////////////////////////////////////////////
// includes custom-function - rename _inc/functions/custom-functions-sample.php
////////////////////////////////////////////////////////////////////////////////
//include( TEMPLATEPATH . '/_inc/functions/custom-functions.php' );
//load buddypress default functions//
if($bp_existed == 'true') {

function add_member_custom_extended_profile() {
$data_name = bp_get_member_profile_data( 'field=Name' );
$data_jobs = bp_get_member_profile_data( 'field=Jobs' );
echo 'Name' . ' - ' . $data_name . '<br />';
echo 'Jobs' . ' - ' . $data_jobs . '<br />';
}
 //add_action('bp_directory_members_item',  'add_member_custom_extended_profile');

include( TEMPLATEPATH . '/bp-functions.php' );


if( USE_CUSTOM_BP_COMPONENT == 'yes' ):
include( TEMPLATEPATH . '/_inc/functions/bp-component-functions.php' );
endif;

///////////////////////////////////////////////////////////////////////////////////
/// get username by id - buddypress only
///////////////////////////////////////////////////////////////////////////////////
function bp_get_username_by_id($id) {
global $wpdb, $bp;
$bp_get_the_username = $wpdb->get_var("SELECT user_nicename FROM " . $wpdb->base_prefix . "users WHERE ID = '" . $id . "'");
return $bp_get_the_username;
}

///////////////////////////////////////////////////////////////////////
/// check if is friend
///////////////////////////////////////////////////////////////////////
function bp_displayed_user_is_friend() {
global $bp;
$friend_privacy_enable = get_option('tn_blogsmu_friend_privacy_status');
$friend_privacy_redirect = get_option('tn_blogsmu_friend_privacy_redirect');

if($friend_privacy_enable == "enable") {
if ( bp_is_user_activity() || bp_is_user_profile() || bp_is_user() ) {
if ( ('is_friend' != BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $bp->displayed_user->id )) && (bp_loggedin_user_id() != bp_displayed_user_id()) ) {
if ( !is_super_admin( bp_loggedin_user_id() ) ) {
if($friend_privacy_redirect == '') {
bp_core_redirect( $bp->root_domain );
} else {
bp_core_redirect( $friend_privacy_redirect );
}
}
}
}
} //enable
}
add_filter('get_header','bp_displayed_user_is_friend',3);

///////////////////////////////////////////////////////////////
// check privacy
////////////////////////////////////////////////////////////////
function check_if_privacy_on() {
global $bp;
$privacy_enable = get_option('tn_blogsmu_privacy_status');
$privacy_redirect = get_option('tn_blogsmu_privacy_redirect');
if($privacy_enable == "enable") {
if ( bp_is_profile_component() || bp_is_activity_component() || bp_is_page( bp_get_root_slug( 'members' ) ) || bp_is_user() ) {
if(!is_user_logged_in()) {
if($privacy_redirect == '') {
bp_core_redirect( $bp->root_domain . '/' . bp_get_root_slug( 'register' ) );
} else {
bp_core_redirect( $privacy_redirect );
}
}
}
} //off
}
add_filter('get_header','check_if_privacy_on',1);

function check_if_create_group_limit() {
global $bp;
$create_limit_enable = get_option('tn_blogsmu_create_group_status');
$create_limit_redirect = get_option('tn_blogsmu_create_group_redirect');
if($create_limit_enable == "yes") {
if( bp_is_group_create() ) {
if ( current_user_can( 'delete_others_posts' ) ) { //only admins and editors
} else {
if( $create_limit_redirect == '' ) {
bp_core_redirect( $bp->root_domain . '/' );
} else {
bp_core_redirect( $create_limit_redirect );
}
}
}

} //off
}
add_filter('get_header','check_if_create_group_limit',2);

/////////////////////////////////////////////////////////////////////////////////////////////////////////
// additional hook on bp
//////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Display Username in Directory */
function my_member_username() {
global $members_template;
return $members_template->member->user_login;
}
add_filter('bp_member_name','my_member_username');

//Add a View BuddyPress profile link in Dashboard >> Users
function user_row_actions_bp_view($actions, $user_object) {
global $bp;
$actions['view'] = '<a href="' . bp_core_get_user_domain($user_object->ID) . '">' . __('View BP', TEMPLATE_DOMAIN) . '</a>';
return $actions;
}
add_filter('user_row_actions', 'user_row_actions_bp_view', 10, 2);



///////////////////////////////////////////////////////////////////////
/// add like it facebook stream
///////////////////////////////////////////////////////////////////////
function add_stream_facebooklike_button() { ?>
<?php if(is_user_logged_in()) { ?>
<p style="margin: 14px 0px; float:left; width: 100%; clear:both;"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php bp_activity_thread_permalink() ?>&amp;layout=standard&amp;show-faces=true&amp;width=450&amp;action=like&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; float: left; width: 100%; height: 30px; overflow:hidden;"></iframe>
</p>
<?php } ?>
<?php }
$tn_blogsmu_stream_facebook_like_status = get_option('tn_blogsmu_stream_facebook_like_status');
if($tn_blogsmu_stream_facebook_like_status == 'enable') {
add_action('bp_activity_entry_content', 'add_stream_facebooklike_button');
}

///////////////////////////////////////////////////////////////////////
/// fetch specific groups
///////////////////////////////////////////////////////////////////////

function fetch_specific_groups($limit='', $size='', $type='', $block_id='') {
global $wpdb, $bp;
$specific_group_id = get_option('tn_blogsmu_home_feat_group_id');
$fetch_group = "SELECT * FROM " . $wpdb->base_prefix . "bp_groups WHERE id IN (" . $specific_group_id . ") ORDER BY id LIMIT $limit";
$sql_fetch_group = $wpdb->get_results($fetch_group); ?>
<div id="<?php echo $block_id; ?>" class="avatar-block">
<?php
$no_avatar = get_template_directory_uri() . '/_inc/images/default.png';
foreach($sql_fetch_group as $group) {
$avatar_full = bp_core_fetch_avatar( 'item_id=' . $group->id . '&class=avatar&object=group&type=' . $type . '&width=' . $size . '&height=' . $size );
$group_description = stripslashes($group->description);
?>
<div class="item-avatar"><a title="<?php echo $group->name . ' - ' . short_text($group_description, 150); ?>" href="<?php echo $bp->root_domain . '/' . bp_get_root_slug( 'groups' )  . '/' . $group->slug; ?>">
<?php echo $avatar_full; ?>
</a></div>
<?php } ?>
</div>
<?php }


///////////////////////////////////////////////////////////////////////
/// fetch random not friend
///////////////////////////////////////////////////////////////////////
function fetch_random_notfriend($limit=6, $size=120) {
global $wpdb, $bp, $friendList, $initiatorList;
$current_loggedin_user = $bp->loggedin_user->id;
$fetch_friend = "SELECT * FROM " . $wpdb->base_prefix . "bp_friends WHERE friend_user_id = '" . $current_loggedin_user . "' AND is_confirmed = '1' ORDER BY rand() LIMIT $limit";
$sql_fetch_friend = $wpdb->get_results($fetch_friend);
$fetch_initiator = "SELECT * FROM " . $wpdb->base_prefix . "bp_friends WHERE initiator_user_id = '" . $current_loggedin_user . "' AND is_confirmed = '1' ORDER BY rand() LIMIT $limit";
$sql_fetch_initiator = $wpdb->get_results($fetch_initiator); ?>
<ul id="random-groups">
<?php
if($sql_fetch_friend) {
foreach($sql_fetch_friend as $friend) {
$array_friend[] = $friend->initiator_user_id;
$friendList = implode(",",$array_friend);
}
}
if($sql_fetch_initiator) {
foreach($sql_fetch_initiator as $initiator) {
$array_initiator[] = $initiator->friend_user_id;
$initiatorList = implode(",",$array_initiator);
}
}
if($sql_fetch_friend && $sql_fetch_initiator) {
$listallfriend = $current_loggedin_user . ',' . $friendList . ',' . $initiatorList;
} else if (!$sql_fetch_friend) {
$listallfriend = $current_loggedin_user . ','  . $initiatorList;
} else if (!$sql_fetch_initiator) {
$listallfriend = $current_loggedin_user . ','  . $friendList;
} else if(!$sql_fetch_friend && !$sql_fetch_initiator) {
$listallfriend = $current_loggedin_user . ',9999999999999,99999999999999999999';
}
// echo $friendList;
$fetch_friend_step2 = "SELECT * FROM " . $wpdb->base_prefix . "users WHERE ID NOT IN ( "  . $listallfriend . ") ORDER BY rand() LIMIT $limit";
$sql_fetch_friend_step2 = $wpdb->get_results($fetch_friend_step2);
foreach($sql_fetch_friend_step2 as $fof) {
$avatar_full = bp_core_fetch_avatar( 'item_id=' . $fof->ID . '&class=av-border&object=user&type=full&width=' . $size . '&height=' . $size );
?>
<li><a title="<?php _e("add",TEMPLATE_DOMAIN); ?> <?php echo $fof->user_nicename; ?> <?php _e("as friend",TEMPLATE_DOMAIN); ?>" href="<?php echo $bp->root_domain . '/' . bp_get_root_slug( 'members' )  . '/' . strtolower( $fof->user_nicename ) . '/'; ?>">
<?php echo $avatar_full; ?>
</a></li>
<?php } ?>
</ul>
<?php }
/////////////////////////////////////////////////////////////////////////////////////////////////////////
// end
////////////////////////////////////////////////////////////////////////////////////////////////////////

} // end $bp_existed checked



function add_post_facebooklike_button() { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php the_permalink() ?>&amp;layout=standard&amp;show-faces=true&amp;width=450&amp;action=like&amp;font=arial&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; float: left; width: 300px; height: 30px; overflow:hidden;"></iframe>
<?php }
$tn_blogsmu_post_facebook_like_status = get_option('tn_blogsmu_post_facebook_like_status');
if($tn_blogsmu_post_facebook_like_status == 'enable') {
add_action('bp_after_post_content', 'add_post_facebooklike_button');
}


///////////////////////////////////////////////////////////////
// single wp adminbar css
////////////////////////////////////////////////////////////////

/* original code from jonas john */
if( !function_exists('colourCreator') ) {
function colourCreator($colour, $per)
{
    $colour = substr( $colour, 1 ); // Removes first character of hex string (#)
    $rgb = ''; // Empty variable
    $per = $per/100*255; // Creates a percentage to work with. Change the middle figure to control colour temperature

    if  ($per < 0 ) // Check to see if the percentage is a negative number
    {
        // DARKER
        $per =  abs($per); // Turns Neg Number to Pos Number
        for ($x=0;$x<3;$x++)
        {
            $c = hexdec(substr($colour,(2*$x),2)) - $per;
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
    }
    else
    {
        // LIGHTER
        for ($x=0;$x<3;$x++)
        {
            $c = hexdec(substr($colour,(2*$x),2)) + $per;
            $c = ($c > 255) ? 'ff' : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
    }
    return '#'.$rgb;
}
         }

if( !is_multisite() ) {

function buddypress_single_adminbar_css() {
$ms_bg = get_option('tn_blogsmu_adminbar_bg_color');
$ms_hover_bg = get_option('tn_blogsmu_adminbar_hover_bg_color');
?>
<?php if( $ms_bg ) { print "<style type='text/css'>"; ?>
div#wp-admin-bar, div#wpadminbar { z-index: 9999; background: <?php echo $ms_bg; ?> none !important; }
div#wpadminbar .quicklinks > ul > li { border-right: 1px solid <?php echo colourCreator($ms_bg,-20); ?> !important; }
#wpadminbar .quicklinks > ul > li > a, #wpadminbar .quicklinks > ul > li > .ab-empty-item, #wpadminbar .quicklinks .ab-top-secondary > li a {
   border-right: 0px none !important;
   border-left: 0px none !important;
}
#wpadminbar .ab-top-secondary {
  background: <?php echo colourCreator($ms_bg,-10); ?> none !important;
}
#wpadminbar .quicklinks .ab-top-secondary > li {
  border-left: 1px solid <?php echo colourCreator($ms_bg,20); ?> !important;
  }

div#wp-admin-bar ul.main-nav li:hover, div#wp-admin-bar ul.main-nav li.sfhover, div#wp-admin-bar ul.main-nav li ul li.sfhover {
background: <?php echo $ms_hover_bg; ?> none !important; }
#wp-admin-bar .padder { background: transparent none !important; }
<?php print "</style>"; ?>
<?php } }

add_action('wp_enqueue_scripts', 'buddypress_single_adminbar_css'); // init global wp_head
add_action('admin_enqueue_scripts', 'buddypress_single_adminbar_css'); // init global admin_head

}



////////////////////////////////////////////////////////////////////////////////
// get attach post img
////////////////////////////////////////////////////////////////////////////////
function dez_get_attachment($the_post_id = '') {
global $wpdb;
$detect_post_id = $the_post_id;
$post_attachment_query = "SELECT guid FROM " . $wpdb->prefix . "posts WHERE post_parent = '" . $detect_post_id . "' AND post_type = 'attachment' ORDER BY menu_order ASC LIMIT 1";
$get_post_attachment = $wpdb->get_var($post_attachment_query);
// If images exist for this page

if(!$get_post_attachment) { ?>
<img title="<h1><?php the_title(); ?></h1>" alt="" src="<?php get_template_directory_uri(); ?>/_inc/images/no-images.jpg">
<?php } else {  ?>
<img title="<h1><?php the_title(); ?></h1>" alt="" src="<?php echo $get_post_attachment; ?>">
<?php }
}



//make text string shorter
function short_text($text='', $wordcount='') {
$text_count = strlen( $text );
if ( $text_count <= $wordcount ) {
$text = $text;
} else {
$text = substr( $text, 0, $wordcount );
$text = $text . '...';
}
return $text;
}

////////////////////////////////////////////////////////////////////////////////
// excerpt the_content()
////////////////////////////////////////////////////////////////////////////////
function custom_the_excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }
  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
  return $excerpt;
}

function custom_the_content($limit) {
global $id, $post;
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  $content = strip_tags($content, '<p>');
  return $content . "<p><a href=\"". get_permalink() . "#more-$id\">" . __('...Click here to read more &raquo;', TEMPLATE_DOMAIN) . "</a></p>";
}


///////////////////////////////////////////////////////////////////////////////
// fetch post img
//////////////////////////////////////////////////////////////////////////////
function custom_get_post_img ($the_post_id='', $size='', $width='', $height='') {
$detect_post_id = $the_post_id;
$images = get_children(array(
'post_parent' => $the_post_id,
'post_type' => 'attachment',
'numberposts' => 1,
'post_mime_type' => 'image'));
if ($images) {
foreach($images as $image) {
$attachment=wp_get_attachment_image_src($image->ID, $size); ?>
<div style="border: 0px none; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background: url(<?php echo $attachment[0]; ?>) no-repeat center center; overflow:hidden;"></div>
<?php
}
} else { ?>
<div style="border: 0px none; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background: url(<?php echo get_template_directory_uri(); ?>/_inc/images/default.jpg) no-repeat center center; overflow:hidden;"></div>
<?php }
}


////////////////////////////////////////////////////////////////////////////////
// WP-PageNavi
////////////////////////////////////////////////////////////////////////////////
function custom_wp_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
	global $request, $posts_per_page, $wpdb, $paged;
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);
		}
		$fromwhere = $matches[1];
		$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
		$max_page = ceil($numposts /$posts_per_page);
		if(empty($paged)) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class=\"wp-pagenavi\"><span class=\"pages\">Page $paged of $max_page:</span>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a>';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='current'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '<a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}

////////////////////////////////////////////////////////////////////////////////
// excerpt features
////////////////////////////////////////////////////////////////////////////////

function the_excerpt_feature($excerpt_length='', $allowedtags='', $filter_type='none', $use_more_link=true, $more_link_text="", $force_more_link=false, $fakeit=1, $fix_tags=true) {
if (preg_match('%^content($|_rss)|^excerpt($|_rss)%', $filter_type)) {
$filter_type = 'the_' . $filter_type;
}
$text = apply_filters($filter_type, get_the_excerpt_feature($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit));
$text = ($fix_tags) ? balanceTags($text) : $text;
echo $text;
}

function get_the_excerpt_feature($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit) {
global $id, $post;
$output = '';
$output = $post->post_excerpt;
if (!empty($post->post_password)) { // if there's a password
if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
$output = __('There is no excerpt because this is a protected post.', TEMPLATE_DOMAIN);
return $output;
}
}

// If we haven't got an excerpt, make one.
if ((($output == '') && ($fakeit == 1)) || ($fakeit == 2)) {
$output = $post->post_content;
$output = strip_tags($output, $allowedtags);

$output = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $output );

$blah = explode(' ', $output);
if (count($blah) > $excerpt_length) {
$k = $excerpt_length;
$use_dotdotdot = 1;
} else {
$k = count($blah);
$use_dotdotdot = 0;
}
$excerpt = '';
for ($i=0; $i<$k; $i++) {
$excerpt .= $blah[$i] . ' ';
}
// Display "more" link (use css class 'more-link' to set layout).
if (($use_more_link && $use_dotdotdot) || $force_more_link) {
$excerpt .= "<a href=\"". get_permalink() . "#more-$id\">$more_link_text</a>";
} else {
$excerpt .= ($use_dotdotdot) ? '...' : '';
}
$output = $excerpt;
} // end if no excerpt
return $output;
}


///////////////////////////////////////////////////////////////////////////////
// Get total count of multiple categories
//////////////////////////////////////////////////////////////////////////////
function dev_multi_category_count($catslugs = '') {
global $wpdb;
$catslug_array = $catslugs;
$slug_where = "cat_terms.term_id IN (" . $catslug_array . ")";
$sql =	"SELECT	COUNT( DISTINCT cat_posts.ID ) AS post_count " .
			"FROM 	" . $wpdb->term_taxonomy . " AS cat_term_taxonomy INNER JOIN " . $wpdb->terms . " AS cat_terms ON " .
						"cat_term_taxonomy.term_id = cat_terms.term_id " .
					"INNER JOIN " . $wpdb->term_relationships . " AS cat_term_relationships ON " .
						"cat_term_taxonomy.term_taxonomy_id = cat_term_relationships.term_taxonomy_id " .
					"INNER JOIN " . $wpdb->posts . " AS cat_posts ON " .
						"cat_term_relationships.object_id = cat_posts.ID " .
			"WHERE 	cat_posts.post_status = 'publish' AND " .
					"cat_posts.post_type = 'post' AND " .
					"cat_term_taxonomy.taxonomy = 'category' AND " .
					$slug_where;

$post_count = $wpdb->get_var($sql);
return $post_count;
}

///////////////////////////////////////////////////////////////////////////////
// get blogs posts and comments count
//////////////////////////////////////////////////////////////////////////////
function get_the_current_blog_post_count() {
global $wpdb;
$numposts = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "posts WHERE post_status = 'publish' AND post_type= 'post'");
return $numposts;
}
function get_the_current_blog_comment_count() {
global $wpdb;
$numcomms = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "comments WHERE comment_approved = '1'");
return $numcomms;
}
///////////////////////////////////////////////////////////////
// http and https detect function
////////////////////////////////////////////////////////////////
function server_https_detect() {
if($_SERVER['HTTPS']){
$the_server_ssl = 'https://';
} else {
$the_server_ssl = 'http://';
}
return $the_server_ssl;
}


////////////////////////////////////////////////////////////////////////////////
// register widget
///////////////////////////////////////////////////////////////////////////////
function blogsmu_widgets_init() {
global $bp_existed;
$footer_enable = get_option('tn_blogsmu_home_footer_block');

register_sidebar(array(
'name'=> __('header 1', TEMPLATE_DOMAIN),
'id' => __('header-1', TEMPLATE_DOMAIN),
'description' => __('Header 1 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

register_sidebar(array(
'name'=> __('header 2', TEMPLATE_DOMAIN),
'id' => __('header-2', TEMPLATE_DOMAIN),
'description' => __('Header 2 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

register_sidebar(array(
'name'=> __('header 3', TEMPLATE_DOMAIN),
'id' => __('header-3', TEMPLATE_DOMAIN),
'description' => __('Header 3 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

register_sidebar(array(
'name'=> __('left-sidebar', TEMPLATE_DOMAIN),
'id' => __('left-sidebar', TEMPLATE_DOMAIN),
'description' => __('Left Sidebar Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

if($footer_enable != 'disable') {
register_sidebar(array(
'name'=> __('footer 1', TEMPLATE_DOMAIN),
'id' => __('footer-1', TEMPLATE_DOMAIN),
'description' => __('Footer 1 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));


register_sidebar(array(
'name'=> __('footer 2', TEMPLATE_DOMAIN),
'id' => __('footer-2', TEMPLATE_DOMAIN),
'description' => __('Footer 2 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

register_sidebar(array(
'name'=> __('footer 3', TEMPLATE_DOMAIN),
'id' => __('footer-3', TEMPLATE_DOMAIN),
'description' => __('Footer 3 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));

register_sidebar(array(
'name'=> __('footer 4', TEMPLATE_DOMAIN),
 'id' => __('footer-4', TEMPLATE_DOMAIN),
'description' => __('Footer 4 Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));
} // end footer check

if($bp_existed == 'true') {
register_sidebar(array(
'name'=> __('member-sidebar', TEMPLATE_DOMAIN),
'id' => __('member-sidebar', TEMPLATE_DOMAIN),
'description' => __('Member Sidebar Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));
}

if ( class_exists( 'bbPress' ) ) {
register_sidebar(array(
'name'=> __('BBPress Sidebar', TEMPLATE_DOMAIN),
'id'=> __('bbpress-sidebar', TEMPLATE_DOMAIN),
'description'=> __('BBPress Sidebar Widget', TEMPLATE_DOMAIN),
'before_widget' => '<li id="%1$s" class="widget %2$s">',
'after_widget' => '</li>',
'before_title' => '<h3>',
'after_title' => '</h3>',
));
}


}
add_action( 'widgets_init', 'blogsmu_widgets_init' );


////////////////////////////////////////////////////////////////////////////////
// CUSTOM IMAGE HEADER  - IF ON WILL BE SHOWN ELSE WILL HIDE
////////////////////////////////////////////////////////////////////////////////

$header_enable = get_option('tn_blogsmu_header_on');
if($header_enable == 'enable' ) {
$custom_height = get_option('tn_blogsmu_image_height');
if($custom_height==''){$custom_height='150'; } else { $custom_height = get_option('tn_blogsmu_image_height'); }
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', ''); // %s is theme dir uri
define('HEADER_IMAGE_WIDTH', 960); //width is fixed
define('HEADER_IMAGE_HEIGHT', $custom_height);
define('NO_HEADER_TEXT', true );

function blogsmu_admin_header_style() { ?>
<style type="text/css">
#headimg {
	background: url(<?php header_image() ?>) no-repeat;
}
#headimg {
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
}

#headimg h1, #headimg #desc {
	display: none;
}
</style>
<?php }
if ( function_exists('add_theme_support') )
	add_theme_support('custom-header', array('admin-head-callback' => 'blogsmu_admin_header_style'));
}
//end check for header image
function custom_img_header_call() {
$tn_blogsmu_header_on = get_option('tn_blogsmu_header_on');
$tn_blogsmu_header_linkto = get_option('tn_blogsmu_header_linkto');
if($tn_blogsmu_header_on == 'enable') {
$header_image = get_header_image();
if($header_image) { ?>
<div id="custom-img-header">
<a href="<?php if($tn_blogsmu_header_linkto == '') { ?><?php echo site_url(); ?><?php } else { ?><?php echo stripslashes($tn_blogsmu_header_linkto); ?><?php } ?>">
<img src="<?php echo $header_image ?>" alt="<?php echo bloginfo('name'); ?>" /></a>
</div>
<?php } } ?>
<?php }


////////////////////////////////////////////////////////////////////////////////
// add theme cms pages
////////////////////////////////////////////////////////////////////////////////
function dev_remove_all_scripts() {
wp_deregister_script('jquery');
wp_deregister_script('jquery-ui-tabs');
}

function mytheme_wp_blogsmu_head() { ?>
<link href="<?php echo get_template_directory_uri(); ?>/_inc/admin/options-css.css" rel="stylesheet" type="text/css" />
<?php if (isset($_GET['page']) && $_GET["page"] == "options-functions.php") {
$feat_style=get_option('tn_blogsmu_featured_blk_option');

print "<style type=\"text/css\">";
if($feat_style == '') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id { display: none !important; }
#sbtabs #tn_blogsmu_featured_custom_field { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_video { display: none !important; }
<?php } else if($feat_style == 'Image Intro') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: inline; }
#sbtabs #tn_blogsmu_featured_video { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_custom_field { display: none !important; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: none !important; }
<?php } else if($feat_style == 'Featured Slider Posts') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: none !important; }
#sbtabs #tn_blogsmu_featured_video { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id, #sbtabs #tn_blogsmu_featured_custom_field { display: inline; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: none !important; }
<?php } else if($feat_style == 'Featured Slider Categories') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: none !important; }
#sbtabs #tn_blogsmu_featured_video { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id, #sbtabs #tn_blogsmu_featured_custom_field { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: inline !important; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: none !important; }
<?php } else if($feat_style == 'Featured Video') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id, #sbtabs #tn_blogsmu_featured_custom_field { display: none; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_video { display: inline !important; }
<?php } else if($feat_style == 'BP Album Rotate') { ?>
#sbtabs #tn_blogsmu_featured_img_url { display: none !important; }
#sbtabs #tn_blogsmu_featured_video { display: none !important; }
#sbtabs #tn_blogsmu_featured_cat_id, #sbtabs #tn_blogsmu_featured_cat_id_count { display: none !important; }
#sbtabs #tn_blogsmu_featured_post_id, #sbtabs #tn_blogsmu_featured_custom_field { display: none; }
#sbtabs #tn_blogsmu_featured_bp_album_count { display: inline !important; }
<?php }
print "</style>"; ?>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jscolor.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/options-js/jquery-ui-personalized-1.6rc2.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/options-js/jquery.cookie.min.js"></script>

<script type="text/javascript">
jQuery.noConflict();
var $jd = jQuery;
$jd(document).ready(function(){
$jd('ul#tabm').tabs({event: "click"});
jQuery("#sbtabs .ui-tabs-nav li a").click(function(){
        jQuery(location).attr('hash', jQuery(this).attr('href') );
});
});
</script>



<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>

<?php add_action('admin_head', 'dev_remove_all_scripts', 100); } else if(isset($_GET['page']) && $_GET["page"] == "custom-homepage.php") { ?>
<?php print "<style type=\"text/css\">"; ?>
#footer { position: relative; }
<?php print "</style>"; ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jquery.imgareaselect-0.3.min.js"></script>
<?php } ?>

<?php }
add_action('admin_head', 'mytheme_wp_blogsmu_head');
///////////////////////////////////////////////////////////////////////
/// load functions
///////////////////////////////////////////////////////////////////////
include( TEMPLATEPATH . '/_inc/functions/services-functions.php');


////////////////////////////////////////////////////////////////////////////////
// get members-login slug
////////////////////////////////////////////////////////////////////////////////
function get_the_page_template_slug($tpl) {
global $wpdb;
$get_page_template = $wpdb->get_var("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_value = '". $tpl ."' AND meta_key = '_wp_page_template'");
$get_page_template_slug = $wpdb->get_var("SELECT post_name FROM " . $wpdb->prefix . "posts WHERE ID = '" . $get_page_template . "' AND post_type='page'");

return $get_page_template_slug;
}

////////////////////////////////////////////////////////////////////////////////
// get google web font
////////////////////////////////////////////////////////////////////////////////
if( !function_exists( 'font_show' ) ):
function font_show(){
$bodytype = get_option('tn_blogsmu_body_font');
$navtype = get_option('tn_blogsmu_nav_font');
$headtype = get_option('tn_blogsmu_headline_font');

if ($bodytype == ""){ ?>
<?php } else if ($bodytype == "Cantarell, arial, serif" ){ ?>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Cardo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Crimson Text, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Droid Sans, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Droid Serif, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "IM Fell DW Pica, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Josefin Sans Std Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Lobster, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Molengo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Neuton, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Nobile, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Reenie Beanie, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Tangerine, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Old Standard TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Volkorn, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Yanone Kaffessatz, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Just Another Hand, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<?php } else if ($bodytype == "Terminal Dosis Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<?php } else if ($bodytype == "Ubuntu, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php }

if ($navtype == ""){ ?>
<?php } else if ($navtype == "Cantarell, arial, serif" ){ ?>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Cardo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Crimson Text, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Droid Sans, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Droid Serif, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "IM Fell DW Pica, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Josefin Sans Std Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Lobster, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Molengo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Neuton, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Nobile, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "OFL Sorts Mill Goudy TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Reenie Beanie, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Tangerine, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Old Standard TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Volkorn, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Yanone Kaffessatz, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<?php } else if ($navtype == "Just Another Hand, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<?php } else if ($navtype == "Terminal Dosis Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<?php } else if ($navtype == "Ubuntu, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php }

if ($headtype == ""){ ?>
<?php } else if ($headtype == "Cantarell, arial, serif" ){ ?>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Cardo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Crimson Text, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Droid Sans, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Droid Serif, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "IM Fell DW Pica, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Josefin Sans Std Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Lobster, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Molengo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Neuton, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Nobile, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "OFL Sorts Mill Goudy TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Reenie Beanie, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Tangerine, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Old Standard TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Volkorn, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Yanone Kaffeesatz, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Just Another Hand, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Terminal Dosis Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Ubuntu, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php }
}
endif;


// Check Remember Me Checked plugin existence and add the check on front-end as well
if ( function_exists('remember_me_checked') ){
	function add_remember_me_checked () {
	?>
	<script type="text/javascript">
		function check_remember_me () {
			jQuery('[name=rememberme]').prop('checked', true);
		}
		jQuery(window).load(check_remember_me);
	</script>
	<?php
	}
	add_action('wp_footer', 'add_remember_me_checked');
}
add_filter('bp_settings_screen_xprofile','xprofile_setting_template');
function xprofile_setting_template(){
	return '/members/single/settings/profile';
}
?>