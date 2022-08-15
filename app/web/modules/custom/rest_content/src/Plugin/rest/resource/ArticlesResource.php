<?php

namespace Drupal\rest_content\Plugin\rest\resource;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityMalformedException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides Articles Resource.
 *
 * @RestResource(
 *   id = "articles_resource",
 *   label = @Translation("Articles Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/articles"
 *   }
 * )
 */
class ArticlesResource extends ResourceBase {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * ArticlesResource constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param ...$parent_parameters
   *   Parameters of a ResourceBase constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ...$parent_parameters) {
    parent::__construct(...$parent_parameters);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): ArticlesResource {
    return new static(
      $container->get('entity_type.manager'),
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest')
    );
  }

  /**
   * Returns JSON response containing articles list.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Articles list JSON response.
   */
  public function get(): JsonResponse {
    $response = [];

    foreach ($this->fetchArticles() as $article) {
      $article_data = $this->extractSingleArticleData($article);

      if (!$article_data) {
        continue;
      }

      $response[] = $article_data;
    }

    return new JsonResponse($response);
  }

  /**
   * Fetches all Article entities.
   *
   * @return array
   *   List of articles.
   */
  protected function fetchArticles(): array {
    $articles = [];

    try {
      $articles = $this->entityTypeManager->getStorage('node')
        ->loadByProperties([
          'type' => 'article',
        ]);
    }
    catch (PluginException $exception) {
      $this->logger->error($exception->getMessage());
    }

    return $articles;
  }

  /**
   * Extracts data from an Article entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $article
   *   Article entity.
   *
   * @return bool|array
   *   Single article response data or FALSE when it cannot be extracted.
   */
  protected function extractSingleArticleData(EntityInterface $article): bool|array {
    try {
      return [
        'id' => $article->id(),
        'path' => $article->toUrl()->setAbsolute()->toString(),
        'bundle' => $article->bundle(),
        'title' => $article->get('title')->value,
      ];
    }
    catch (EntityMalformedException $exception) {
      return FALSE;
    }
  }

}
