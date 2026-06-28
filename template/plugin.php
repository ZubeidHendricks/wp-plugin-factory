<?php
/**
 * Plugin Name:       {{TITLE}}
 * Plugin URI:        https://zubeidhendricks.dev/wp-plugins/{{SLUG}}
 * Description:        {{TITLE}} — a simple, single-purpose plugin from the Zub Plugin Factory.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.2
 * Author:            Zubeid Hendricks
 * Author URI:        https://zubeidhendricks.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       {{SLUG}}
 *
 * @package {{CLASS}}
 */

defined( 'ABSPATH' ) || exit;

define( '{{CONST}}_VERSION', '1.0.0' );

require_once __DIR__ . '/includes/factory-core.php';

/**
 * Main plugin class.
 */
final class {{CLASS}} extends ZubFactory_Plugin {

	protected function configure() {
		$this->slug    = '{{SLUG}}';
		$this->title   = '{{TITLE}}';
		$this->version = {{CONST}}_VERSION;
	}

	protected function settings_fields() {
		return array(
			'enabled' => array(
				'label'    => __( 'Enable', '{{SLUG}}' ),
				'type'     => 'checkbox',
				'cb_label' => __( 'Turn this plugin on', '{{SLUG}}' ),
				'default'  => 1,
			),
		);
	}

	protected function hooks() {
		// TODO: register the one feature this plugin does.
	}
}

add_action(
	'plugins_loaded',
	function () {
		( new {{CLASS}}( __FILE__ ) )->boot();
	}
);
