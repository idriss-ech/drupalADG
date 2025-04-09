# Drupal + JSON API use case

## ✅ Step 1: Install Drupal with Composer
```bash
composer create-project drupal/recommended-project my_drupal_site
cd my_drupal_site
```

## ✅ Step 2: Install JSON:API module
![alt text](image.png)

## ✅ Step 3: Test JSON:API with Postman
Play with examples :

### List nodes:
```bash
GET /jsonapi/node/articlef
```
![alt text](image-1.png)
### Fields:
```bash
GET /jsonapi/node/article?fields[node--article]=title,body
```
![alt text](image-2.png)

### Include relationships:
```bash
GET /jsonapi/node/article?include=uid
```
![alt text](image-3.png)

### Sort by created date:
```bash
GET /jsonapi/node/article?sort=-created
```
![alt text](image-4.png)

### Filter by title:
```bash
GET /jsonapi/node/article?filter[title][value]=Hello
```
![alt text](image-5.png)

## ✅ Step 4: Install JSON:API Extras
```bash 
composer require 'drupal/jsonapi_extras:^3.26'
```
![alt text](image-6.png)
Then we go to /admin/config/services/jsonapi-extras to configure:

- Custom paths
- Rename resource types
- Enable/disable fields
- Set human-friendly resource names ...

![alt text](image-7.png)
![alt text](image-8.png)

## ✅ Step 5: Define a custom field using hook_entity_base_field_info()
### 1. Create a custom module, e.g., my_custom_fields.
```bash
cd web/modules/custom
mkdir my_custom_fields
touch my_custom_fields.info.yml
```
### my_custom_fields.info.yml

``` yaml
name: 'My Custom Fields'
type: module
description: 'Adds custom base fields to entities.'
core_version_requirement: ^10
package: Custom
dependencies:
  - drupal:node
```

### my_custom_fields.module

```php
<?php

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\node\Entity\Node;

/**
 * Implements hook_entity_base_field_info().
 */
function my_custom_fields_entity_base_field_info(EntityTypeInterface $entity_type) {
  $fields = [];

  // Add a field to node entities only.
  if ($entity_type->id() === 'node') {
    $fields['custom_text'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Custom Text'))
      ->setDescription(t('A custom text field for demonstration.'))
      ->setSettings([
        'max_length' => 255,
      ])
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 100,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  }

  return $fields;
}

```
Enable the module and test using Postman : 
![alt text](image-9.png)


# Task
For this exercise, we will integrate Next.js with a Drupal backend. The objective is to create a working Next.js project that fetches data from the Drupal backend.

Our tasks are as follows:

1. Set up a Next.js project with a Drupal backend.

2. Create a homepage displaying a list of links to another page that shows a list of articles.

3. Display a list of articles on the articles page (no need for a detailed view).

## Add Next.js module

```bash 
composer require drupal/next
```
## Enable Next.js and Next.js JSON:API modules

![alt text](image-10.png)

## Configure path aliases for content types
![alt text](image-11.png)

## Create a new Next.js project using the starter
```bash
npx create-next-app -e https://github.com/chapter-three/next-drupal-basic-starter
```
## Connect Drupal
![alt text](image-12.png)

## Final Result
The content for the Articles, Hero section, and Team items is fetched from Drupal.

![alt text](image-15.png)
### 1. Home Page
![alt text](image-14.png)
### 2. Article Detail
![alt text](image-13.png)
