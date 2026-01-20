<?php
declare(strict_types=1);

/**
 * @file
 * Provide views data that isn't tied to any other module.
 */

/**
 * Implements hook_views_data_alter().
 */
function views_pdf_views_data_alter(&$data) {
  $data['views']['page_break'] = [
    'title' => t('Page Break'),
    'help' => t('Add page break to the PDF rendering.'),
    'field' => [
      'id' => 'page_break',
    ],
  ];
}
