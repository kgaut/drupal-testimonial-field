<?php

namespace Drupal\testimonial_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'testimonial' widget.
 *
 * @FieldWidget(
 *   id = "testimonial",
 *   label = @Translation("Testimonial"),
 *   field_types = {
 *     "testimonial"
 *   }
 * )
 */
class TestimonialWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['image'] = [
      '#title' => 'Image',
      '#type' => 'managed_file',
      '#default_value' => isset($items[$delta]->image) ? [$items[$delta]->image] : NULL,
      '#upload_location'  => $items[$delta]->getUploadLocation(),
      '#multiple' => FALSE,
      '#description' => t('Allowed extensions: gif png jpg jpeg'),
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
    ];

    $element['lastname'] = [
      '#title' => 'Lastname',
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->lastname) ? $items[$delta]->lastname : NULL,
      '#size' => 60,
      '#maxlength' => 255,
    ];

    $element['firstname'] = [
      '#title' => 'Lastname',
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->firstname) ? $items[$delta]->firstname : NULL,
      '#size' => 60,
      '#maxlength' => 255,
    ];

    $element['country'] = [
      '#title' => 'Lastname',
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->country) ? $items[$delta]->country : NULL,
      '#size' => 60,
      '#maxlength' => 255,
    ];

    $element['content'] = [
      '#type' => 'text_format',
      '#format' => isset($items[$delta]->content_format) ? $items[$delta]->content_format : 'full_html',
      '#default_value' => isset($items[$delta]->content) ? $items[$delta]->content : '',
      '#title' => t('Answer'),
      '#rows' => 5,
      '#attached' => [
        'library' => ['text/drupal.text'],
      ],
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $content = $value['content'];
      $value['content_format'] = $content['format'];
      $value['content'] = $content['value'];
    }
    return parent::massageFormValues($values, $form, $form_state);
  }

}
