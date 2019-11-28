<?php
namespace Drupal\ftf_parcelling\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;

class AreaImportService {

  /** @var \Drupal\Core\Database\Connection */
  protected $database;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Hardcoded array should be changed into something dynamic
   */
  protected $mapAreaTypes = [
    'bolig' => 13,
    'erhverv' => 19,
    'storparcel' => 24,
  ];

  /**
   * ParcelImportService constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(Connection $connection) {
    $this->database = $connection;
    $this->logger = \Drupal::logger('ftf_area_import');
  }

  /**
   * Executes the area import
   *
   * @return array
   */
  public function startAreaImport() {

    $this->logger->notice('Starting import');

    $area_identifiers = $this->fetchCurrentAreaIdentifiers();
    $area_data = $this->getDataFromFeed('https://webkort.favrskov.dk/spatialmap?page=grundsalg-get-omraade');

    foreach ($area_data as $area) {
      if($area->status != 'gældende') {
        continue;
      }

      if($area->type != '' && $area->bynavn != '') {
        if(!isset($area_identifiers[$area->type]) || !isset($area_identifiers[$area->type][$area->bynavn])) {
          $this->logger->notice('Create node', ['type' => $area->type, 'bynavn' => $area->bynavn]);
          /** @var Node $node */
          $node = $this->createAreaNode($area);
          if($node) {
            $area_identifiers[$node->field_area_type_identifier->value][$node->field_area_identifier->value] = $node->id();
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
   * Create a new node of the type "Area"
   *
   * @param $area
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   */
  private function createAreaNode($area) {
    //todo: change to something more dynamic
    $parent_id = false;
    if(isset($this->mapAreaTypes[$area->type])) {
      $parent_id = $this->mapAreaTypes[$area->type];
    }
    if($parent_id > 0) {
      try {
        $node = Node::create([
          'uid' => 1,
          'type' => 'area',
          'title' => $area->bynavn,
          'status' => 0
        ]);
        $node->set('field_area_type_identifier', (string) $area->type);
        $node->set('field_area_identifier', (string) $area->bynavn);
        $node->set('field_parent', $parent_id);

        $node->save();
        return $node;
      } catch (EntityStorageException $e) {
        $this->logger->error('Failed to create node', ['type' => $area->type, 'bynavn' => $area->bynavn]);
      }
    } else {
      $this->logger->error('Could not find parent node', ['type' => $area->type]);
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


}
