<?php

/**
 * @file
 * Contains \Drupal\openapi\Form\OpenAPIForm.
 */

namespace Drupal\openapi\Form;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteBuilderInterface;

/**
 * Manage REST resources.
 */
class OpenAPIForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * The available Authentication Providers.
   *
   * @var array
   */
  protected $authenticationProviders;

  /**
   * The available serialization formats.
   *
   * @var array
   */
  protected $formats = array();

  /**
   * The route builder used to rebuild all routes.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routeBuilder;

  /**
   * Constructs a \Drupal\user\RestForm object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandler $module_handler, array $authenticationProviders, array $formats, RouteBuilderInterface $routeBuilder) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
    $this->authenticationProviders = $authenticationProviders;
    $this->formats = $formats;
    $this->routeBuilder= $routeBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      array_keys($container->get('restui.authentication_collector')->getSortedProviders()),
      $container->getParameter('serializer.formats'),
      $container->get('router.builder')
    );
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormID() {
    return 'openapi';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'openapi.settings',
    ];
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   *
   * @var array $form
   *   The form array.
   * @var array $form_state
   *   The $form_state array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @var string $resource_id
   *   A string that identfies the REST resource.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = \Drupal::config('openapi.settings');
    
    

    $form['title'] = [
      '#markup' => '<h2>' . t('OpenAPI documentation settings') . '</h2>',
    ];

    $form['openapi'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    $form['openapi']['spec'] = [
      '#title' => t('Specification Version'),
      '#type' => 'textfield',
      '#description' => t('Specifies the Swagger Specification version being used. It can be used by the Swagger UI and other clients to interpret the API listing. The value MUST be "2.0".'),
      '#default_value' => $config->get('spec') ?: '2.0',
      '#disabled' => TRUE,
    ];

    $form['openapi']['title'] = [
      '#title' => t('API Title'),
      '#type' => 'textfield',
      '#description' => t('The title of the application.'),
      '#default_value' => $config->get('title') ?: (new FormattableMarkup(':site API', [':site' => \Drupal::config('system.site')->get('name')]))->__toString(),
      '#required' => TRUE,
    ];

    $form['openapi']['version'] = [
      '#title' => t('API Version'),
      '#type' => 'textfield',
      '#description' => t('Provides the version of the application API (not to be confused with the specification version).'),
      '#default_value' => $config->get('version') ?: '1.0',
      '#required' => TRUE,
    ];

    $form['openapi']['description'] = [
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#description' => t('A short description of the application. GFM syntax can be used for rich text representation.'),
      '#default_value' => $config->get('description') ?: '',
    ];

    $form['openapi']['terms'] = [
      '#title' => t('Terms of Service'),
      '#type' => 'textarea',
      '#description' => t('The Terms of Service for the API.'),
      '#default_value' => $config->get('terms') ?: '',
    ];

    $form['openapi']['contact'] = [
      '#type' => 'details',
      '#title' => t('Contact'),
    ];

    $form['openapi']['contact']['name'] = [
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#description' => t('The identifying name of the contact person/organization.'),
      '#default_value' => $config->get('contact.name') ?: '',
    ];

    $form['openapi']['contact']['url'] = [
      '#title' => t('URL'),
      '#type' => 'textfield',
      '#description' => t('The URL pointing to the contact information. MUST be in the format of a URL.'),
      '#default_value' => $config->get('contact.url') ?: '',
    ];

    $form['openapi']['contact']['email'] = [
      '#title' => t('Email'),
      '#type' => 'textfield',
      '#description' => t('The URL pointing to the contact information. MUST be in the format of a URL.'),
      '#default_value' => $config->get('contact.email') ?: '',
    ];

    $form['openapi']['license'] = [
      '#type' => 'details',
      '#title' => t('License'),
    ];

    $form['openapi']['license']['name'] = [
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#description' => t('The license name used for the API.'),
      '#default_value' => $config->get('license.name') ?: '',
    ];

    $form['openapi']['license']['url'] = [
      '#title' => t('URL'),
      '#type' => 'textfield',
      '#description' => t('A URL to the license used for the API. MUST be in the format of a URL.'),
      '#default_value' => $config->get('license.url') ?: '',
    ];











//    $plugin = $this->resourcePluginManager->getInstance(array('id' => $resource_id));
//    if (empty($plugin)) {
//      throw new NotFoundHttpException();
//    }
//
//    $config = \Drupal::config('rest.settings')->get('resources') ? : array();
//    $methods = $plugin->availableMethods();
//    $pluginDefinition = $plugin->getPluginDefinition();
//    $form['#tree'] = TRUE;
//    $form['resource_id'] = array('#type' => 'value', '#value' => $resource_id);
//    $form['title'] = array(
//      '#markup' => '<h2>' . t('Settings for resource @label', array('@label' => $pluginDefinition['label'])) . '</h2>',
//    );
//    $form['description'] = array(
//      '#markup' => '<p>' . t('Here you can restrict which HTTP methods should this resource support.' .
//          ' And within each method, the available serialization formats and ' .
//          'authentication providers.') . '</p>',
//    );
//    $form['note'] = array(
//      '#markup' => '<p>' . t('<b>Note:</b> Leaving all formats unchecked will enable all of them, while leaving all authentication providers unchecked will default to <code>cookie</code>') . '</p>',
//    );
//    $form['methods'] = array('#type' => 'container');
//
//    foreach ($methods as $method) {
//      $group = array();
//      $group[$method] = array(
//        '#title' => $method,
//        '#type' => 'checkbox',
//        '#default_value' => isset($config[$resource_id][$method]),
//      );
//      $group['settings'] = array(
//        '#type' => 'container',
//        '#attributes' => array('style' => 'padding-left:20px'),
//      );
//
//      // Available formats
//      $enabled_formats = array();
//      if (isset($config[$resource_id][$method]['supported_formats'])) {
//        $enabled_formats = $config[$resource_id][$method]['supported_formats'];
//      }
//      $group['settings']['formats'] = array(
//        '#title' => 'Supported formats',
//        '#type' => 'checkboxes',
//        '#options' => array_combine($this->formats, $this->formats),
//        '#multiple' => TRUE,
//        '#default_value' => $enabled_formats,
//      );
//
//      // Authentication providers.
//      $enabled_auth = array();
//      if (isset($config[$resource_id][$method]['supported_auth'])) {
//        $enabled_auth = $config[$resource_id][$method]['supported_auth'];
//      }
//      $group['settings']['auth'] = array(
//        '#title' => 'Authentication providers',
//        '#type' => 'checkboxes',
//        '#options' => array_combine($this->authenticationProviders, $this->authenticationProviders),
//        '#multiple' => TRUE,
//        '#default_value' => $enabled_auth,
//      );
//      $form['methods'][$method] = $group;
//    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, formstateinterface $form_state) {
    // At least one method must be checked.
//    $method_checked = FALSE;
//    foreach ($form_state->getValue('methods') as $method => $values) {
//      if ($values[$method] == 1) {
//        $method_checked = TRUE;
//        // At least one format and authentication method must be selected.
//        $formats = array_filter($values['settings']['formats']);
//        $auth = array_filter($values['settings']['auth']);
//        if (empty($formats)) {
//          $form_state->setErrorByName('methods][' . $method . '][settings][formats', $this->t('At least one format must be selected for method !method.', array('!method' => $method)));
//        }
//        if (empty($auth)) {
//          $form_state->setErrorByName('methods][' . $method . '][settings][auth' , $this->t('At least one authentication method must be selected for method !method.', array('!method' => $method)));
//        }
//      }
//    }
//    if (!$method_checked) {
//      $form_state->setErrorByName('methods', $form_state, $this->t('At least one HTTP method must be selected'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, formstateinterface $form_state) {

    $values = $form_state->getValue('openapi');

    $config = \Drupal::configFactory()->getEditable('openapi.settings');
    $config->set('spec', $values['spec']);
    $config->set('title', $values['title']);
    $config->set('version', $values['version']);
    $config->set('description', $values['description']);
    $config->set('terms', $values['terms']);
    $config->set('contact.name', $values['contact']['name']);
    $config->set('contact.url', $values['contact']['url']);
    $config->set('contact.email', $values['contact']['email']);
    $config->set('license.name', $values['license']['name']);
    $config->set('license.url', $values['license']['url']);
    $config->save();

    drupal_set_message(t('OpenAPI settings have been updated successfully.'));
  }

}
