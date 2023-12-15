#!/usr/bin/env bash
##
# Run lint checks.
#

set -eu
[ -n "${DEBUG:-}" ] && set -x

#-------------------------------------------------------------------------------
# Variables (passed from environment; provided for reference only).
#-------------------------------------------------------------------------------

# Directory where Drupal site will be built.
BUILD_DIR="${BUILD_DIR:-build}"

# Module name, taken from .info file.
MODULE="$(basename -s .info.yml -- ./*.info.yml)"

#-------------------------------------------------------------------------------

echo "==> Lint code for module $MODULE."
echo "  > Running PHPCS."
build/vendor/bin/phpcs \
  -s \
  -p \
  --standard=Drupal,DrupalPractice \
  --extensions=module,php,install,inc,test,info.yml,js \
  "${BUILD_DIR}/web/modules/${MODULE}"

echo "  > Running Drupal Rector."
pushd "${BUILD_DIR}" >/dev/null || exit 1
vendor/bin/rector process \
  "web/modules/${MODULE}" \
  --dry-run
popd >/dev/null || exit 1
