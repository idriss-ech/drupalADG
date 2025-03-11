# Drupal Advanced Development Guide - Sprint 6
<hr>

# 🚀🌟 DAY 1 🌟🚀

## Create a Custom Content Entity
https://drupalize.me/tutorial/create-custom-content-entity?p=2607

<img width="1440" alt="image" src="https://github.com/user-attachments/assets/6ebc693f-6d6d-4829-9165-e5394f7cbc9e" />
<img width="1440" alt="image" src="https://github.com/user-attachments/assets/bf081502-1ff9-4308-93c5-b525563f75bd" />



<hr>

## Altering the User Entity's Password Field with a Constraint

### 1. Install and Configure the Password Policy Module  
- The module is installed via Composer and enabled using Drush.
  ```bash
  composer require 'drupal/password_policy:^4.0'
  ```
  <img width="855" alt="image" src="https://github.com/user-attachments/assets/c5b1a456-fa69-4756-b10c-0da657bb9e32" />
- enable the models Password Character Length Policy and Password Character Types Policy
  <img width="1141" alt="image" src="https://github.com/user-attachments/assets/414d2a63-e303-49b7-8862-3d95a92207b0" />
- Password policies are configured in the Drupal admin panel.
  <img width="1440" alt="image" src="https://github.com/user-attachments/assets/be713051-d0c7-45e9-8b67-b87fc8d8e451" />


### 2. Alter the User Entity Type  
- The `hook_entity_base_field_info_alter()` hook is implemented in a custom module.  
- It modifies the `pass` field of the user entity by adding a custom validation constraint.
```php
function custom_password_policy_entity_base_field_info_alter(&$fields, EntityTypeInterface $entity_type) {
  // Only alter the user entity type.
  if ($entity_type->id() === 'user') {
    // Check if the pass field exists.
    if (isset($fields['pass']) && $fields['pass'] instanceof BaseFieldDefinition) {
      // Add the custom constraint to the pass field.
      $fields['pass']->addConstraint('PasswordPolicy', []);
    }
  }
}
```

### 3. Define a Password Constraint Plugin  
- A new constraint named **PasswordPolicyConstraint** is created.  
- It defines the validation error message displayed if the password does not meet the policy requirements.  
```php
class PasswordPolicyConstraint extends Constraint {

  /**
   * Message shown when password doesn't meet the requirements.
   *
   * @var string
   */
  public $message = 'The password does not meet the requirements: @reasons';

}
```
### 4. Create the Constraint Validator  
- A validator class is implemented.  
- It injects the `password_policy.validator` service, which checks if the password meets the defined policies.  
- If the password is invalid, an error message is generated.  
```php
class PasswordPolicyConstraintValidator extends ConstraintValidator {
  protected $passwordPolicyValidator;

  public function __construct(PasswordPolicyValidatorInterface $passwordPolicyValidator) {
    $this->passwordPolicyValidator = $passwordPolicyValidator;
  }

  public function validate($value, Constraint $constraint) {
    if (!$this->passwordPolicyValidator->validate($value)) {
      $this->context->addViolation($constraint->message);
    }
  }
}
```
### 5. Register the Validator as a Service  
- The validator is registered in the module’s `.services.yml` file.  
- It is linked to the constraint system in Drupal.  
```php
services:
  custom_password_policy.password_policy_constraint_validator:
    class: Drupal\custom_password_policy\Plugin\Validation\Constraint\PasswordPolicyConstraintValidator
    arguments: ['@password_policy.validator']
    tags:
      - { name: constraint.validator }
```
### 6. Validation Execution  
- When a user updates their password, Drupal triggers validation on the `pass` field.  
- The constraint validator uses the `password_policy.validator` service to check compliance.  
- If the password does not meet the policy, an error message is displayed, preventing submission.
<img width="1440" alt="image" src="https://github.com/user-attachments/assets/08e1043a-f950-4d95-ad1b-a186f1a41ec6" />


<hr>

## What is AccessResult and how does it work?
AccessResult is a value object in Drupal used for access control, complete with cacheability metadata. It allows us to define access results using methods like allowed(), forbidden(), and neutral(). These results can be combined with methods like andIf() or orIf() to create more complex access logic.
## Creating a custom entity involves a lot of moving parts and boilerplate code, how do you quickly generate and scafold a new entity codebase ?
```bash
drush generate entity:content
```
<img width="924" alt="image" src="https://github.com/user-attachments/assets/0b6ba60c-2630-48c5-a16d-d4b83e74b4fd" />
<hr>

## How do you get a field definition using code ?
In Drupal, the EntityFieldManager service provides methods to retrieve information about fields in entities. It's primarily used to programmatically access field definitions for a specific entity type or bundle (e.g., a "node" of type "article").
Key Method:

getFieldDefinitions($entity_type, $bundle) Retrieves all field definitions for a specified entity type (e.g., node, user) and bundle (e.g., article, page).
```bash
./vendor/bin/drush php
> $field_definitions = \Drupal::service('entity_field.manager')  ->getFieldDefinitions('node', 'article');
> foreach ($field_definitions as $field_name => $field_definition) {
    print $field_name . "\n";
    }
```
<img width="907" alt="image" src="https://github.com/user-attachments/assets/742b59cb-d701-424d-a7c7-9715b9dc9245" />
* Service Call: \Drupal::service('entity_field.manager') gets the EntityFieldManager.

* Field Definitions: getFieldDefinitions('node', 'article') fetches all fields for the "article" content type.

* Loop: Iterates through the fields and prints their names.
<hr>

## Is it possible to create multiple fields formatter for a field type ? If so, how ?
Yes, it is absolutely possible to create multiple field formatters for a single field type in Drupal. Field formatters are used to control how the data of a field is displayed when rendered on the front end.

⌨️ And this is how: 
First, we create a src/Plugin/Field/FieldFormatter directory in our module. Then, we create the field formatter classes. For example, the first field formatter could be a Simple Text Formatter,
```php

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
```

and the second could be an Uppercase Text Formatter. 


```php
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
```
After that, we clear the cache and navigate to the 'Manage Display' page for an entity type, where we can see the new formatters.
<img width="1202" alt="image" src="https://github.com/user-attachments/assets/a85d9064-5485-4ed3-85c6-7758fcfa3a31" />

<hr>

## Using drush, how to retrieve a module configuration (settings) ?
To retrieve a module's configuration (settings) using Drush, you can use the drush config:get (or drush cget) command. Here's how we can do it:
```bush
./drush cget custom_password_policy.settings   
```
<img width="833" alt="image" src="https://github.com/user-attachments/assets/ab3caeca-bcff-45a7-b9ff-5d0cf78e7e2b" />

<hr>

# 🚀🌟 DAY 2 🌟🚀

<hr>

## How do you add a new field base to an exisiting entity type using hook_entity_base_field_info
hook_entity_base_field_info() is a Drupal hook that allows as to define base fields for our custom or existing entities. Base fields are fields that are defined programmatically and are always present for a specific entity type. Unlike configurable fields (which are added through the UI and stored in configuration), base fields are part of the entity's structure and are stored in code.

So, Let's try to use this hook to add "Featured" Field to Articles
```php
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Implements hook_entity_base_field_info().
 */
function generated_entity_entity_base_field_info(EntityTypeInterface $entity_type) {
  $fields = [];

  // Check if the entity type is a node.
  if ($entity_type->id() === 'node') {
    // Check for the "article" bundle (content type).
    $fields['featured'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Featured'))
      ->setDescription(t('Mark this article as featured.'))
      ->setDefaultValue(false)
      ->setSetting('on_label', t('Yes'))
      ->setSetting('off_label', t('No'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->addConstraint('bundle', ['value' => 'article']); // Limit this field to articles only.
  }

  return $fields;
}
```
<img width="928" alt="Screenshot 2025-03-11 at 12 13 04" src="https://github.com/user-attachments/assets/9269306c-6565-40ed-ab9f-809e1b80ed59" />


<hr>

## What is the role of hook_update_n ?
Purpose: It is used to apply changes to the database, configuration, or other data structures when a module is updated.

Naming Convention: The N in hook_update_N() represents a sequential number (e.g., hook_update_8001(), hook_update_9001()). This number corresponds to the module's schema version.

Execution: When you run drush updb or visit update.php, Drupal checks for any pending updates and runs them in order.

### Let's use this hook to add a new tabe "my_custom_table" to database 
```php
/**
 * Implements hook_update_N().
 */
function generated_entity_update_8001() {
  // Define the schema for the new table.
  $schema = [
    'fields' => [
      'id' => [
        'type' => 'serial',
        'not null' => TRUE,
        'description' => 'Primary key: ID.',
      ],
      'name' => [
        'type' => 'varchar',
        'length' => 255,
        'not null' => TRUE,
        'description' => 'Name of the item.',
      ],
      'created' => [
        'type' => 'int',
        'not null' => TRUE,
        'description' => 'Timestamp of creation.',
      ],
    ],
    'primary key' => ['id'],
  ];

  // Create the table using Drupal's schema API.
  \Drupal::database()->schema()->createTable('my_custom_table', $schema);

  // Provide a human-readable message about what the update does.
  return t('Added the new "my_custom_table" to the database.');
}
```

- Now let's upply this update using drush 

```bash
drush updatedb
```

<img width="1229" alt="image" src="https://github.com/user-attachments/assets/a909edca-f86e-45e6-841f-0fd1df735e76" />

Let's check database tables list :
<img width="1440" alt="Screenshot 2025-03-11 at 12 29 25" src="https://github.com/user-attachments/assets/c9c592c0-1f2d-4614-a955-ebc2d476ce26" />

<hr>

## What is the role of hook_install ?
hook_install() is a special Drupal hook that runs when your module is installed. It is used to perform tasks that need to happen only once, at the time of installation. For example, you might use it to create database tables, add default configuration, or set up necessary variables for your module.

- Let's try an example with the module custom_password_policy. We will implement the hook in the module file:
```php
/**
 * Implements hook_install().
 */
function custom_password_policy_install() {
  // Set default values for the module's configuration.
  \Drupal::configFactory()->getEditable('custom_password_policy.settings')
    ->set('default_message', 'Welcome to our site!')  // Set a default welcome message.
    ->save();
}
```
- Now, let's verify the configuration of the module by running this command:
```bash
drush cget custom_password_policy.settings 
```
<img width="1194" alt="image" src="https://github.com/user-attachments/assets/6c41ee7b-4282-49b5-9c66-da393daa83cc" />

- Next, we will uninstall the module and install it again. Finally, let's check the configuration file once more.
<img width="1195" alt="image" src="https://github.com/user-attachments/assets/4c4b24e4-c10e-461f-9476-be7b4dfccd9a" />

- As expected, the default_message has been added to the configuration file of our module.




<hr>

## How would you prefix all your newly created nodes (type: article) with HEY- using hook_ENTITY_TYPE_presave ?
- hook_ENTITY_TYPE_presave is a hook that allows as to make changes to an entity before it is saved to the database.
```php
<?php
  /**
 * Implements hook_entity_presave().
 */
function custom_password_policy_entity_presave(EntityInterface $entity) {
  if ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'article') {
    // Check if this is a new node (doesn't have an ID yet).
    if ($entity->isNew()) {
      $title = $entity->get('title')->value;

      // Prefix the title with "HEY-" if it's not already prefixed.
      if (strpos($title, 'HEY-') !== 0) {
        $entity->set('title', 'HEY-' . $title);
      }
    }
  }
}
```
  <img width="924" alt="image" src="https://github.com/user-attachments/assets/5e6b68db-b4df-46f3-bd5c-04ae586dfe78" />


<hr>

## What is the role of $entity->original ?
$entity->original is used to access the original version of an entity before it is updated or changed.

<hr>

## How can you override a Theme Hook provided by another module ?
In Drupal, overriding a theme hook provided by another module requires a combination of leveraging hook_theme(), hook_theme_suggestions_HOOK_alter() and providing custom templates. This allows us to customize how certain elements are rendered. Let’s walk through the steps together:
 - Step 1: Declare the Custom Theme Hook in hook_theme()
```php
/**
 * Implements hook_theme().
 */
function custom_password_policy_theme() {
  return [
    'my__page__custom' => [ // Define a custom suggestion.
      'template' => 'my--page--custom', // Points to the Twig file (without .html.twig).
      'render element' => 'elements',  // Passes renderable elements to the template.
    ],
  ];
}
```
-Step 2: Alter Theme Suggestions with hook_theme_suggestions_HOOK_alter()
```php
/**
 * Implements hook_theme_suggestions_HOOK_alter() for node templates.
 */
function custom_password_policy_theme_suggestions_node_alter(array &$suggestions, array $variables) {
  // Check if the rendered element is a node and ensure it's a "Basic Page."
  if (isset($variables['elements']['#node']) && $variables['elements']['#node'] instanceof \Drupal\node\NodeInterface) {
    // Check for "Basic Page" bundle.
    if ($variables['elements']['#node']->bundle() == 'page') {
      // Add a custom suggestion.
      $suggestions[] = 'my__page__custom';
    }
  }
}
```
- Step 3: Create the Custom Twig Template
 my--page--custom.html.twig
```html
<div>
  <h3 style="color:#22ff99">Hello, this is my custom page</h3>
  {{ elements.content|raw }}
</div>
```
<img width="927" alt="image" src="https://github.com/user-attachments/assets/c02b3c50-bc7c-462b-92d0-cd1969ca84db" />



<hr>

## Using hook_theme_suggestions_alter how can you add a new theme suggestion for $hook === 'user' based on the view mode ?
We start by implementing hook_theme_suggestions_alter(). This allows us to dynamically add theme suggestions based on the view mode of the user entity.
```php
/**
 * Implements hook_theme_suggestions_alter().
 */
function generated_entity_theme_suggestions_alter(array &$suggestions, array $variables, $hook) {
  // Check if we are dealing with the 'user' theme hook.
  if ($hook === 'user' && isset($variables['elements']['#view_mode'])) {
    // Retrieve the view mode.
    $view_mode = $variables['elements']['#view_mode'];
    // Add a theme suggestion specific to this view mode.
    $suggestions[] = 'user__' . $view_mode;
  }
}
```
Here, we ensure that for each view mode (e.g., full or teaser), Drupal automatically appends a new theme suggestion like user__full or user__teaser

-Step 2: Define the Theme Hooks in hook_theme()
```php
/**
 * Implements hook_theme().
 */
function generated_entity_theme() {
  return [
    'user__full' => [
      'render element' => 'elements',
    ],
    'user__teaser' => [
      'render element' => 'elements',
    ],
  ];
}
```
Here, we ensure that for each view mode (e.g., full or teaser), Drupal automatically appends a new theme suggestion like user__full or user__teaser.
- Step 3: Create Custom Templates :
* user--full.html.twig
```php
<h1 style="background-color:#99aa99"> User - Full </h1>
```
* user--teaser.html.twig
```php
<h1 style="background-color:#99aa99"> User - Teaser </h1>
```

### Final Result : 
<img width="944" alt="image" src="https://github.com/user-attachments/assets/ccaabfe2-d7ed-4195-87d7-b08140bf9d05" />

