<?php
require __DIR__ . '/wp-harness.php';

$base    = getenv( 'HOME' ) . '/wp-plugins';
$plugins = array(
	'wp-duplicate-anything'       => array( 'opts' => array() ),
	'wp-coming-soon-lite'         => array( 'opts' => array( 'enabled' => 0 ) ), // keep off → no exit path
	'wp-disable-comments-clean'   => array( 'opts' => array() ),
	'wp-reading-time-plus'        => array( 'opts' => array( 'wpm' => 200, 'label' => '{time} min read', 'position' => 'before', 'types' => 'post', 'images' => 1 ) ),
	'wp-announcement-bar'         => array( 'opts' => array( 'enabled' => 1, 'message' => 'Sale!', 'link_text' => 'Go', 'link_url' => 'https://x.test', 'bg' => '#222222', 'fg' => '#ffffff', 'dismissible' => 1 ) ),
	'wp-whatsapp-chat-button'     => array( 'opts' => array( 'enabled' => 1, 'number' => '14155552671', 'prefill' => 'Hi', 'label' => 'Chat', 'position' => 'right' ) ),
	'wp-countdown-timer-block'    => array( 'opts' => array( 'accent' => '#2271b1' ), 'sc' => '[countdown date="2031-12-31 23:59"]' ),
	'wp-faq-accordion-block'      => array( 'opts' => array( 'schema' => 1, 'open_first' => 1 ), 'sc' => '[faq][faq_item q="Refunds?"]Yes, within 30 days.[/faq_item][faq_item q="Where?"]Cape Town.[/faq_item][/faq]' ),
	'wp-redirect-manager'         => array( 'opts' => array( 'rules' => "/old | /new | 301\n/gone || 410", 'log404' => 1 ) ),
	'wp-cookie-consent-bar'       => array( 'opts' => array( 'enabled' => 1, 'message' => 'We use cookies', 'accept' => 'Yes', 'decline' => 'No', 'policy' => 'https://x.test', 'bg' => '#111111' ) ),
	'wp-pricing-table-block'      => array( 'opts' => array( 'accent' => '#2271b1' ), 'sc' => '[pricing][plan name="Pro" price="$29" period="/mo" url="#" featured="yes" features="A|B|C"][/pricing]' ),
	'wp-testimonials-slider'      => array( 'opts' => array( 'accent' => '#2271b1', 'interval' => 5 ), 'sc' => '[testimonials][testimonial name="Jane" role="CEO" stars="5"]Great![/testimonial][/testimonials]' ),
);

$pass = 0; $fail = 0;
foreach ( $plugins as $slug => $cfg ) {
	// Reset registries between plugins.
	$GLOBALS['__actions'] = array();
	$GLOBALS['__filters'] = array();
	$GLOBALS['__shortcodes'] = array();
	$GLOBALS['__options'] = array( $slug . '_options' => $cfg['opts'] );

	$file = "$base/$slug/$slug.php";
	$steps = array();

	try {
		// 1. Load main file (defines class, registers plugins_loaded).
		require $file;
		$steps[] = 'load';

		// 2. Boot (instantiate + configure + settings + hooks + freemius no-op).
		do_action( 'plugins_loaded' );
		$steps[] = 'boot';

		// 3. Front-end content filter (reading time auto-display, etc.).
		ob_start();
		$out = apply_filters( 'the_content', '<p>' . str_repeat( 'word ', 300 ) . '</p>' );
		ob_end_clean();
		$steps[] = 'the_content';

		// 4. Footer renderers (announcement, whatsapp, cookie).
		ob_start();
		do_action( 'wp_footer' );
		ob_get_clean();
		$steps[] = 'wp_footer';

		// 5. Shortcodes.
		if ( ! empty( $cfg['sc'] ) ) {
			$html = do_shortcode( $cfg['sc'] );
			if ( strlen( $html ) < 5 ) { throw new RuntimeException( 'shortcode produced empty output' ); }
			$steps[] = 'shortcode(' . strlen( $html ) . 'b)';
		}

		// 6. Settings page render.
		ob_start();
		foreach ( $GLOBALS['__actions']['admin_menu'] ?? array() as $cb ) { call_user_func( $cb ); }
		ob_get_clean();
		$steps[] = 'settings';

		echo "✓ $slug  [" . implode( ', ', $steps ) . "]\n";
		$pass++;
	} catch ( \Throwable $e ) {
		echo "✗ $slug  FAILED after [" . implode( ', ', $steps ) . "]\n";
		echo "    " . get_class( $e ) . ': ' . $e->getMessage() . "\n";
		echo "    at " . $e->getFile() . ':' . $e->getLine() . "\n";
		$fail++;
	}
}

echo "\n==== $pass passed, $fail failed ====\n";
exit( $fail ? 1 : 0 );
