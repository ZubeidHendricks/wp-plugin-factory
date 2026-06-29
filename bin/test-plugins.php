<?php
// Full runtime verification of all 12 current plugins via the WP stub harness.
require getenv('HOME') . '/wp-plugins/wp-plugin-factory/bin/wp-harness.php';

$base = getenv('HOME') . '/wp-plugins';
$plugins = array(
	'duplicate-anything'         => array('opts' => array()),
	'coming-soon-lite'           => array('opts' => array('enabled' => 0)),
	'disable-comments-clean'     => array('opts' => array()),
	'reading-time-plus'          => array('opts' => array('wpm'=>200,'label'=>'{time} min read','position'=>'before','types'=>'post','images'=>1)),
	'slim-announcement-bar'      => array('opts' => array('enabled'=>1,'message'=>'Sale!','link_text'=>'Go','link_url'=>'https://x','bg'=>'#222222','fg'=>'#ffffff','dismissible'=>1)),
	'whatsapp-chat-button'       => array('opts' => array('enabled'=>1,'number'=>'14155552671','prefill'=>'Hi','label'=>'Chat','position'=>'right')),
	'countdown-timer-block'      => array('opts' => array('accent'=>'#2271b1'), 'sc' => '[countdown date="2031-12-31 23:59"]'),
	'faq-accordion-block'        => array('opts' => array('schema'=>1,'open_first'=>1), 'sc' => '[faq][faq_item q="Refunds?"]Yes, within 30 days.[/faq_item][faq_item q="Where?"]Cape Town.[/faq_item][/faq]'),
	'redirect-manager'           => array('opts' => array('rules'=>"/old | /new | 301\n/gone || 410",'log404'=>1)),
	'cookie-consent-bar'         => array('opts' => array('enabled'=>1,'message'=>'We use cookies','accept'=>'Yes','decline'=>'No','policy'=>'https://x','bg'=>'#111111')),
	'pricing-table-block'        => array('opts' => array('accent'=>'#2271b1'), 'sc' => '[pricing][plan name="Pro" price="$29" period="/mo" url="#" featured="yes" features="A|B|C"][/pricing]'),
	'simple-testimonials-slider' => array('opts' => array('accent'=>'#db2777','interval'=>5), 'sc' => '[testimonials][testimonial name="Jane" role="CEO" stars="5"]Great![/testimonial][/testimonials]'),
);

$pass = 0; $fail = 0;
foreach ($plugins as $slug => $cfg) {
	$GLOBALS['__actions'] = $GLOBALS['__filters'] = $GLOBALS['__shortcodes'] = array();
	$GLOBALS['__options'] = array($slug.'_options' => $cfg['opts']);
	$file = "$base/wp-$slug/wp-$slug.php";
	$steps = array();
	try {
		require $file; $steps[] = 'load';
		do_action('plugins_loaded'); $steps[] = 'boot';
		ob_start(); apply_filters('the_content', '<p>'.str_repeat('word ',300).'</p>'); ob_end_clean(); $steps[] = 'the_content';
		ob_start(); do_action('wp_footer'); ob_get_clean(); $steps[] = 'wp_footer';
		if (!empty($cfg['sc'])) {
			$html = do_shortcode($cfg['sc']);
			if (strlen($html) < 5) throw new RuntimeException('empty shortcode output');
			$steps[] = 'shortcode('.strlen($html).'b)';
		}
		ob_start(); foreach ($GLOBALS['__actions']['admin_menu'] ?? array() as $cb) call_user_func($cb); ob_get_clean(); $steps[] = 'settings';
		echo "✓ $slug  [".implode(', ',$steps)."]\n"; $pass++;
	} catch (\Throwable $e) {
		echo "✗ $slug  FAILED after [".implode(', ',$steps)."]\n     ".get_class($e).': '.$e->getMessage()." @ ".basename($e->getFile()).':'.$e->getLine()."\n"; $fail++;
	}
}
echo "\n==== runtime: $pass passed, $fail failed ====\n";
exit($fail ? 1 : 0);
