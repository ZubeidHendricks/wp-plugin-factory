#!/usr/bin/env bash
#
# Stamp a new factory plugin from template/ into a sibling repo folder.
#
# Usage:  bin/new-plugin.sh "Reading Time Plus" reading-time-plus
#
set -euo pipefail

TITLE="${1:?Usage: new-plugin.sh \"Plugin Title\" plugin-slug}"
SLUG="${2:?Usage: new-plugin.sh \"Plugin Title\" plugin-slug}"

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEST="$HERE/../wp-$SLUG"

# Derived tokens
CLASS="$(echo "$SLUG" | sed -E 's/(^|-)([a-z])/\U\2/g')"   # reading-time-plus -> ReadingTimePlus
CONST="$(echo "$SLUG" | tr 'a-z-' 'A-Z_')"                  # reading-time-plus -> READING_TIME_PLUS

if [ -d "$DEST" ]; then
  echo "✗ $DEST already exists" >&2
  exit 1
fi

mkdir -p "$DEST/includes"
cp "$HERE/core/factory-core.php" "$DEST/includes/factory-core.php"

# Render template files, replacing tokens.
render() {
  sed \
    -e "s/{{TITLE}}/$TITLE/g" \
    -e "s/{{SLUG}}/$SLUG/g" \
    -e "s/{{CLASS}}/$CLASS/g" \
    -e "s/{{CONST}}/$CONST/g" \
    "$1"
}

render "$HERE/template/plugin.php"        > "$DEST/wp-$SLUG.php"
render "$HERE/template/readme.txt"        > "$DEST/readme.txt"
render "$HERE/template/uninstall.php"     > "$DEST/uninstall.php"
render "$HERE/template/README.md"         > "$DEST/README.md"
cp     "$HERE/template/freemius-config.sample.php" "$DEST/freemius-config.sample.php"
cp     "$HERE/template/lemonsqueezy.sample.php"     "$DEST/lemonsqueezy.sample.php"
cp     "$HERE/template/.gitignore"          "$DEST/.gitignore"
cp     "$HERE/LICENSE"                       "$DEST/LICENSE"

echo "✓ Created $DEST"
echo "  Next:"
echo "    cd $DEST && git init -b main && git add -A && git commit -m 'Initial commit'"
echo "    gh repo create wp-$SLUG --public --source=. --push"
