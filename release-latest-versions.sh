#!/usr/bin/env bash
#
# Generate WooCommerce stubs from all the latest versions.
#

set -e

WC_JSON="$(curl -s "https://api.wordpress.org/plugins/info/1.0/woocommerce.json")"

# https://wordpress.org/plugins/woocommerce/advanced/
for V in 9.5 9.6; do
    # Find latest version
    printf -v JQ_FILTER '."versions" | keys[] | select(test("^%s\\\\.%s\\\\.\\\\d+$"))' "${V%.*}" "${V#*.}"
    LATEST="$(jq -r "$JQ_FILTER" <<<"$WC_JSON" | sort -t "." -k 3 -g | tail -n 1)"
    if [ -z "$LATEST" ]; then
        echo "No version for ${V}!"
        continue
    fi

    echo "Releasing version ${LATEST} ..."

    if git rev-parse "refs/tags/v${LATEST}" >/dev/null 2>&1; then
        echo "Tag exists!"
        continue
    fi

    # Clean up source/ directory
    git status --ignored --short -- source/ | sed -n -e 's#^!! ##p' | xargs --no-run-if-empty -- rm -rf
    # Get new version
    printf -v SED_EXP 's#\\("woocommerce/woocommerce"\\): "[0-9]\\+\\.[0-9]\\+\\.[0-9]\\+"#\\1: "%s"#' "${LATEST}"
    curl -L "https://downloads.wordpress.org/plugin/woocommerce.${LATEST}.zip" -o "source/woocommerce.${LATEST}.zip"
    unzip -q -d source/ source/woocommerce.*.zip

    # Generate stubs
    echo "Generating stubs ..."
    bash ./generate.sh

    # Tag version
    git commit --all -m "Generate stubs for WooCommerce ${LATEST}"
    git tag "v${LATEST}"
done
