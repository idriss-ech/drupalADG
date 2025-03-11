<?php

declare(strict_types=1);

namespace Drupal\generated_entity\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\generated_entity\GeneratedEntityInterface;

/**
 * Defines the generated entity entity class.
 *
 * @ContentEntityType(
 *   id = "generated_entity",
 *   label = @Translation("Generated entity"),
 *   label_collection = @Translation("Generated entities"),
 *   label_singular = @Translation("generated entity"),
 *   label_plural = @Translation("generated entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count generated entities",
 *     plural = "@count generated entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\generated_entity\GeneratedEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\generated_entity\Form\GeneratedEntityForm",
 *       "edit" = "Drupal\generated_entity\Form\GeneratedEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "generated_entity",
 *   data_table = "generated_entity_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer generated_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/generated-entity",
 *     "add-form" = "/generated-entity/add",
 *     "canonical" = "/generated-entity/{generated_entity}",
 *     "edit-form" = "/generated-entity/{generated_entity}/edit",
 *     "delete-form" = "/generated-entity/{generated_entity}/delete",
 *     "delete-multiple-form" = "/admin/content/generated-entity/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.generated_entity.settings",
 * )
 */
final class GeneratedEntity extends ContentEntityBase implements GeneratedEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setTranslatable(TRUE)
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the generated entity was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the generated entity was last edited.'));

    return $fields;
  }

}
