<?php

/**
 * @file
 * Contains \Drupal\openapi\Controller\OpenAPIPageController.
 */

namespace Drupal\openapi\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\openapi\Plugin\ResourceDocumentationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\openapi\Plugin\Type\ResourceDocumentationPluginManager;

/**
 * Controller routines for page example routes.
 */
class OpenAPIPageController extends ControllerBase {

  public function json() {

    $container = \Drupal::getContainer();

    /** @var ResourceDocumentationPluginManager $documentation */
    $manager = $container->get('plugin.manager.openapi');

    $plugins = $manager->getDefinitions();

    $paths = [];
    foreach (array_keys($plugins) as $plugin_id) {

      /** @var ResourceDocumentationInterface $plugin */
      $plugin = $manager->getInstance(['id' => $plugin_id]);

      foreach ($plugin->paths() as $path => $methods) {
        if (!isset($paths[$path])) {
          $paths[$path] = $methods;
        }
        else {
          // TODO: determine if/when/how to combine endpoints with the same path
        }
      }
    }

    $config = \Drupal::config('openapi.settings');
    $doc = [
      'openapi' => $config->get('spec') ?: '2.0',
      'info' => [
        'title' => $config->get('title') ?: (new FormattableMarkup(':site API', [':site' => \Drupal::config('system.site')->get('name')]))->__toString(),
        'version' => $config->get('version') ?: '1.0',
        'description' => $config->get('description') ?: '',
        'termsOfService' => $config->get('terms') ?: '',
        'contact' => [
          'name' => $config->get('contact.name') ?: '',
          'url' => $config->get('contact.url') ?: '',
          'email' => $config->get('contact.email') ?: '',
        ],
        'license' => [
          'name' => $config->get('license.name') ?: '',
          'url' => $config->get('license.url') ?: '',
        ],
      ],
      'basePath' => base_path(),
      'paths' => $paths,
    ];

    return new JsonResponse($doc);
  }

}
