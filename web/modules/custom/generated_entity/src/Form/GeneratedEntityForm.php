<?php

declare(strict_types=1);

namespace Drupal\generated_entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the generated entity entity edit forms.
 */
final class GeneratedEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->toLink()->toString()];
    $logger_args = [
      '%label' => $this->entity->label(),
      'link' => $this->entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New generated entity %label has been created.', $message_args));
        $this->logger('generated_entity')->notice('New generated entity %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The generated entity %label has been updated.', $message_args));
        $this->logger('generated_entity')->notice('The generated entity %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl());

    return $result;
  }

}
