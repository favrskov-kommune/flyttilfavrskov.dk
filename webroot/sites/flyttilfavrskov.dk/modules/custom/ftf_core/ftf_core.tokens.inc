<?php

/**
 * Implements hook_token_info().
 */
function ftf_core_token_info()
{
  return [
    'types' => [
      'ftf_core' => [
        'name' => 'FTF tokens',
        'description' => '',
      ],
    ],
    'tokens' => [
      'ftf_core' => [
        'ftf_core_primary_description' =>[
          'name' => 'FTF Core Primary Description',
          'description' => 'Calculates the primary meta description for nodes',
        ],
      ],
    ],
  ];
}


/**
 * Implements hook_tokens().
 */
function ftf_core_tokens($type, $tokens, array $data, array $options, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata)
{
  $replacements = [];

  if ($type == 'ftf_core') {
    if (!empty($data['node'])) {
      /** @var \Drupal\node\NodeInterface $node */
      $node = $data['node'];

      foreach ($tokens as $name => $original) {
        switch ($name) {
          case 'ftf_core_primary_description':

            $description = '';

            if($node->hasField('field_teaser') && !$node->field_teaser->isEmpty()) {
              $description = strip_tags($node->field_teaser->value);
            } else if($node->hasField('field_list_description') && !$node->field_list_description->isEmpty()) {
              $description = strip_tags($node->field_list_description->value);
            } else if($node->hasField('field_header_subline') && !$node->field_header_subline->isEmpty()) {
              $description = strip_tags($node->field_header_subline->value);
            }

            $replacements[$original] = $description;
            break;
        }
      }
    }
  }

  return $replacements;
}

