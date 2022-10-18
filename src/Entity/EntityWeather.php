<?php

namespace Drupal\weather\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the weather entity class.
 *
 * @ContentEntityType(
 *   id = "weatherentity",
 *   label = @Translation("weatherentity"),
 *   label_collection = @Translation("weatherentity"),
 *   label_singular = @Translation("weatherentity"),
 *   label_plural = @Translation("weatherentity"),
 *   label_count = @PluralTranslation(
 *     singular = "@count weatherentity",
 *     plural = "@count weatherentity",
 *   ),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "weatherentity",
 *   data_table = "weatherentity_data",
 *   admin_permission = "administer weatherentity",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *   },
 * )
 */
class EntityWeather extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['city_field'] = BaseFieldDefinition::create('string')
      ->setLabel(t('City'))
      ->setReadOnly(TRUE)
      ->setSetting('max_length', 25);

    $fields['temperature'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Temperature'))
      ->setReadOnly(TRUE)
      ->setSetting('max_length', 10);
    return $fields;
  }

}
