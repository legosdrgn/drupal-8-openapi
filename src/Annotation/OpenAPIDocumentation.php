<?php

namespace Drupal\openapi\Annotation;

use \Drupal\Component\Annotation\Plugin;

/**
 * Defines a resource documentation annotation object.
 *
 * Plugin Namespace: Plugin\rest\resource
 *
 * For a working example, see \Drupal\dblog\Plugin\rest\resource\DBLogResource
 *
 * @see \Drupal\rest\Plugin\Type\ResourcePluginManager
 * @see \Drupal\rest\Plugin\ResourceBase
 * @see \Drupal\rest\Plugin\ResourceInterface
 * @see plugin_api
 *
 * @ingroup third_party
 *
 * @Annotation
 */
class OpenAPIDocumentation extends Plugin {

  /**
   * The resource plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the resource plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
