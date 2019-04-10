<?php

namespace Drupal\testimonial_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Plugin implementation of the 'testimonial' formatter.
 *
 * @FieldFormatter(
 *   id = "testimonial",
 *   label = @Translation("Testimonial"),
 *   field_types = {
 *     "testimonial"
 *   }
 * )
 */
class TestimonialFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'image_style' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $image_styles = image_style_options(FALSE);
    $description_link = Link::fromTextAndUrl(
      $this->t('Configure Image Styles'),
      Url::fromRoute('entity.image_style.collection')
    );
    $element['image_style'] = [
      '#title' => t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable(),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');
    if (isset($image_styles[$image_style_setting])) {
      $summary[] = t('Image style: @style', ['@style' => $image_styles[$image_style_setting]]);
    }
    else {
      $summary[] = t('Original image');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }

    return $elements;
  }

  protected function viewValue(FieldItemInterface $item) {
    $image_style_setting = $this->getSetting('image_style');
    $image = FALSE;
    if($item->image) {
      $image = File::load($item->image);
    }
    return [
      '#theme' => 'testimonial',
      '#content' => [
        'lastname' => $item->lastname,
        'firstname' => $item->firstname,
        'country' => $item->country,
        'content' => [
          '#type' => 'processed_text',
          '#text' => $item->content,
          '#format' => $item->content_format,
        ],
        'image' => $image ? [
          '#theme' => 'image_style',
          '#style_name' => $image_style_setting,
          '#uri' => $image->getFileUri(),
        ] : NULL,
      ]
    ];
  }

}
