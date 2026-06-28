#!/usr/bin/env bash
#
# Vendor the Freemius WordPress SDK into the CURRENT plugin directory.
# Run this from inside a plugin folder (e.g. ~/wp-plugins/wp-reading-time-plus).
#
# After running, copy freemius-config.sample.php -> freemius-config.php, fill in
# your Plugin ID + Public Key, and the factory core auto-activates monetization.
#
set -euo pipefail

DEST="includes/freemius"

if [ ! -d includes ]; then
  echo "✗ Run this from a plugin directory (no ./includes found)." >&2
  exit 1
fi

if [ -d "$DEST/.git" ] || [ -f "$DEST/start.php" ]; then
  echo "→ Freemius SDK already present. Updating…"
  ( cd "$DEST" && git pull --depth 1 --ff-only ) || true
else
  echo "→ Cloning Freemius SDK…"
  git clone --depth 1 https://github.com/Freemius/wordpress-sdk.git "$DEST"
fi

# Drop the SDK's own .git so it ships cleanly inside the plugin.
rm -rf "$DEST/.git"

echo "✓ Freemius SDK vendored at $DEST"
echo "  Next: cp freemius-config.sample.php freemius-config.php  &&  edit your keys."
