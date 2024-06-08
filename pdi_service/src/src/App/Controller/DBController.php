<?php

// alter table onboarding add column data_error text  DEFAULT NULL;
// alter table onboarding add column count_error tinyint  DEFAULT 0;
// alter table onboarding modify state tinyint  DEFAULT NULL;
// CREATE INDEX xpressid_validation_id_idx ON onboarding (xpressid_validation_id);
// alter table onboarding add column signature_request_id varchar(255) NULL DEFAULT NULL;
// create index signature_request_id_idx on onboarding(signature_request_id);

namespace METRIC\App\Controller;

use METRIC\App\Config\AppConfig;
use METRIC\App\Service\DBConnection;
use METRIC\App\Controller\BaseController;

class DBController extends BaseController
{
  private $response = [];
  private $database;

  public function __construct()
  {

    parent::__construct();

    $this->database = new DBConnection(
      AppConfig::getInstance()->getValue("DB_HOST"),
      AppConfig::getInstance()->getValue("DB_USER"),
      AppConfig::getInstance()->getValue("DB_PASSWORD"),
      AppConfig::getInstance()->getValue("DB_NAME"),
    );
  }

  public function insertCaseToAtfinity(array $data): array
  {
    $this->logInfo(json_encode($data));

    $query = sprintf(
      "INSERT INTO %s.pdi (atfinity_case_id, atfinity_instance_id, atfinity_instance_fields_id, language, document_type, hash, hash_key) VALUES ('%d', '%d', '%s', '%s', '%s', '%s', '%s')",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["atfinity_case_id"],
      $data["atfinity_instance_id"],
      $data["atfinity_instance_fields_id"],
      $data["language"],
      $data["document_type"],
      $data["hash"],
      $data["hash_key"],
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  public function getCaseByHash(string $hash): array
  {
    $query = sprintf(
      "SELECT  * FROM %s.pdi WHERE hash='%s'",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $hash
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = $result;
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      if (count($rows) > 0) {
        $this->logInfo($rows[0]);
        $this->response['error'] = false;
        $this->response['info'] = $rows[0];
      } else {
        $this->logInfo("case no encontrado");
        $this->response['error'] = true;
        $this->response['info'] = $rows;
      }
      
    }

    mysqli_free_result($result);

    $this->database->close();
    return $this->response;
  }

  public function getHashKeyByHash(string $hash): array
  {
    $query = sprintf(
      "SELECT  `hash`, `hash_key` FROM %s.pdi WHERE hash='%s'",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $hash
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->response['error'] = true;
      $this->response['info'] = $result;
      $this->logError("response: false");
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      if (count($rows) > 0) {
        $this->logInfo($rows[0]);
        $this->response['error'] = false;
        $this->response['info'] = $rows[0];
      } else {
        $this->logInfo("hash no encontrado");
        $this->response['error'] = true;
        $this->response['info'] = $rows;
      }

      mysqli_free_result($result);
      $this->database->close();
    }
   
    return $this->response;
  }

  public function insertPdiCase(array $data)
  {
    $this->logInfo(json_encode($data));
  }

  public function read(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = 'SELECT * FROM ' . AppConfig::getInstance()->getValue("DB_NAME") . '.onboarding WHERE hash = "' . $data["hash"] . '"';

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = null;
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->logInfo($rows);
      if (!empty($rows)) {
        $this->response['error'] = false;
        $this->response['info'] = $rows[0];
      } else {
        $this->response['error'] = true;
        $this->response['info'] = null;
      }
    }

    $this->database->close();

    return $this->response;
  }

  public function send(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = 'INSERT INTO ' . AppConfig::getInstance()->getValue("DB_NAME") . '.onboarding (`atfinity_case_id`, `atfinity_instance_id`, `atfinity_instance_fields_id` ,`odoo_id`, `language`, `document_type`, `state`, `date`, `last_update`, `hash`) VALUES ("' . $data["case_id"] . '", "' . $data["instance_id"] . '", "' . $data["instance_fields_id"] . '", "' . $data["odoo_id"] . '", "' . $data["language"] . '", "' . $data["document_type"] . '", "' . $data["state"] . '", "' . $data["date"] . '", "' . $data["last_update"] . '", "' . $data["hash"] . '")';

    $this->response['error'] = !$this->database->query($query);
    $this->database->close();

    return $this->response;
  }

  public function update(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET state=%d, last_update='%s', library_document_id='%s', xpressid_validation_id='%s', document_type=%s WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["state"],
      $data["last_update"],
      $data["library_document_id"],
      $data["xpressid_validation_id"],
      ($data["document_type"]) ? sprintf("'%s'", $data["document_type"]) : 'document_type',
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  public function updateState(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET state=%d, last_update='%s', webhook_timestamp=%d WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["state"],
      $data["last_update"],
      $data["webhook_timestamp"],
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $error = ($this->response['error']) ? "true" : "false";
    $this->logInfo("error: " . $error);

    $this->database->close();
    return $this->response;
  }

  public function updateStateBoidasToAtfinity(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET state=%d, last_update='%s', webhook_timestamp=%d, boidas_to_atfinity=%d WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["state"],
      $data["last_update"],
      $data["webhook_timestamp"],
      $data["boidas_to_atfinity"],
      $data["case_id"],
      $data["instance_id"],
    );

    $this->response['error'] = !$this->database->query($query);

    $error = ($this->response['error']) ? "true" : "false";
    $this->logInfo("error: " . $error);

    $this->database->close();
    return $this->response;
  }

  public function updateAtfinityInstanceFieldsId(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET atfinity_instance_fields_id=%d, last_update='%s' WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["atfinity_instance_fields_id"],
      $data["last_update"],
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  public function getDataWhereAtfinity(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "SELECT  * FROM %s.onboarding WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["case_id"],
      $data["instance_id"]
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->logInfo($rows[0]);
      $this->response['error'] = false;
      $this->response['info'] = $rows[0];
    }


    $this->database->close();
    return $this->response;
  }

  public function getDataWhereValidationId(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "SELECT  * FROM %s.onboarding WHERE xpressid_validation_id='%s'",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["validation_id"]
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->logInfo($rows[0]);
      $this->response['error'] = false;
      $this->response['info'] = $rows[0];
    }


    $this->database->close();
    return $this->response;
  }

  public function getDataWhereSignatureRequestId(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "SELECT  * FROM %s.onboarding WHERE signature_request_id='%s'",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["signature_request_id"]
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->logInfo($rows[0]);
      $this->response['error'] = false;
      $this->response['info'] = $rows[0];
    }


    $this->database->close();
    return $this->response;
  }

  public function getDataWhereState(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "SELECT  * FROM %s.onboarding WHERE state in (%s) AND webhook_timestamp <= unix_timestamp(now()) AND boidas_to_atfinity = 0",
      AppConfig::getInstance()->getValue("DB_NAME"),
      implode(',', $data["state"])
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      //$this->logInfo($rows);
      $this->response['error'] = false;
      $this->response['info'] = $rows;
    }


    $this->database->close();
    return $this->response;
  }

  public function updateDataError(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET data_error='%s' WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["data_error"],
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  public function resetCountError(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET count_error=0 WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  public function updateCountError(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET count_error=count_error+1 WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["case_id"],
      $data["instance_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  /*
   * Obtiene los registros de la tabla onboarding susceptibles de operación retargeting
   */
  public function getRetargetingCasesFromOnboarding()
  {
    $query = 'SELECT * FROM ' . AppConfig::getInstance()->getValue("DB_NAME") . '.onboarding WHERE state not in (3, 4, -1, -4) and retargeting > -1';

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
        $this->logInfo("id: " . $row["id"] . " - " . "atfinity_case_id: " . $row["atfinity_case_id"]);
      }
      $this->response['error'] = false;
      $this->response['info'] = $rows;
    }


    $this->database->close();

    return $this->response;
  }

  public function getRetargetingByOnboardingIdAndCaseId(array $data)
  {
    $query = sprintf(
      "SELECT  * FROM %s.retargeting WHERE onboarding_id=%d AND atfinity_case_id=%d AND counter < 6 AND counter > -1",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["onboarding_id"],
      $data["atfinity_case_id"]
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->response['error'] = false;
      $this->response['info'] = $rows;
    }

    $this->database->close();

    return $this->response;
  }

  /*
   * append case from onboarding table in retargeting table
   */
  public function saveCaseInRetargetingTable(array $case)
  {
    $date = new \DateTime();
    $date = $date->format("Y-m-d H:i:s");

    $nextDate = new \DateTime();
    $nextDate->add(new \DateInterval('PT10M'));
    $nextDate = $nextDate->format("Y-m-d H:i:s");

    $query = 'INSERT INTO ' . AppConfig::getInstance()->getValue("DB_NAME") . '.retargeting (`onboarding_id`, `atfinity_case_id`, `date`, `last_updated`, `next_update`) VALUES ("' . $case["id"] . '", "' . $case["atfinity_case_id"] . '", "' . $date . '", "' . $date . '", "' . $nextDate . '")';

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->logError($this->response['info']);
    } else {
      $this->logInfo($result);
    }

    $this->database->close();

    return $result;
  }

  public function updateRetargetingField(array $data)
  {
    $query = sprintf(
      "UPDATE %s.onboarding SET retargeting=%d WHERE atfinity_case_id=%d AND atfinity_instance_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["retargeting"],
      $data["atfinity_case_id"],
      $data["atfinity_instance_id"]
    );
    $result = $this->database->query($query);
    return $result;
  }

  public function updateCounterField(array $data)
  {

    $counter = $data['counter'] + 1;

    $query = sprintf(
      "UPDATE %s.retargeting SET counter=%d WHERE id=%d AND atfinity_case_id=%d AND onboarding_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $counter,
      $data["retargeting_id"],
      $data["atfinity_case_id"],
      $data["onboarding_id"]
    );

    $result = $this->database->query($query);
    return $result;
  }

  public function setCounterField(array $data)
  {
    $query = sprintf(
      "UPDATE %s.retargeting SET counter=%d WHERE atfinity_case_id=%d AND onboarding_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["counter"],
      $data["atfinity_case_id"],
      $data["onboarding_id"]
    );

    $result = $this->database->query($query);
    return $result;
  }

  public function updateDatesFields(array $data)
  {

    $date = new \DateTime();
    $date = $date->format("Y-m-d H:i:s");

    $nextDate = new \DateTime();
    $nextDate->add(new \DateInterval($data["next_update"]));
    $nextDate = $nextDate->format("Y-m-d H:i:s");

    $query = sprintf(
      'UPDATE %s.retargeting SET last_updated="' . $date . '", next_update="' . $nextDate . '" WHERE id=%d AND atfinity_case_id=%d AND onboarding_id=%d',
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["retargeting_id"],
      $data["atfinity_case_id"],
      $data["onboarding_id"]
    );
    $result = $this->database->query($query);
    return $result;
  }

  public function updateSignatureRequestId(array $data)
  {

    $this->logInfo(json_encode($data));

    $query = sprintf(
      "UPDATE %s.onboarding SET signature_request_id='%s' WHERE xpressid_validation_id='%s'",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["signature_request_id"],
      $data["validation_id"]
    );

    $this->response['error'] = !$this->database->query($query);

    $this->database->close();
    return $this->response;
  }

  /*
   * Obtains cases in state 4 for request additional documents 
   */
  public function getRequestProspectsFromOnboarding()
  {
    $query = sprintf(
      "SELECT * FROM %s.onboarding WHERE state=%d and prospects=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $state = 4,
      $prospects = 0
    );

    $result = $this->database->query($query);

    if ($result == false) {
      $this->logError($result);
      $this->response['error'] = true;
      $this->response['info'] = mysqli_error($this->database);
      $this->logError($this->response['info']);
    } else {
      $rows = [];
      while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
      }
      $this->response['error'] = false;
      $this->response['info'] = $rows;
    }

    $this->database->close();

    return $this->response;
  }
  
  public function updateProspectField(array $data)
  {

    $query = sprintf(
      "UPDATE %s.onboarding SET prospects=%d WHERE id=%d AND atfinity_case_id=%d",
      AppConfig::getInstance()->getValue("DB_NAME"),
      $data["send"],
      $data["onboarding_id"],
      $data["atfinity_case_id"]
    );

    $result = $this->database->query($query);
    return $result;
  }
}
