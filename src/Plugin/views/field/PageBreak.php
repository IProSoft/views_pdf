<?php
declare(strict_types=1);

namespace Drupal\views_pdf\Plugin\views\field;

use Drupal\Component\Utility\Xss;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Render\ViewsRenderPipelineMarkup;
use Drupal\views\ResultRow;

/**
 * The page break plugin for PDF page display.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("page_break")
 */
class PageBreak extends FieldPluginBase {

function query() {
    // Override parent::query() and don't alter query.
    $this->field_alias = 'pdf_page_break_' . $this->position;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['last_row'] = ['default' => FALSE];
    $options['every_nth'] = ['default' => 1];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    unset($form['label']);
    unset($form['element_type_enable']);
    unset($form['element_type']);
    unset($form['element_class_enable']);
    unset($form['element_class']);
    unset($form['element_label_class_enable']);
    unset($form['element_label_class']);
    unset($form['element_wrapper_type_enable']);
    unset($form['element_wrapper_type']);
    unset($form['element_wrapper_class_enable']);
    unset($form['element_wrapper_class']);
    unset($form['element_label_type_enable']);
    unset($form['element_label_type']);
    unset($form['element_default_classes']);
    unset($form['element_label_colon']);
    unset($form['exclude']);
    unset($form['custom_label']);
    unset($form['style_settings']);
    unset($form['alter']);

    // dd($form);

    $form['last_row'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exclude from last row'),
      '#default_value' => $this->options['last_row'],
      '#description' => $this->t('Check this box to not add new page on last row.'),
    ];
    $form['every_nth'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Insert break after how many rows?'),
      '#size' => 10,
      '#default_value' => $this->options['every_nth'],
      '#element_validate' => ['element_validate_integer_positive'],
      '#description' => $this->t('Enter a value greater than 1 if you want to have multiple rows on one page')
    );
  }

  /**
   * {@inheritdoc}
   */
  function render(ResultRow $values) {
    // Row index starts from 0 so substract 1 one from result count.
    $last_row = $this->view->row_index == count($this->view->result) - 1;
    // Calculate if 'every_nth' rule matches for this row.
    $add_pagebreak = ($this->view->row_index + 1) % $this->options['every_nth'] == 0;

    // Last row setting takes priority over 'every_nth' rule if we're infact
    // rendering last row.
    if (($this->options['last_row'] === FALSE || !$last_row) && $add_pagebreak) {
      return ViewsRenderPipelineMarkup::create(Xss::filterAdmin('<br pagebreak="true" />'));
    }

    return '';
  }

  protected function allowAdvancedRender() {
    return FALSE;
  }

}
