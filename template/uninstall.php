<?php
/**
 * Fired when the plugin is uninstalled. Cleans up its options.
 *
 * @package {{CLASS}}
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( '{{SLUG}}_options' );
