<?php
/**
 * Generate wordpress.org plugin assets (icon + banner) for each plugin via GD.
 * Output goes to each repo's .wordpress-org/ folder (the standard deploy convention;
 * on SVN these live under the plugin's /assets/ directory).
 */
$base = getenv('HOME') . '/wp-plugins';
$font = '/System/Library/Fonts/Supplemental/Arial Bold.ttf';

// slug => [accent hex, display name, initials, tagline]
$P = [
  'duplicate-anything'     => ['#2563eb','Duplicate Anything','DA','One-click clone any post, page or CPT'],
  'coming-soon-lite'       => ['#0f172a','Coming Soon','CS','Branded maintenance & coming-soon page'],
  'disable-comments-clean' => ['#dc2626','Disable Comments','DC','Kill comments site-wide, scrub the admin'],
  'reading-time-plus'      => ['#7c3aed','Reading Time Plus','RT','Accurate “X min read” for your posts'],
  'announcement-bar'       => ['#2271b1','Announcement Bar','AB','Dismissible notification bar, no code'],
  'whatsapp-chat-button'   => ['#128C3E','WhatsApp Button','WA','Floating click-to-chat in one tap'],
  'countdown-timer-block'  => ['#ea580c','Countdown Timer','CT','Live countdowns for launches & sales'],
  'faq-accordion-block'    => ['#0891b2','FAQ Accordion','FAQ','Accordions + Google FAQ rich results'],
  'redirect-manager'       => ['#4338ca','Redirect Manager','RM','301/302/410 redirects + 404 logging'],
  'cookie-consent-bar'     => ['#b45309','Cookie Consent','CC','Lightweight GDPR consent banner'],
  'pricing-table-block'    => ['#059669','Pricing Table','PT','Responsive pricing tables by shortcode'],
  'testimonials-slider'    => ['#db2777','Testimonials','TS','Rotating customer testimonials slider'],
];

function hex($img,$h,$f=0){
  $h=ltrim($h,'#'); $r=hexdec(substr($h,0,2));$g=hexdec(substr($h,2,2));$b=hexdec(substr($h,4,2));
  if($f){ $r=max(0,(int)($r*(1-$f))); $g=max(0,(int)($g*(1-$f))); $b=max(0,(int)($b*(1-$f))); }
  return imagecolorallocate($img,$r,$g,$b);
}
function gradient($img,$w,$h,$hexc){
  for($y=0;$y<$h;$y++){ $f=0.35*($y/$h); imagefilledrectangle($img,0,$y,$w,$y,hex($img,$hexc,$f)); }
}
function fitfont($font,$text,$boxw,$start){
  $s=$start; do{ $bb=imagettfbbox($s,0,$font,$text); $tw=abs($bb[2]-$bb[0]); if($tw<=$boxw)break; $s-=2; }while($s>8);
  return $s;
}

foreach($P as $slug=>$d){
  [$accent,$name,$initials,$tagline]=$d;
  $out="$base/wp-$slug/.wordpress-org";
  @mkdir($out,0777,true);

  /* ---- ICON 256 ---- */
  $ic=imagecreatetruecolor(256,256); gradient($ic,256,256,$accent);
  $white=imagecolorallocate($ic,255,255,255);
  $fs=fitfont($font,$initials,200,120);
  $bb=imagettfbbox($fs,0,$font,$initials); $tw=abs($bb[2]-$bb[0]); $th=abs($bb[7]-$bb[1]);
  imagettftext($ic,$fs,0,(256-$tw)/2-($bb[0]),(256+$th)/2,$white,$font,$initials);
  imagepng($ic,"$out/icon-256x256.png");
  $ic128=imagecreatetruecolor(128,128); imagecopyresampled($ic128,$ic,0,0,0,0,128,128,256,256);
  imagepng($ic128,"$out/icon-128x128.png");

  /* ---- BANNER 772x250 ---- */
  $bw=772;$bh=250; $bn=imagecreatetruecolor($bw,$bh); gradient($bn,$bw,$bh,$accent);
  $white=imagecolorallocate($bn,255,255,255);
  $soft=imagecolorallocate($bn,235,238,245);
  // accent chip with initials on the right
  $chipx=$bw-200;
  $chip=hex($bn,$accent,0.45);
  imagefilledrectangle($bn,$chipx,0,$bw,$bh,$chip);
  $cfs=fitfont($font,$initials,150,90);
  $cb=imagettfbbox($cfs,0,$font,$initials); $ctw=abs($cb[2]-$cb[0]); $cth=abs($cb[7]-$cb[1]);
  imagettftext($bn,$cfs,0,$chipx+(200-$ctw)/2-$cb[0],(($bh+$cth)/2),$white,$font,$initials);
  // name + tagline on the left
  $nfs=fitfont($font,$name,$chipx-80,46);
  imagettftext($bn,$nfs,0,48,118,$white,$font,$name);
  imagettftext($bn,20,0,50,160,$soft,$font,$tagline);
  imagepng($bn,"$out/banner-772x250.png");
  $bn2=imagecreatetruecolor(1544,500); imagecopyresampled($bn2,$bn,0,0,0,0,1544,500,$bw,$bh);
  imagepng($bn2,"$out/banner-1544x500.png");

  echo "✓ wp-$slug: icon-256, icon-128, banner-772, banner-1544\n";
  imagedestroy($ic);imagedestroy($ic128);imagedestroy($bn);imagedestroy($bn2);
}
echo "\nAssets generated.\n";
