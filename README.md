<p align="center">
  <a href="" rel="noopener">
  <img width=200px height=200px src="https://placehold.jp/000000/ffffff/200x200.png?text=Generated+Content&css=%7B%22border-radius%22%3A%22%20100px%22%7D" alt="Generated Content"></a>
</p>

<h1 align="center">Generated Content</h1>

<div align="center">

[![GitHub Issues](https://img.shields.io/github/issues/AlexSkrypnyk/generated_content.svg)](https://github.com/AlexSkrypnyk/generated_content/issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/AlexSkrypnyk/generated_content.svg)](https://github.com/AlexSkrypnyk/generated_content/pulls)
[![CircleCI](https://circleci.com/gh/AlexSkrypnyk/generated_content.svg?style=shield)](https://circleci.com/gh/AlexSkrypnyk/generated_content)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/AlexSkrypnyk/generated_content)
![LICENSE](https://img.shields.io/github/license/AlexSkrypnyk/generated_content)
![Renovate](https://img.shields.io/badge/renovate-enabled-green?logo=renovatebot)

</div>

Drupal.org module page: https://www.drupal.org/project/generated_content

## User stories

    As a site owner
    I want to see generated content before I have content
    So that I can see how my site looks

    As a Drupal developer
    I want to control what is put into generated content
    So that I could have control over what is being generated

    As a Drupal developer
    I want to have a list of pre-generated pages with URLs
    So that I could use then for Visual Regression testing during site releases

## Installation

    composer require drupal/generated_content

## How it works

1. The module provides callbacks system to generate content entities within a
   code of a custom module sitting in `generated_content/{entity_type}/{entity_bundle}.inc`.
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

See [`generated_content.api.php`](generated_content.api.php) for API of callbacks system.

## Example to generate Tags

```php

<?php

/**
 * @file
 * Create generated Tags terms.
 */

use Drupal\Core\Link;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\taxonomy\Entity\Term;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_weight() {
  return 12;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_tracking().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags_tracking() {
  return TRUE;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function generated_content_example2_generated_content_create_taxonomy_term_tags() {
  // Total number of terms to create.
  $total_terms_count = 10;

  /** @var \Drupal\generated_content\Helpers\GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $terms = [];

  for ($i = 0; $i < $total_terms_count; $i++) {
    // Create a term instance.
    $term = Term::create([
      'vid' => 'tags',
      'name' => 'Generated term ' . ($i + 1),
    ]);

    // Save term instance.
    $term->save();

    // Track saved term instance to return.
    $terms[] = $term;

    // Log creation of this entity.
    $helper::log(
      'Created "%s" term "%s" [ID: %s] %s',
      $term->bundle(),
      $term->toLink()->toString(),
      $term->id(),
      Link::createFromRoute('Edit', 'entity.taxonomy_term.edit_form', ['taxonomy_term' => $term->id()])->toString()
    );
  }

  // Return created term instances.
  return $terms;
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

See example of class usage: [`modules/generated_content_example2/generated_content/node/article.inc`](modules/generated_content_example2/generated_content/node/article.inc)

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
