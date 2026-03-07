<p align="center">
  <a href="" rel="noopener">
  <img width=200px height=200px src="https://placehold.jp/000000/ffffff/200x200.png?text=Generated+Content&css=%7B%22border-radius%22%3A%22%20100px%22%7D" alt="Generated Content"></a>
</p>

<h1 align="center">Generated Content</h1>

<div align="center">

[![GitHub Issues](https://img.shields.io/github/issues/AlexSkrypnyk/generated_content.svg)](https://github.com/AlexSkrypnyk/generated_content/issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/AlexSkrypnyk/generated_content.svg)](https://github.com/AlexSkrypnyk/generated_content/pulls)
[![Test](https://github.com/AlexSkrypnyk/generated_content/actions/workflows/test.yml/badge.svg)](https://github.com/AlexSkrypnyk/generated_content/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/AlexSkrypnyk/generated_content/graph/badge.svg)](https://codecov.io/gh/AlexSkrypnyk/generated_content)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/AlexSkrypnyk/generated_content)
![LICENSE](https://img.shields.io/github/license/AlexSkrypnyk/generated_content)
![Renovate](https://img.shields.io/badge/renovate-enabled-green?logo=renovatebot)

![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4.svg)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4.svg)
![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4.svg)
![Drupal 10](https://img.shields.io/badge/Drupal-10-009CDE.svg)
![Drupal 11](https://img.shields.io/badge/Drupal-11-006AA9.svg)

</div>

---

Drupal.org module page: https://www.drupal.org/project/generated_content

## User stories

    As a site owner
    I want to see generated content before I have content
    So that I can see how my site looks

    As a Drupal developer
    I want to control what is put into generated content
    So that I have control over what is being generated

    As a Drupal developer
    I want to have a list of pre-generated pages with URLs
    So that I can use them for Visual Regression testing during site releases

## Installation

    composer require drupal/generated_content

## How it works

1. The module uses Drupal's plugin system to discover content generators.
   Each generator is a PHP class with a `#[GeneratedContent]` attribute in
   your module's `src/Plugin/GeneratedContent/` directory.
2. The module provides a helper (singleton) class to generate random and static
   content. It also supports extending this class in your custom module to
   enhance with your site-specific generation helpers.
3. Generated content entities are tracked in the Repository so that they could
   be referenced from other generated entities (e.g., generated Articles
   using generated Tags).
4. Content can be generated from UI `/admin/config/development/generated-content`
   or through a Drush command `drush generated-content:create-content {entity_type} {bundle}`.
5. Content can also be generated on module install if `GENERATED_CONTENT_CREATE`
   environment variable is set to `1`.
   Generation can be further filtered by specified types in `GENERATED_CONTENT_ITEMS`
   environment variable as a comma-separated list of `{entity_type}-{bundle}`
   values:

       # Generate all items in my_module module when it is enabled.
       GENERATED_CONTENT_CREATE=1 drush pm-enable my_module

       # Generate only selected items in my_module module when it is enabled.
       GENERATED_CONTENT_CREATE=1 GENERATED_CONTENT_ITEMS=media-image,taxonomy_term-tags,node-page drush pm-enable my_module

See test [example module 1](modules/generated_content_example1) and [test example module 2](modules/generated_content_example2) for extensive examples.

The module supports `hook_generated_content_plugin_alter()` to alter plugin
definitions at runtime.

## Difference with Devel Generate

Devel Generate and Generated Content are two different tools for creating
content in Drupal. Devel Generate is mainly used for generating random dummy
content, users, and taxonomy terms for testing and development. It allows you to
specify how many and what types of entities to create, but the content is
random.

On the other hand, Generated Content is for creating specific sets of content
based on predefined settings. It is useful for ensuring the same content is
produced each time, which is helpful for tasks like Visual Regression testing
where consistency is key. Unlike Devel Generate, which is more about quick,
random content, Generated Content is about having control and reproducibility
for structured content setups.

Generated Content does not provide any generators itself, but it allows you to
create your own generators and provides a harness to run them.

## Example to generate Tags

```php
<?php

declare(strict_types=1);

namespace Drupal\my_module\Plugin\GeneratedContent;

use Drupal\Core\Link;
use Drupal\generated_content\Attribute\GeneratedContent;
use Drupal\generated_content\Plugin\GeneratedContent\GeneratedContentPluginBase;
use Drupal\taxonomy\Entity\Term;

#[GeneratedContent(id: 'my_module_taxonomy_term_tags', entity_type: 'taxonomy_term', bundle: 'tags', weight: 12)]
class TaxonomyTermTags extends GeneratedContentPluginBase {

  public function generate(): array {
    $total_terms_count = 10;

    $terms = [];

    for ($i = 0; $i < $total_terms_count; $i++) {
      $term = Term::create([
        'vid' => 'tags',
        'name' => 'Generated term ' . ($i + 1),
      ]);

      $term->save();

      $terms[] = $term;

      $this->helper::log(
        'Created "%s" term "%s" [ID: %s] %s',
        $term->bundle(),
        $term->toLink()->toString(),
        $term->id(),
        Link::createFromRoute('Edit', 'entity.taxonomy_term.edit_form', ['taxonomy_term' => $term->id()])->toString()
      );
    }

    return $terms;
  }

}
```

## Generation helper

Generation helper class `GeneratedContentHelper` is a Singleton class which
provides:

1. Random non-Drupal scalar values generation.
2. Static non-Drupal scalar values generation.
3. Random asset generator (files of different types).
4. Static asset generator (files from pre-defined assets).
5. Random Drupal entity values generation.
6. Static Drupal entity values generation.

### Extending generation helper

See example of class extension: [`modules/generated_content_example2/src/GeneratedContentExample2Helper.php`](modules/generated_content_example2/src/GeneratedContentExample2Helper.php)

See example of class usage: [`modules/generated_content_example2/src/Plugin/GeneratedContent/NodeArticle.php`](modules/generated_content_example2/src/Plugin/GeneratedContent/NodeArticle.php)

## Random vs Static content

Sometimes, it is sufficient to simply populate entities with random content
to make the site look "not empty". Depending on your deployment strategy (if
you are enabling content generation modules on every deployment on top of the
fresh database), this may change the content on every deployment.

However, there are times when all generated content can still be a "placeholder"
content, but it should be "static" between deployments, so that all content and
it's aliases would not change. This is specifically important for Visual
Regression testing during a release: the tool can compare generated pages with
known aliases in 2 environments and report differences, if any.

## Roadmap

1. Add more random and static generators.
2. Add tests for existing random and static generators.
3. [Suggest yours](https://www.drupal.org/project/issues/generated_content).

## Local development

1. Install PHP with SQLite support and Composer
3. Clone this repository
4. Run `ahoy build`

## Building website

`ahoy build` assembles the codebase, starts the PHP server
and provisions the Drupal website with this extension enabled. These operations
are executed using scripts within [`.devtools`](.devtools) directory. CI uses
the same scripts to build and test this extension.

The resulting codebase is then placed in the `build` directory. The extension
files are symlinked into the Drupal site structure.

The `build` command is a wrapper for more granular commands:
```bash
ahoy assemble     # Assemble the codebase
ahoy start        # Start the PHP server
ahoy provision    # Provision the Drupal website
```

The `provision` command is useful for re-installing the Drupal website without
re-assembling the codebase.

### Drupal versions

The Drupal version used for the codebase assembly is determined by the
`DRUPAL_VERSION` variable and defaults to the latest stable version.

You can specify a different version by setting the `DRUPAL_VERSION` environment
variable before running the `ahoy build` command:

```bash
DRUPAL_VERSION=11 ahoy build        # Drupal 11
DRUPAL_VERSION=11@alpha ahoy build  # Drupal 11 alpha
DRUPAL_VERSION=10@beta ahoy build   # Drupal 10 beta
DRUPAL_VERSION=11.1 ahoy build      # Drupal 11.1
```

The `minimum-stability` setting in the `composer.json` file is
automatically adjusted to match the specified Drupal version's stability.

### Using Drupal project fork

If you want to use a custom fork of `drupal-composer/drupal-project`, set the
`DRUPAL_PROJECT_REPO` environment variable before running the `ahoy build`
command:

```bash
DRUPAL_PROJECT_REPO=https://github.com/me/drupal-project-fork.git ahoy build
```

### Patching dependencies

To apply patches to the dependencies, add a patch to the `patches` section of
`composer.json`. Local patches are be sourced from the `patches` directory.

### Providing `GITHUB_TOKEN`

To overcome GitHub API rate limits, you may provide a `GITHUB_TOKEN` environment
variable with a personal access token.

### Provisioning the website

The `provision` command installs the Drupal website from the `standard`
profile with the extension (and any `suggest`'ed extensions) enabled. The
profile can be changed by setting the `DRUPAL_PROFILE` environment variable.

The website will be available at http://localhost:8000. The hostname and port
can be changed by setting the `WEBSERVER_HOST` and `WEBSERVER_PORT` environment
variables.

An SQLite database is created in `/tmp/site_generated_content.sqlite` file.
You can browse the contents of the created SQLite database using
[DB Browser for SQLite](https://sqlitebrowser.org/).

A one-time login link will be printed to the console.

## Coding standards

The `ahoy lint` command checks the codebase using multiple tools:
- PHP code standards checking against `Drupal` and `DrupalPractice` standards.
- PHP code static analysis with PHPStan.
- PHP deprecated code analysis and auto-fixing with Drupal Rector.
- Twig code analysis with Twig CS Fixer.
- JavaScript code analysis with ESLint.
- CSS code analysis with Stylelint.

The configuration files for these tools are located in the root of the codebase.

### Fixing coding standards issues

To fix coding standards issues automatically, run `ahoy lint-fix`. This runs
the same tools as `lint` command but with the `--fix` option (for the tools
that support it).

## Testing

The `ahoy test` command runs the PHPUnit tests for this extension.

The tests are located in the `tests/src` directory. The `phpunit.xml` file
configures PHPUnit to run the tests. It uses Drupal core's bootstrap file
`core/tests/bootstrap.php` to bootstrap the Drupal environment before running
the tests.

The `test` command is a wrapper for multiple test commands:
```bash
ahoy test-unit        # Run Unit tests
ahoy test-kernel      # Run Kernel tests
ahoy test-functional  # Run Functional tests
```

### Running specific tests

You can run specific tests by passing a path to the test file or PHPUnit CLI
option (`--filter`, `--group`, etc.) to the `ahoy test` command:

```bash
ahoy test-unit tests/src/Unit/MyUnitTest.php
ahoy test-unit -- --group=wip
```

You may also run tests using the `phpunit` command directly:

```bash
cd build
php -d pcov.directory=.. vendor/bin/phpunit tests/src/Unit/MyUnitTest.php
php -d pcov.directory=.. vendor/bin/phpunit --group=wip
```

---
_This repository was created using the [Drupal Extension Scaffold](https://github.com/AlexSkrypnyk/drupal_extension_scaffold) project template_
