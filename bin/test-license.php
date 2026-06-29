<?php
require getenv('HOME') . '/wp-plugins/wp-plugin-factory/bin/wp-harness.php';

// Extra stubs the License class needs.
if (!defined('DAY_IN_SECONDS')) define('DAY_IN_SECONDS', 86400);
$GLOBALS['__remote'] = null; // canned API response (json string)
function wp_remote_post($url, $args = array()) { return array('body' => $GLOBALS['__remote'], 'url' => $url, 'sent' => $args['body']); }
function wp_remote_retrieve_body($res) { return is_array($res) ? ($res['body'] ?? '') : ''; }
function wp_nonce_field($a = -1, $b = '_wpnonce') { echo '<input type="hidden" name="_wpnonce" value="x">'; }

require getenv('HOME') . '/wp-plugins/wp-plugin-factory/core/factory-core.php';

$slug = 'reading-time-plus';
$ok = 0; $fail = 0;
function check($label, $cond) { global $ok,$fail; echo ($cond?"✓ ":"✗ ").$label."\n"; $cond?$ok++:$fail++; }

// Boot with a product lock of 123.
ZubFactory_License::boot($slug, array('product_id' => 123, 'buy_url' => 'https://printparty.lemonsqueezy.com/buy/abc'));

// 1. Fresh: no license → Pro gate OFF.
check('fresh install: is_pro = false', ZubFactory_Upsell::is_pro($slug) === false);

// 2. License box renders with an Activate button + buy link.
ob_start(); ZubFactory_License::render_box($slug); $box = ob_get_clean();
check('license box shows Activate + buy link', strpos($box,'Activate')!==false && strpos($box,'/buy/abc')!==false);

// 3. Activate with a key that returns the WRONG product → must stay OFF.
$GLOBALS['__remote'] = json_encode(array('activated'=>true,'instance'=>array('id'=>'i1'),'meta'=>array('product_id'=>999)));
$_POST = array($slug.'_license_action'=>'activate', $slug.'_license_key'=>'KEY-WRONG-PRODUCT');
ZubFactory_License::handle($slug);
check('wrong product_id rejected: is_pro = false', ZubFactory_Upsell::is_pro($slug) === false);

// 4. Activate with a valid key for the RIGHT product → Pro gate ON.
$GLOBALS['__remote'] = json_encode(array('activated'=>true,'instance'=>array('id'=>'i2'),'meta'=>array('product_id'=>123)));
$_POST = array($slug.'_license_action'=>'activate', $slug.'_license_key'=>'KEY-VALID');
ZubFactory_License::handle($slug);
check('valid key activates: is_pro = true', ZubFactory_Upsell::is_pro($slug) === true);

// 5. Box now shows "Pro active" + Deactivate.
ob_start(); ZubFactory_License::render_box($slug); $box2 = ob_get_clean();
check('box shows Pro active + Deactivate', strpos($box2,'Pro active')!==false && strpos($box2,'Deactivate')!==false);

// 6. Daily re-validate keeps it active when LS says valid.
$st = get_option($slug.'_license'); $st['checked'] = time() - 2*DAY_IN_SECONDS; update_option($slug.'_license',$st);
$GLOBALS['__remote'] = json_encode(array('valid'=>true,'meta'=>array('product_id'=>123)));
check('stale re-validate (still valid): is_pro = true', ZubFactory_Upsell::is_pro($slug) === true);

// 7. Re-validate when LS says invalid (e.g. refunded) → gate flips OFF.
$st = get_option($slug.'_license'); $st['checked'] = time() - 2*DAY_IN_SECONDS; update_option($slug.'_license',$st);
$GLOBALS['__remote'] = json_encode(array('valid'=>false,'error'=>'license_key not active'));
check('stale re-validate (now invalid): is_pro = false', ZubFactory_Upsell::is_pro($slug) === false);

// 8. Deactivate clears everything.
$GLOBALS['__remote'] = json_encode(array('deactivated'=>true));
$_POST = array($slug.'_license_action'=>'deactivate');
ZubFactory_License::handle($slug);
check('deactivate clears license', get_option($slug.'_license', 'GONE') === 'GONE' && ZubFactory_Upsell::is_pro($slug) === false);

echo "\n==== license flow: $ok passed, $fail failed ====\n";
exit($fail ? 1 : 0);
