#!/bin/bash

# resize.sh - Resize an image to App Store screenshot size (e.g., 1290x2796 for iPhone 14 Pro Max)

if [ $# -ne 2 ]; then
    echo "Usage: $0 input_image output_image"
    exit 1
fi

INPUT="$1"
OUTPUT="$2"
WIDTH=384
HEIGHT=384

# Check if sips is available
if ! command -v sips &> /dev/null; then
    echo "Error: sips command not found. This script requires macOS."
    exit 2
fi

sips -z $HEIGHT $WIDTH "$INPUT" --out "$OUTPUT"

echo "Resized $INPUT to $WIDTH x $HEIGHT and saved as $OUTPUT"