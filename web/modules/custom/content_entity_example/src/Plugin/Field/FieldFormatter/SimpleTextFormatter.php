<?php

namespace Drupal\content_entity_example\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'simple_text_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "simple_text_formatter",
 *   label = @Translation("Simple Text Formatter"),
 *   field_types = {
 *     "string",
 *     "text"
 *   }
 * )
 */
class SimpleTextFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => '<p>' . $item->value . '</p>',
      ];
    }

    return $elements;
  }

}