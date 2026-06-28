<?php
// Check wordpress.org for slug availability + name competition via the public API.
$plugins = [
  'duplicate-anything'     => 'Duplicate Anything',
  'coming-soon-lite'       => 'Coming Soon & Maintenance Mode Lite',
  'disable-comments-clean' => 'Disable Comments Clean',
  'reading-time-plus'      => 'Reading Time Plus',
  'announcement-bar'       => 'Announcement Bar',
  'whatsapp-chat-button'   => 'WhatsApp Chat Button',
  'countdown-timer-block'  => 'Countdown Timer',
  'faq-accordion-block'    => 'FAQ Accordion',
  'redirect-manager'       => 'Redirect Manager',
  'cookie-consent-bar'     => 'Cookie Consent Bar',
  'pricing-table-block'    => 'Pricing Table',
  'testimonials-slider'    => 'Testimonials Slider',
];

function api($url){
  $ctx = stream_context_create(['http'=>['timeout'=>20,'header'=>"User-Agent: zub-factory\r\n"]]);
  $r = @file_get_contents($url, false, $ctx);
  return $r ? json_decode($r, true) : null;
}

foreach ($plugins as $slug => $name) {
  // 1. Is the exact slug taken?
  $info = api("https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=".rawurlencode($slug));
  $slug_taken = is_array($info) && !empty($info['name']) && empty($info['error']);

  // 2. Top search matches for the display name.
  $q = api("https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[search]=".rawurlencode($name)."&request[per_page]=4");
  $matches = [];
  $exact = false;
  if (!empty($q['plugins'])) {
    foreach ($q['plugins'] as $p) {
      $pn = html_entity_decode(strip_tags($p['name']));
      if (strcasecmp(trim($pn), trim($name)) === 0) $exact = true;
      $matches[] = $pn." (".number_format((int)$p['active_installs'])."+ installs)";
    }
  }

  $flag = $slug_taken ? "❌ SLUG TAKEN" : ($exact ? "⚠️  exact-name match exists" : "✅ slug free");
  echo "── $name  [$slug]\n";
  echo "   $flag\n";
  if ($slug_taken) echo "   taken by: ".html_entity_decode(strip_tags($info['name']))." — ".number_format((int)($info['active_installs']??0))."+ installs\n";
  if ($matches) echo "   top search hits: ".implode("; ", array_slice($matches,0,3))."\n";
  echo "\n";
  usleep(200000);
}
