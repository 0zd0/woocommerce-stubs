#!/usr/bin/env bash
#
# Generate woocommerce stubs from the source directory.
#

HEADER=$'/**\n * Generated stub declarations for woocommerce.\n * @see https://woocommerce.com\n * @see https://github.com/0zd0/woocommerce-stubs\n */'

FILE="woocommerce-stubs.phpstub"
DIR=$(dirname "$0")

IGNORE_HOOKS=(
"woocommerce_email_recipient_' . \$this->id"
"woocommerce_process_' . \$post->post_type . '_meta"
"woocommerce_order_action_' . sanitize_title( \$action )"
"woocommerce_order_status_' . \$status_transition['to']"
)
IGNORE_HOOKS_STRING=$(IFS=,; echo "${IGNORE_HOOKS[*]}")

set -e

test -f "$FILE" || touch "$FILE"
test -d "source/woocommerce"

"$DIR/vendor/bin/generate-hooks" \
    --input=source/woocommerce \
    --input=source/overrides \
    --output=hooks \
    --ignore-hooks="$IGNORE_HOOKS_STRING"

"$DIR/vendor/bin/generate-stubs" \
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
