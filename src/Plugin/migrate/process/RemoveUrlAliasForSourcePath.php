<?php

namespace Drupal\migrate_plugins\Plugin\migrate\process;

use Drupal\Core\Path\AliasStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'RemoveUrlAliasForSourcePath' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "remove_url_alias_for_source_path"
 * )
 */
class RemoveUrlAliasForSourcePath extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Alias storage service.
   *
   * @var \Drupal\pathauto\AliasStorageInterface
   */
  protected $aliasStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, MigrationInterface $migration, AliasStorageInterface $aliasStorage) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->aliasStorage = $aliasStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $migration,
      $container->get('path.alias_storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!empty($value) && is_string($value)) {
      // The source path must start with leading slash.
      $source = $value;
      if ($source[0] != '/') {
        $source = '/' . $source;
      }

      // Delete any URL alias disregard the language that match source path.
      $result = $this->aliasStorage->delete([
        'source' => $source,
      ]);
    }

    return $value;
  }

}
