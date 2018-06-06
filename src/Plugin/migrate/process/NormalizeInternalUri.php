<?php

namespace Drupal\migrate_plugins\Plugin\migrate\process;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a 'NormalizeInternalUri' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "normalize_internal_uri"
 * )
 */
class NormalizeInternalUri extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Ignore absolute URLS.
    $path = $value;

    if (!UrlHelper::isValid($path)) {
      return $path;
    }

    // Remove leading slash.
    $path = ltrim('/', $path);

    // Prepend the internal schema when missing.
    if (strpos('internal:/', $path) !== FALSE) {
      $path = 'internal:/' . $path;
    }

    // If the internal route do not exists throw and exception.
    if ($url = Url::fromUri($path) && !$url->isRouted()) {
      throw new MigrateException(sprintf('The path "%s" failed validation.', $path));
    }

    return $path;
  }

}
