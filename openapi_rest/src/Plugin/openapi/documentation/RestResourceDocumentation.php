<?php

namespace Drupal\openapi_rest\Plugin\openapi\documentation;

use Drupal\openapi\Plugin\ResourceDocumentationBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents entities as resources.
 *
 * @see \Drupal\rest\Plugin\Deriver\EntityDeriver
 *
 * @OpenAPIDocumentation(
 *   id = "rest",
 *   label = @Translation("REST")
 * )
 */
class RestResourceDocumentation extends ResourceDocumentationBase {

  /**
   * Settings for REST resources.
   *
   * @var array
   */
  protected $resource_settings;

  /**
   * REST resource plugin manager.
   *
   * @var \Drupal\rest\Plugin\Type\ResourcePluginManager
   */
  protected $rest;

  /**
   * Constructs a Drupal\openapi\Plugin\ResourceDocumentationBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param array $resource_settings
   *   Settings for the REST resources.
   * @param \Drupal\rest\Plugin\Type\ResourcePluginManager
   *   Manager to load REST resource plugins.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, array $resource_settings, $rest) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
    $this->resource_settings = $resource_settings;
    $this->rest = $rest;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('openapi'),
      $container->get('config.factory')->get('rest.settings')->get('resources'),
      $container->get('plugin.manager.rest')
    );
  }

  public function paths() {

    $paths = array();
    $resources = array_intersect_key($this->resource_settings, $this->rest->getDefinitions());

    foreach ($resources as $resource_id => $methods) {
      /** @var \Drupal\rest\Plugin\ResourceInterface $plugin */
      $plugin = $this->rest->getInstance(['id' => $resource_id]);

      if (!$plugin) {
        continue;
      }

      $routes = $plugin->routes()->all();

      foreach ($methods as $method => $method_info) {
        foreach ($routes as $route_id => $route) {
          if (in_array($method, $route->getMethods())) {

            $paths[$route->getPath()][strtolower($method)] = [
              'operationId' => $route_id,
              'consumes' => '',
              'produces' => '',
              'parameters' => [],
              'responses' => [
                '200' => [
                  'description' => 'Success',
                ],
              ],
            ];


            $compiled_route = $route->compile();

            foreach ($compiled_route->getTokens() as $token) {
              if ($token[0] == 'variable') {
                $paths[$route->getPath()][strtolower($method)]['parameters'][] = [
                  'name' => $token[3],
                  'in' => 'path',
                  'required' => TRUE,
                  'type' => 'string',
                  'pattern' => $token[2],
                ];
              }
            }

            break;
          }
        }
      }
    }

    return $paths;
  }

}
