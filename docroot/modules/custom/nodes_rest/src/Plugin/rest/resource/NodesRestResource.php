<?php

namespace Drupal\nodes_rest\Plugin\rest\resource;

use Psr\Log\LoggerInterface;
use Drupal\rest\ResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Pager\PagerManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get the results of a view.
 *
 * @RestResource(
 *   id = "nodes_rest_list",
 *   label = @Translation("Nodes listing"),
 *   serialization_class = "Drupal\node\Entity\Node",
 *   uri_paths = {
 *     "canonical" = "/nodes/list"
 *   }
 * )
 */
class NodesRestResource extends ResourceBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function get(Request $request) {
    $page = $request->query->get('page', 1);
    $limit = 2;
    $offset = ($page - 1) * $limit;

    # sort them DESC-> show newest nodes first
    $query = \Drupal::entityQuery('node')
      ->range($offset, $limit)
      ->sort('created', 'DESC');
    $nids = $query->execute();

    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $output = [];
    foreach ($nodes as $node) {
      $output[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'type' => $node->getType(),
        'body' => $node->get('body')->value,
        'created' => date('c', $node->getCreatedTime()),
      ];
    }

    $total_count = \Drupal::entityQuery('node')->count()->execute();
    $total_pages = ceil($total_count / $limit);

    $pagination = [
      'current_page' => $page,
      'per_page' => $limit,
      'total_count' => $total_count,
      'total_pages' => $total_pages,
    ];

    $response = new ResourceResponse([
      'data' => $output,
      'pagination' => $pagination,
    ], 200);

    $response = new ResourceResponse($output, 200);

    $cacheability = new CacheableMetadata();
    $cacheability->addCacheContexts(['node:list']);
    $response->addCacheableDependency($cacheability);

    return $response;
  }

}
