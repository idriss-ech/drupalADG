<?php

namespace Drupal\custom_password_policy\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Password policy constraint.
 *
 * @Constraint(
 *   id = "PasswordPolicy",
 *   label = @Translation("Password Policy", context = "Validation"),
 * )
 */
class PasswordPolicyConstraint extends Constraint {

  /**
   * Message shown when password doesn't meet the requirements.
   *
   * @var string
   */
  public $message = 'The password does not meet the requirements: @reasons';

}