#!/bin/bash
# ─────────────────────────────────────────────────────────────
# Elo Marketplace — Theme Color Changer
# Usage:  bash change-theme.sh <hex> [hover-hex]
# Example: bash change-theme.sh "#0d6efd" "#0b5ed7"
# ─────────────────────────────────────────────────────────────

NEW_COLOR="${1}"
HOVER_COLOR="${2}"

if [[ -z "$NEW_COLOR" ]]; then
    echo "Usage: bash change-theme.sh <hex-color> [hover-hex-color]"
    echo "  e.g. bash change-theme.sh \"#0d6efd\" \"#0b5ed7\""
    exit 1
fi

# Strip surrounding quotes if any
NEW_COLOR="${NEW_COLOR//\"/}"
HOVER_COLOR="${HOVER_COLOR//\"/}"

# Default hover to same as primary if not provided
[[ -z "$HOVER_COLOR" ]] && HOVER_COLOR="$NEW_COLOR"

CSS="public/assets/css/style.css"
ADMIN_HEADER="admin/includes/header.php"
SVG_DIR="public/assets/images"

echo "Changing theme to $NEW_COLOR (hover: $HOVER_COLOR) ..."

# ── 1. CSS variables ─────────────────────────────────────────
sed -i "s|--primary-color: #[0-9a-fA-F]\{3,6\};|--primary-color: ${NEW_COLOR};|" "$CSS"
sed -i "s|--secondary-color: #[0-9a-fA-F]\{3,6\};|--secondary-color: ${HOVER_COLOR};|" "$CSS"

# Compute RGB from hex
HEX="${NEW_COLOR#\#}"
R=$((16#${HEX:0:2}))
G=$((16#${HEX:2:2}))
B=$((16#${HEX:4:2}))
sed -i "s|--primary-rgb: [0-9, ]\+;|--primary-rgb: ${R}, ${G}, ${B};|" "$CSS"

# ── 2. CSS: btn-success hover (marked with /* hover-btn */) ──
sed -i "s|background-color: #[0-9a-fA-F]\{3,6\} !important; /\* hover-btn \*/|background-color: ${HOVER_COLOR} !important; /* hover-btn */|" "$CSS"

# Also update the border-color on the line after hover-btn
awk -v hover="$HOVER_COLOR" '
    /hover-btn/ { print; getline; sub(/#[0-9a-fA-F]{3,6}/, hover); print; next }
    { print }
' "$CSS" > /tmp/_elo_style_tmp.css && mv /tmp/_elo_style_tmp.css "$CSS"

# ── 3. Admin sidebar (marked with /* sidebar */) ─────────────
sed -i "s|background: #[0-9a-fA-F]\{3,6\}; /\* sidebar \*/|background: ${NEW_COLOR}; /* sidebar */|" "$ADMIN_HEADER"

# ── 4. All SVG placeholders ──────────────────────────────────
for svg in "$SVG_DIR"/*.svg; do
    sed -i "s|fill=\"#[0-9a-fA-F]\{3,6\}\"|fill=\"${NEW_COLOR}\"|g" "$svg"
    echo "  Updated SVG: $(basename "$svg")"
done

echo ""
echo "Done. Theme is now ${NEW_COLOR}."
echo "Hard-refresh your browser (Ctrl+Shift+R) to see the change."
