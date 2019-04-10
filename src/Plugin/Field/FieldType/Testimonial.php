<?php

namespace Drupal\testimonial_field\Plugin\Field\FieldType;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;

/**
 * Plugin implementation of the 'testimonial' field type.
 *
 * @FieldType(
 *   id = "testimonial",
 *   label = @Translation("Testimonial"),
 *   description = @Translation("Testimonial field type"),
 *   default_widget = "testimonial",
 *   default_formatter = "testimonial"
 * )
 */
class Testimonial extends FieldItemBase {

  public static function defaultFieldSettings() {
    return [
        'file_directory' => 'public://',
      ] + parent::defaultFieldSettings();
  }

  public static function validateDirectory($element, FormStateInterface $form_state) {
    // Strip slashes from the beginning and end of $element['file_directory'].
    $value = trim($element['#value'], '\\/');
    $form_state->setValueForElement($element, $value);
  }

  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];
    $settings = $this->getSettings();

    $element['file_directory'] = [
      '#type' => 'textfield',
      '#title' => t('File directory'),
      '#default_value' => $settings['file_directory'],
      '#description' => t('Optional subdirectory within the upload destination where files will be stored. Do not include preceding or trailing slashes.'),
      '#element_validate' => [[get_class($this), 'validateDirectory']],
      '#weight' => 3,
    ];
    return $element;
  }

  public function getUploadLocation($data = []) {
    return static::doGetUploadLocation($this->getSettings(), $data);
  }

  public static function doGetUploadLocation(array $settings, $data = []) {
    $destination = trim($settings['file_directory'], '/');
    $destination = PlainTextOutput::renderFromHtml(\Drupal::token()->replace($destination, $data));
    return $destination;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['image'] = DataReferenceTargetDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Picture file ID'));

    $properties['lastname'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Lastname'));

    $properties['firstname'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Firstname'));

    $properties['country'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Country'));

    $properties['content'] = DataDefinition::create('string')
      ->setLabel(t('Content'));

    $properties['content_format'] = DataDefinition::create('filter_format')
      ->setLabel(t('Content text format'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'image' => [
          'description' => 'Image file id',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE
        ],
        'lastname' => [
          'description' => 'Lastname',
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'firstname' => [
          'description' => 'Firstname',
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'country' => [
          'description' => 'Country',
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'content' => [
          'type' => 'text',
          'size' => 'big',
          'not null' => FALSE,
        ],
        'content_format' => [
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('image')->getValue();
    return $value === NULL || $value === '';
  }

}
