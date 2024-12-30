#!/usr/bin/env bash
#
# Generate woocommerce stubs from the source directory.
#

HEADER=$'/**\n * Generated stub declarations for woocommerce.\n * @see https://woocommerce.com\n * @see https://github.com/0zd0/woocommerce-stubs\n */'

FILE="woocommerce-stubs.php"

set -e

test -f "$FILE" || touch "$FILE"
test -d "source/woocommerce"

"$(dirname "$0")/vendor/bin/generate-stubs" \
    --force \
    --finder=finder.php \
    --visitor=visitor.php \
    --header="$HEADER" \
    --functions \
    --classes \
    --interfaces \
    --traits \
    --constants \
    --out="$FILE"
