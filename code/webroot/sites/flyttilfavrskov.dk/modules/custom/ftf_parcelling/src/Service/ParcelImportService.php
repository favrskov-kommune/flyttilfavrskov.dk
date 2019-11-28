<?php
namespace Drupal\ftf_parcelling\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class ParcelImportService {

  /** @var \Drupal\Core\Database\Connection */
  protected $database;

  protected $statusMap = [
    'Ledig' => 'available',
    'Reserveret' => 'reserved',
    'Solgt' => 'sold',
    'Afventer' => 'pending',
  ];

  protected $typeMap = [
    'Byggegrund - parcelhus' => 'bolig',
    'Byggegrund - erhverv' => 'erhverv',
    'Byggegrund - storparcel' => 'storparcel'
  ];

  protected $logger;

  /**
   * ParcelImportService constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(Connection $connection) {
    $this->database = $connection;
    $this->logger = \Drupal::logger('ftf_parcel_import');
  }

  public function startParcelImport() {

//    $this->deleteAllParcelParagraphs();

    $this->logger->notice('Starting import');
    $parcelling_identifiers = $this->fetchCurrentParcellingIdentifiers();
    $parcel_identifiers = $this->fetchCurrentParcelIdentifiers();
    $parcel_data = $this->getAllParcelData();
//    $parcel_data = [];


    foreach ($parcel_data as $parcel) {
      $identifier = trim($parcel->vejnavn.' '.$parcel->husnr);

      if(!isset($this->typeMap[$parcel->grund_type])) {
        $this->logger->notice($identifier.': Unknown parcel type: '.$parcel->grund_type);
        continue;
      }

      if ($identifier != '') {
        if(!isset($parcel_identifiers[$identifier])) {
          $parcelling_identifier = $parcel->udstyk_navn;
          if(isset($parcel->omraade) && $parcel->omraade != '') {
            $parcelling_identifier = $parcelling_identifier.' - '.$parcel->omraade;
          }

          if (isset($parcelling_identifiers[$this->typeMap[$parcel->grund_type]]) && isset($parcelling_identifiers[$this->typeMap[$parcel->grund_type]][$parcel->bynavn]) && isset($parcelling_identifiers[$this->typeMap[$parcel->grund_type]][$parcel->bynavn][$parcelling_identifier])) {
            // create parcel
            $this->logger->notice($identifier.': Creating');
            $this->createParcel(['parcelling_id' => $parcelling_identifiers[$this->typeMap[$parcel->grund_type]][$parcel->bynavn][$parcelling_identifier], 'parcel_identifier' => $identifier, 'data' => $parcel]);
          } else {
            // unknown parcelling - skipping import
            $this->logger->notice($identifier.': Unknown parcelling: '.$this->typeMap[$parcel->grund_type].' - '.$parcel->bynavn.' - '.$parcelling_identifier);
          }
        } else {
//          $this->logger->notice($identifier.': Updating');
          $this->updateParcel(['parcel_id' => $parcel_identifiers[$identifier], 'parcel_identifier' => $identifier, 'data' => $parcel]);
          //remove processed parcel from $parcel_identifiers
          unset($parcel_identifiers[$identifier]);
        }
      } else {
        $this->logger->error('Invalid identifier', $parcel);
      }
    }

    //any parcels left in $parcel_identifiers was not found in the feed and should be deleted
    foreach ($parcel_identifiers as $parcel_identifier => $paragraph_id) {
      $this->logger->notice($identifier.': Deleting');
      $this->deleteParcel(['paragraph_id' => $paragraph_id]);
    }

    $this->logger->notice('Import completed');

    return [
      'status' => true,
      'message' => 'this is the succes message',
    ];

  }

  public function createParcel($data) {
    $parent_id = $data['parcelling_id'];
    if ($parent_id > 0) {
      /** @var Node $node */
      $node = Node::load($parent_id);
      if($node) {
        $parcel_paragraph = $this->createParcelParagraph($data['data']);
        if ($parcel_paragraph) {
          try {
            $parcels = array_column($node->get('field_parcelling_parcels')->getValue(), 'target_id');
            if(!in_array($parcel_paragraph->id(), $parcels)) {
              $node->get('field_parcelling_parcels')->appendItem($parcel_paragraph);
              $node->save();
            } else {
              $this->logger->notice($data['parcel_identifier'].': Parcel already exists on node '.$parent_id);
            }
          } catch (EntityStorageException $e) {
            $this->logger->error($data['parcel_identifier'].': Failed to add parcel to node '.$parent_id);
          }
        }
      } else {
        $this->logger->error($data['parcel_identifier'].': Failed to load parent node '.$parent_id);
      }
    }
  }

  private function createParcelParagraph($data) {
    try {
      //check that it doesn't exist already even though it shouldn't
      $identifier = trim($data->vejnavn.' '.$data->husnr);
      $pids = \Drupal::entityQuery('paragraph')
        ->condition('field_parcel_address', $identifier)
        ->execute();
      if(count($pids) > 0) {
        $this->logger->notice($identifier.': Parcel already exists - skip create');
        return Paragraph::load(reset($pids));
      }

      $entity = Paragraph::create([
        'uid' => 1,
        'type' => 'parcel',
      ]);
      if (isset($this->statusMap[$data->salg_status])) {
        $status = $this->statusMap[$data->salg_status];
      } else {
        $status = $this->statusMap['unknown'];
        $this->logger->error($identifier.': Unknown status '.$data->salg_status);

      }
      $entity->set('field_parcel_address', $identifier);
      $entity->set('field_parcel_area', (int) $data->grundareal);
      $entity->set('field_parcel_price', (intval($data->kvm_pris) * intval($data->grundareal)));
      $entity->set('field_parcel_status', $status);

      $entity->save();
      return $entity;
    } catch (EntityStorageException $e) {
      $this->logger->error($identifier.': Failed to create parcel');
      return null;
    }
  }
  public function updateParcel($data) {
//    kint($data);die;

  }

  public function deleteParcel($data) {
    try {
      /** @var Paragraph $paragraph */
      $paragraph = Paragraph::load($data['paragraph_id']);
      if ($paragraph) {
        /** @var Node $node */
        $node = $paragraph->getParentEntity();
        if($node) {
          //first, remove paragraph from node
          foreach ($node->get('field_parcelling_parcels')->getValue() as $key => $value) {
            if ($value['target_id'] == $data['paragraph_id']) {
              $node->get('field_parcelling_parcels')->removeItem($key);
            }
          }
          $node->save();
        } else {
          $this->logger->error('Unable to find parent entity for parcel '.$data['paragraph_id']);
        }

        //delete the paragraph
        $paragraph->delete();

      } else {
        $this->logger->error('Unable to find parcel '.$data['paragraph_id']);
      }

    } catch (EntityStorageException $e) {
      $this->logger->error('Unable to delete parcel '.$data['paragraph_id']);
    }
  }

  private function getAllParcelData() {
    $erhverv = $this->getDataFromFeed('https://webkort.favrskov.dk/spatialmap?page=grundsalg-get-erhverv');
    $bolig = $this->getDataFromFeed('https://webkort.favrskov.dk/spatialmap?page=grundsalg-get-parcel');
    $storparcel = $this->getDataFromFeed('https://webkort.favrskov.dk/spatialmap?page=grundsalg-get-storparcel');

    $return = [];
    foreach ($bolig as $item) {
      $return[] = $item;
    }
    foreach ($erhverv as $item) {
      $return[] = $item;
    }
    foreach ($storparcel as $item) {
      $return[] = $item;
    }

    return $return;
  }

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
  protected function fetchCurrentParcelIdentifiers() {
    $query = $this->database->select('paragraph__field_parcel_address', 'p')
      ->fields('p', ['field_parcel_address_value', 'entity_id']);
    return $query->execute()->fetchAllKeyed();
  }

  protected function deleteAllParcelParagraphs() {
    $pids = \Drupal::entityQuery('paragraph')
      ->condition('type', 'parcel')
      ->execute();
    $paragraphs = Paragraph::loadMultiple($pids);
    foreach ($paragraphs as $paragraph) {
      $paragraph->delete();
    }
  }

}
