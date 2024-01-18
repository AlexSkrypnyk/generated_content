#!/usr/bin/env bash
##
# Build the codebase.
#
# Allows to use the latest Drupal core as well as specified versions (for
# testing backward compatibility).
#
# - Retrieves the scaffold from drupal-composer/drupal-project or custom scaffold.
# - Builds Drupal site codebase with current extension and it's dependencies.
# - Adds development dependencies.
# - Installs composer dependencies.
#
# This script will re-build the codebase from scratch every time it runs.

# shellcheck disable=SC2015,SC2094,SC2002

set -eu
[ -n "${DEBUG:-}" ] && set -x

#-------------------------------------------------------------------------------
# Variables (passed from environment; provided for reference only).
#-------------------------------------------------------------------------------

# Drupal core version to use. If not provided - the latest stable version will be used.
# Must be coupled with DRUPAL_PROJECT_SHA below.
DRUPAL_VERSION="${DRUPAL_VERSION:-}"

# Commit SHA of the drupal-project to install custom core version. If not
# provided - the latest version will be used.
# Must be coupled with DRUPAL_VERSION above.
DRUPAL_PROJECT_SHA="${DRUPAL_PROJECT_SHA:-}"

# Repository for "drupal-composer/drupal-project" project.
# May be overwritten to use forked repos that may have not been accepted
# yet (i.e., when major Drupal version is about to be released).
DRUPAL_PROJECT_REPO="${DRUPAL_PROJECT_REPO:-https://github.com/drupal-composer/drupal-project.git}"

#-------------------------------------------------------------------------------

echo "-------------------------------"
echo "         Build codebase        "
echo "-------------------------------"

# Make sure Composer doesn't run out of memory.
export COMPOSER_MEMORY_LIMIT=-1

echo "> Validate tools."
! command -v git >/dev/null && echo "ERROR: Git is required for this script to run." && exit 1
! command -v php >/dev/null && echo "ERROR: PHP is required for this script to run." && exit 1
! command -v composer >/dev/null && echo "ERROR: Composer (https://getcomposer.org/) is required for this script to run." && exit 1
! command -v jq >/dev/null && echo "ERROR: jq (https://stedolan.github.io/jq/) is required for this script to run." && exit 1

# Extension name, taken from the .info file.
extension="$(basename -s .info.yml -- ./*.info.yml)"
[ "${extension}" == "*" ] && echo "ERROR: No .info.yml file found." && exit 1

# Extension type.
type=$(grep -q "type: theme" "${extension}.info.yml" && echo "themes" || echo "modules")

echo "> Validate Composer configuration."
composer validate --ansi --strict

# Reset the environment.
[ -d "build" ] && echo "> Remove existing build directory." && chmod -Rf 777 "build" && rm -rf "build"

# Allow installing custom version of Drupal core from drupal-composer/drupal-project,
# but only coupled with drupal-project SHA (required to get correct dependencies).
if [ -n "${DRUPAL_VERSION:-}" ] && [ -n "${DRUPAL_PROJECT_SHA:-}" ]; then
  echo "> Initialise Drupal site from the scaffold repo ${DRUPAL_PROJECT_REPO} commit ${DRUPAL_PROJECT_SHA}."

  # Clone Drupal core at the specific commit SHA.
  git clone -n "${DRUPAL_PROJECT_REPO}" "build"
  git --git-dir="build/.git" --work-tree="build" checkout "${DRUPAL_PROJECT_SHA}"
  rm -rf "build/.git" >/dev/null

  echo "> Pin Drupal to a specific version ${DRUPAL_VERSION}."
  sed_opts=(-i) && [ "$(uname)" == "Darwin" ] && sed_opts=(-i '')
  sed "${sed_opts[@]}" 's|\(.*"drupal\/core"\): "\(.*\)",.*|\1: '"\"$DRUPAL_VERSION\",|" "build/composer.json"
  cat "build/composer.json"
else
  echo "> Initialise Drupal site from the latest scaffold."
  # There are no releases in "drupal-composer/drupal-project", so have to use "@dev".
  composer create-project drupal-composer/drupal-project:@dev "build" --no-interaction --no-install
fi

echo "> Merge configuration from composer.dev.json."
php -r "echo json_encode(array_replace_recursive(json_decode(file_get_contents('composer.dev.json'), true),json_decode(file_get_contents('build/composer.json'), true)),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);" >"build/composer2.json" && mv -f "build/composer2.json" "build/composer.json"

echo "> Merge configuration from extension's composer.json."
php -r "echo json_encode(array_replace_recursive(json_decode(file_get_contents('composer.json'), true),json_decode(file_get_contents('build/composer.json'), true)),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);" >"build/composer2.json" && mv -f "build/composer2.json" "build/composer.json"

echo "> Create GitHub authentication token if provided."
[ -n "${GITHUB_TOKEN:-}" ] && composer config --global github-oauth.github.com "${GITHUB_TOKEN}" && echo "Token: " && composer config --global github-oauth.github.com

echo "> Create custom directories."
mkdir -p build/web/modules/custom build/web/themes/custom

echo "> Install dependencies."
composer --working-dir="build" install

# Suggested dependencies allow to install them for testing without requiring
# them in extension's composer.json.
echo "> Install suggested dependencies from extension's composer.json."
composer_suggests=$(cat composer.json | jq -r 'select(.suggest != null) | .suggest | keys[]')
for composer_suggest in $composer_suggests; do
  composer --working-dir="build" require "${composer_suggest}"
done

echo "> Copy tools configuration files."
cp phpcs.xml phpstan.neon phpmd.xml rector.php .twig_cs.php "build/"

echo "> Symlink extension's code."
rm -rf "build/web/${type}/custom" >/dev/null && mkdir -p "build/web/${type}/custom/${extension}"
ln -s "$(pwd)"/* "build/web/${type}/custom/${extension}" && rm "build/web/${type}/custom/${extension}/build"

# If front-end dependencies are used in the project, package-lock.json is
# expected to be committed to the repository.
if [ -f "build/web/${type}/custom/${extension}/package-lock.json" ]; then
  pushd "build/web/${type}/custom/${extension}" >/dev/null || exit 1
  echo "> Install front-end dependencies."
  [ -f ".nvmrc" ] && nvm use || true
  [ ! -d "node_modules" ] && npm ci || true
  echo "> Build front-end dependencies."
  npm run build
  popd >/dev/null || exit 1
fi

echo
echo "-------------------------------"
echo "        Codebase built 🚀      "
echo "-------------------------------"
echo
echo "> Next steps:"
echo "  .devtools/start-server.sh # Start the webserver"
echo "  .devtools/provision.sh    # Provision the website"
echo
