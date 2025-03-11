<?php

namespace Drupal\content_entity_example\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'uppercase_text_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "uppercase_text_formatter",
 *   label = @Translation("Uppercase Text Formatter"),
 *   field_types = {
 *     "string",
 *     "text"
 *   }
 * )
 */
class UppercaseTextFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => '<p>' . strtoupper($item->value) . '</p>',
      ];
    }

    return $elements;
  }

}