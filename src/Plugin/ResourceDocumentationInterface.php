<?php

namespace Drupal\openapi\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Specifies the documentation of the publicly available methods of a resource plugin.
 *
 * @see \Drupal\rest\Annotation\RestResource
 * @see \Drupal\rest\Plugin\Type\ResourcePluginManager
 * @see \Drupal\rest\Plugin\ResourceBase
 * @see plugin_api
 *
 * @ingroup third_party
 */
interface ResourceDocumentationInterface extends PluginInspectionInterface {

  /**
   * Returns a collection of paths describing the resources available.
   *
   * @return array()
   *   A collection of paths describing resources.
   */
  public function paths();
  
}
