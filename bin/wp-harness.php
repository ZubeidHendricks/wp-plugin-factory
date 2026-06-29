<?php
/**
 * Minimal WordPress stub harness to smoke-test the factory plugins:
 * loads + boots each plugin and fires its shortcodes / render hooks,
 * catching runtime fatals (not just syntax) without a real WP install.
 */
error_reporting( E_ALL & ~E_DEPRECATED );
define( 'ABSPATH', '/tmp/wp/' );
define( 'WP_UNINSTALL_PLUGIN', true );

$GLOBALS['__actions']    = array();
$GLOBALS['__filters']    = array();
$GLOBALS['__shortcodes'] = array();
$GLOBALS['__options']    = array();

/* --- hooks --- */
function add_action( $h, $cb, $p = 10, $a = 1 ) { $GLOBALS['__actions'][ $h ][] = $cb; return true; }
function add_filter( $h, $cb, $p = 10, $a = 1 ) { $GLOBALS['__filters'][ $h ][] = $cb; return true; }
function do_action( $h, ...$args ) { foreach ( $GLOBALS['__actions'][ $h ] ?? array() as $cb ) { call_user_func_array( $cb, $args ); } }
function apply_filters( $h, $val, ...$rest ) { foreach ( $GLOBALS['__filters'][ $h ] ?? array() as $cb ) { $val = call_user_func_array( $cb, array_merge( array( $val ), $rest ) ); } return $val; }
function add_shortcode( $tag, $cb ) { $GLOBALS['__shortcodes'][ $tag ] = $cb; }

/* --- a real-enough do_shortcode for registered tags (enclosing + self-closing) --- */
function do_shortcode( $content ) {
	foreach ( $GLOBALS['__shortcodes'] as $tag => $cb ) {
		// Enclosing form [tag ...]inner[/tag]
		$content = preg_replace_callback(
			'/\[' . preg_quote( $tag, '/' ) . '((?:\s[^\]]*)?)\](.*?)\[\/' . preg_quote( $tag, '/' ) . '\]/s',
			function ( $m ) use ( $cb ) { return (string) call_user_func( $cb, _ha_atts( $m[1] ), $m[2] ); },
			$content
		);
		// Self-closing form [tag ...]
		$content = preg_replace_callback(
			'/\[' . preg_quote( $tag, '/' ) . '((?:\s[^\]]*)?)\]/s',
			function ( $m ) use ( $cb ) { return (string) call_user_func( $cb, _ha_atts( $m[1] ), '' ); },
			$content
		);
	}
	return $content;
}
function _ha_atts( $str ) {
	$atts = array();
	if ( preg_match_all( '/(\w+)\s*=\s*"([^"]*)"/', $str, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $a ) { $atts[ $a[1] ] = $a[2]; }
	}
	return $atts;
}
function shortcode_atts( $defaults, $atts, $tag = '' ) { return array_merge( $defaults, is_array( $atts ) ? $atts : array() ); }

/* --- options --- */
function get_option( $k, $d = false ) { return $GLOBALS['__options'][ $k ] ?? $d; }
function update_option( $k, $v, $a = null ) { $GLOBALS['__options'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['__options'][ $k ] ); return true; }
function register_setting( ...$a ) {}
function settings_fields( ...$a ) {}
function submit_button( ...$a ) { echo '<button>Save</button>'; }
function add_options_page( ...$a ) { return 'hook'; }

/* --- escaping / i18n (identity) --- */
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return (string) $s; }
function esc_js( $s ) { return (string) $s; }
function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_attr__( $s, $d = '' ) { return $s; }
function esc_html_e( $s, $d = '' ) { echo $s; }
function esc_attr_e( $s, $d = '' ) { echo $s; }
function __( $s, $d = '' ) { return $s; }
function _e( $s, $d = '' ) { echo $s; }
function checked( $a, $b = true, $echo = true ) { $r = ( $a == $b ) ? ' checked' : ''; if ( $echo ) { echo $r; } return $r; }
function selected( $a, $b = true, $echo = true ) { $r = ( $a == $b ) ? ' selected' : ''; if ( $echo ) { echo $r; } return $r; }

/* --- sanitization --- */
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_hex_color( $s ) { return preg_match( '/^#[0-9a-fA-F]{3,6}$/', (string) $s ) ? $s : ''; }
function wp_unslash( $v ) { return is_array( $v ) ? array_map( 'wp_unslash', $v ) : stripslashes( (string) $v ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_kses_post( $s ) { return $s; }
function wpautop( $s ) { return '<p>' . $s . '</p>'; }
function wp_json_encode( $d ) { return json_encode( $d ); }
function wp_rand( $a = 0, $b = 99999 ) { return random_int( $a, $b ); }

/* --- conditionals (front-end, logged-in admin) --- */
function is_admin() { return false; }
function is_404() { return false; }
function is_singular() { return true; }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function is_user_logged_in() { return true; }
function current_user_can( $c ) { return true; }
function is_admin_bar_showing() { return false; }

/* --- urls / misc --- */
function plugin_dir_path( $f ) { return dirname( $f ) . '/'; }
function plugin_dir_url( $f ) { return 'https://example.test/wp-content/plugins/' . basename( dirname( $f ) ) . '/'; }
function home_url( $p = '' ) { return 'https://example.test' . $p; }
function admin_url( $p = '' ) { return 'https://example.test/wp-admin/' . $p; }
function add_query_arg( $k, $v, $url = '' ) { return $url . ( strpos( $url, '?' ) === false ? '?' : '&' ) . $k . '=' . $v; }
function wp_get_referer() { return 'https://example.test/back'; }
function untrailingslashit( $s ) { return rtrim( (string) $s, '/' ); }
function get_bloginfo( $k = '' ) { return 'Test Site'; }
function bloginfo( $k = '' ) { echo 'Test Site'; }
function language_attributes() { echo 'lang="en"'; }
function nocache_headers() {}
function status_header( $c ) {}
function get_current_user_id() { return 1; }

/* --- posts / taxonomies --- */
function get_post( $id = null ) {
	return (object) array(
		'ID' => 7, 'post_title' => 'Sample Post',
		'post_content' => str_repeat( 'word ', 450 ) . '<img src="x.jpg">',
		'post_excerpt' => 'Excerpt', 'post_status' => 'publish',
		'post_type' => 'post', 'post_parent' => 0, 'menu_order' => 0,
	);
}
function get_post_type( $p = null ) { return 'post'; }
function get_post_types( $a = array() ) { return array( 'post', 'page' ); }
function post_type_supports( $t, $f ) { return true; }
function remove_post_type_support( $t, $f ) {}
function get_object_taxonomies( $t ) { return array( 'category' ); }
function wp_get_object_terms( $id, $tax, $a = array() ) { return array( 1, 2 ); }
function wp_set_object_terms( ...$a ) { return array( 1 ); }
function get_post_meta( $id, $k = '', $s = false ) { return array( '_custom' => array( 'val' ) ); }
function add_post_meta( ...$a ) { return true; }
function wp_insert_post( $arr, $err = false ) { return 99; }
function is_wp_error( $t ) { return false; }
function maybe_unserialize( $v ) { return $v; }

/* --- admin / nonce / redirect (no exit in tests) --- */
function wp_nonce_url( $u, $a = -1 ) { return $u . '&_wpnonce=abc'; }
function check_admin_referer( ...$a ) { return true; }
function remove_menu_page( $s ) {}
function remove_meta_box( ...$a ) {}
function wp_die( $m = '', $t = '', $a = array() ) { throw new RuntimeException( 'wp_die: ' . ( is_string( $m ) ? $m : 'error' ) ); }
function wp_safe_redirect( $l, $s = 302 ) { return true; }
function wp_redirect( $l, $s = 302 ) { return true; }

echo "harness loaded\n";
