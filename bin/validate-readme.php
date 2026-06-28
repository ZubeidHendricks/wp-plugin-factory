<?php
// Validate readme.txt files against wordpress.org plugin directory rules.
$base = getenv('HOME') . '/wp-plugins';
$repos = glob("$base/wp-*", GLOB_ONLYDIR);
$issues = 0;
foreach ($repos as $dir) {
    $repo = basename($dir);
    if ($repo === 'wp-plugin-factory') continue;
    $rt = "$dir/readme.txt";
    $main = "$dir/$repo.php";
    if (!file_exists($rt)) { echo "✗ $repo: no readme.txt\n"; $issues++; continue; }
    $r = file_get_contents($rt);
    $php = file_exists($main) ? file_get_contents($main) : '';
    $msgs = [];

    // Header name line
    if (!preg_match('/^===\s*(.+?)\s*===/m', $r, $m)) $msgs[] = "missing === Name === header";
    // Required fields
    foreach (['Contributors','Tags','Requires at least','Tested up to','Stable tag','License'] as $f) {
        if (!preg_match('/^'.preg_quote($f,'/').':\s*(.+)$/mi', $r)) $msgs[] = "missing field: $f";
    }
    // Tags count (max 5 effective)
    if (preg_match('/^Tags:\s*(.+)$/mi', $r, $m)) {
        $t = array_filter(array_map('trim', explode(',', $m[1])));
        if (count($t) > 5) $msgs[] = "too many tags (".count($t).", max 5)";
    }
    // Short description (first non-empty line after header block) <=150 chars
    if (preg_match('/===.*?===\s*\n(.*?)\n\n/s', $r, $m)) {
        // skip the field lines; short desc is the line right after the blank line following fields
    }
    if (preg_match('/\n\n(.+?)\n\n==/s', $r, $m)) {
        $short = trim($m[1]);
        if (strlen($short) > 150) $msgs[] = "short description ".strlen($short)." chars (max 150)";
    } else { $msgs[] = "no short description block found"; }
    // Required sections
    foreach (['== Description ==','== Installation ==','== Changelog =='] as $s) {
        if (strpos($r, $s) === false) $msgs[] = "missing section: $s";
    }
    // Stable tag matches plugin Version
    preg_match('/^Stable tag:\s*([0-9.]+)/mi', $r, $st);
    preg_match('/^\s*\*\s*Version:\s*([0-9.]+)/mi', $php, $vv);
    if (!empty($st[1]) && !empty($vv[1]) && $st[1] !== $vv[1]) $msgs[] = "Stable tag {$st[1]} != plugin Version {$vv[1]}";
    // Text domain matches folder slug
    $slug = substr($repo, 3);
    if ($php && preg_match('/Text Domain:\s*([a-z0-9-]+)/i', $php, $td) && $td[1] !== $slug)
        $msgs[] = "Text Domain {$td[1]} != slug $slug";

    if ($msgs) { echo "✗ $repo:\n"; foreach($msgs as $x) echo "    - $x\n"; $issues += count($msgs); }
    else echo "✓ $repo: readme valid\n";
}
echo "\n==== ".($issues?"$issues issue(s)":"all readmes valid")." ====\n";
