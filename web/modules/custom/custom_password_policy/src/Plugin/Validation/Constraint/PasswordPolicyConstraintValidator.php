<?php

namespace Drupal\custom_password_policy\Plugin\Validation\Constraint;

use Drupal\password_policy\PasswordPolicyValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Validates the PasswordPolicy constraint.
 */
class PasswordPolicyConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The password policy validator.
   *
   * @var \Drupal\password_policy\PasswordPolicyValidatorInterface
   */
  protected $passwordValidator;

  /**
   * Constructs a PasswordPolicyConstraintValidator object.
   *
   * @param \Drupal\password_policy\PasswordPolicyValidatorInterface $password_validator
   *   The password policy validator.
   */
  public function __construct(PasswordPolicyValidatorInterface $password_validator) {
    $this->passwordValidator = $password_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('password_policy.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($password, Constraint $constraint) {
    if (empty($password) || $password->isEmpty()) {
      return;
    }

    // Get the plain text password.
    $password_value = $password->value;
    dump($password_value);
    // Validate password against all enabled policies.
    $user = NULL;
    if ($password->getParent()->getEntity() !== null) {
      $user = $password->getParent()->getEntity();
    }
    
    $errors = $this->passwordValidator->validatePassword($password_value, $user);
    
    if (!empty($errors)) {
      $this->context->addViolation($constraint->message, ['@reasons' => implode(', ', (array) $errors)]);
    }
  }
}