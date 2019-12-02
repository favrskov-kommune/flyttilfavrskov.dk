<?php
namespace Drupal\ftf_parcelling\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class ParcellingImportService {

  /** @var \Drupal\Core\Database\Connection */
  protected $database;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * ParcelImportService constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(Connection $connection) {
    $this->database = $connection;
    $this->logger = \Drupal::logger('ftf_parcelling_import');
  }

  /**
   * Executes the parcelling import
   *
   * @return array
   */
  public function startParcellingImport() {

    $this->deleteAllParcellingNodes();die;

    $this->logger->notice('Starting import');

    $parcelling_identifiers = $this->fetchCurrentParcellingIdentifiers();
    $area_data = $this->getDataFromFeed('https://webkort.favrskov.dk/spatialmap?page=grundsalg-get-omraade');

    foreach ($area_data as $area) {
      if($area->status != 'gældende') {
        continue;
      }

      if($area->type != '' && $area->bynavn != '' && $area->udstyk_navn != '') {
        $identifier = $area->udstyk_navn;
        if(isset($area->omraade) && $area->omraade != '') {
          $identifier = $identifier.' - '.$area->omraade;
        }
        if(!isset($parcelling_identifiers[$area->type]) || !isset($parcelling_identifiers[$area->type][$area->bynavn]) || !isset($parcelling_identifiers[$area->type][$area->bynavn][$identifier])) {
          $this->logger->notice('Create node', ['type' => $area->type, 'bynavn' => $area->bynavn, 'identifier' => $identifier]);
          /** @var Node $node */
          $node = $this->createParcellingNode($identifier, $area);
          if($node) {
            $parcelling_identifiers[$area->type][$area->bynavn][$node->field_parcelling_identifier->value] = $node->id();
          }
        }
      }
    }

    $this->logger->notice('Import completed');

    return [
      'status' => true,
      'message' => 'this is the succes message',
    ];

  }

  /**
   * Create a new node of the type "Parcelling"
   *
   * @param $area
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   */
  private function createParcellingNode($identifier, $area) {

    $area_identifiers = $this->fetchCurrentAreaIdentifiers();
    $parent_id = false;
    if(isset($area_identifiers[$area->type]) && isset($area_identifiers[$area->type][$area->bynavn])) {
      $parent_id = $area_identifiers[$area->type][$area->bynavn];
    }
    if($parent_id > 0) {
      try {
        $node = Node::create([
          'uid' => 1,
          'type' => 'parcelling',
          'status' => 0
        ]);
        $node->set('title', $identifier);
        $node->set('field_parcelling_identifier', $identifier);
        $node->set('field_parent', $parent_id);

        $node->save();
        return $node;
      } catch (EntityStorageException $e) {
        $this->logger->error('Failed to create node', ['type' => $area->type, 'bynavn' => $area->bynavn]);
      }
    } else {
      $this->logger->error('Could not find area - area import should be run first', ['type' => $area->type]);
    }
    return null;
  }



  /**
   * Get data from external feed
   *
   * @param $feed
   *
   * @return mixed
   */
  private function getDataFromFeed($feed) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $feed);
    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);
    return $obj;
  }

  /**
   * Fetch all known parcelling identifiers as an array
   *
   * @return mixed
   */
  protected function fetchCurrentParcellingIdentifiers() {
    $query = $this->database->select('node__field_area_identifier', 'n');
    $query->join('node__field_area_type_identifier', 't', 'n.entity_id = t.entity_id');
    $query->join('node__field_parent', 'p', 'n.entity_id = p.field_parent_target_id');
    $query->join('node__field_parcelling_identifier', 'pi', 'p.entity_id = pi.entity_id');
    $query->fields('n', ['field_area_identifier_value']);
    $query->fields('t', ['field_area_type_identifier_value']);
    $query->fields('pi', ['entity_id', 'field_parcelling_identifier_value']);
    $results = $query->execute();

    $return = [];
    foreach ($results as $result) {
      $return[$result->field_area_type_identifier_value][$result->field_area_identifier_value][$result->field_parcelling_identifier_value] = $result->entity_id;
    }

    return $return;
  }

  /**
   * Fetch all known parcel identifiers as an array where key is the address
   * and value is the paragraph id
   *
   * @return mixed
   */
  protected function fetchCurrentAreaIdentifiers() {
    $query = $this->database->select('node__field_area_identifier', 'n');
    $query->join('node__field_area_type_identifier', 't', 'n.entity_id = t.entity_id');
    $query->fields('n', ['entity_id', 'field_area_identifier_value']);
    $query->fields('t', ['field_area_type_identifier_value']);
    $results = $query->execute();

    $return = [];
    foreach ($results as $result) {
      $return[$result->field_area_type_identifier_value][$result->field_area_identifier_value] = $result->entity_id;
    }

    return $return;
  }

  protected function deleteAllParcellingNodes() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'parcelling')
      ->execute();
    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $node) {
      $node->delete();
    }

  }


}
