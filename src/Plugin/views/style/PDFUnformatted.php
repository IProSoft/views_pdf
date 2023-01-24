<?php
declare(strict_types=1);

namespace Drupal\views_pdf\Plugin\views\style;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\views\Annotation\ViewsStyle;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\String\match;

//  *   theme = "views_view_pdf_unformatted",

/**
 * Style plugin to render a PDF Unformatted display style.
 *
   * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "pdf_unformatted",
 *   title = @Translation("PDF Unformatted"),
 *   help = @Translation("Outputs the view as a PDF Unformatted style."),
 *   register_theme = FALSE,
 *   display_types={"pdf"}
 * )
 */
class PDFUnformatted extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /** @var \Symfony\Component\HttpFoundation\Request */
  protected Request $request;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
  }

  public function getStyle() : string {
    return 'pdf_unformatted';
  }

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Http\RequestStack $requestStack
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RequestStack $requestStack
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->request = $requestStack->getCurrentRequest();
  }

  /**
   * {@inheritDoc}
   */
  protected function defineOptions() : array {
    $options = parent::defineOptions();

    $options['algo'] = [
      'default' => 'pdf'
    ];

    return $options;
  }

  /**
   * {@inheritDoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) : void {
    parent::buildOptionsForm($form, $form_state);

    $form['algo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Something'),
      '#default_value' => $this->options['algo'] ?? 'nada',
      '#description' => $this->t('Testing all the options and setup views display')
    ];
  }

  /**
   * Block render phase on views preview.
   *
   * @return array
   */
  protected function previewRender() : array {
    $message = $this->t("PDF cannot be viewed as a live preview.");
    $this->messenger()->addWarning($message);

    return ['#markup' => $message];
  }

  /**
   * Work the render fields for unformatted PDF.
   *
   * @return array
   */
  protected function renderBuild() : array {

    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $this->view->rowPlugin->render($row);
    }

    return  [
      '#view' => $this->view,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function render() : array {

    $render = match($this->request->get('_route')) {
      'entity.view.preview_form' => $this->previewRender(),
      default => $this->renderBuild(),
    };

    return $render;
  }

}
