#!/bin/bash

# create_images.sh
# Usage: ./create_images.sh source.png
# Requires: sips (macOS built-in)

if [ $# -ne 1 ]; then
    echo "Usage: $0 source.png"
    exit 1
fi

SRC="$1"
if [ ! -f "$SRC" ]; then
    echo "Source file '$SRC' not found."
    exit 1
fi

OUTDIR="SafariExtensionIcons"
mkdir -p "$OUTDIR"

# Icon sizes required for Safari Extension (macOS)
declare -a sizes=("16" "32" "64" "128" "256" "512" "1024")

for size in "${sizes[@]}"; do
    OUTFILE="$OUTDIR/icon${size}.png"
    sips -z $size $size "$SRC" --out "$OUTFILE" >/dev/null
    echo "Created $OUTFILE"
done

echo "All icons created in $OUTDIR/"