<?php

function getCareers(PDO $db) {
  global $response;
  $careers = array();
  $success = false;

  try {
    $query = $db->prepare(<<<SQL
Select * from careers order by careers_order
SQL
    );
    $success = $query->execute();
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $careers[] = array('id' => $row['careers_id'], 'title' => $row['careers_title'], 'order' => $row['careers_order']);
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting careers list';
  }

  return $careers;
}

function getCareerPosition(PDO $db, String $id) {
  global $response;
  $position = array();
  $success = false;

  try {
    $query = $db->prepare(<<<SQL
Select * from careers where careers_id = ?
SQL
    );
    $success = $query->execute([$id]);
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $position = array('id' => $row['careers_id'], 'title' => $row['careers_title'], 'location' => $row['careers_location'], 'duration' => $row['careers_duration'], 'active' => (bool) $row['careers_active'],
                          'description' => $row['careers_description'], 'desc_extra' => $row['careers_desc_extra']);
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting career position';
  }

  return $position;
}

function createPosition (PDO $db, String $name) {
  global $response;
  $success = false;
  $existing_careers = getCareers($db);

  foreach($existing_careers as $key => $position) {
    if($position['title'] == $name) {
      http_response_code(500);
      $response['msg'] = 'This position name already exists';

      return false;
    }
  }

  $query = $db->prepare(<<<SQL
    INSERT INTO careers (careers_id, careers_title) VALUES (?,?)
SQL
  );

  $success = $query->execute(array(
    null,
    $name));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error creating position';
  }

  return $success;
}

function deletePosition (PDO $db, String $name) {
  global $response;
  $success = false;
  $toDelete = null;
  $existing_careers = getCareers($db);

  foreach($existing_careers as $key => $position) {
    if($position['title'] == $name) {
      $toDelete = $position['id'];
    }
  }

  if($toDelete == null) {
    http_response_code(500);
    $response['msg'] = 'The name entered does not match an existing position';

    return false;
  }

  $query = $db->prepare(<<<SQL
    DELETE FROM careers WHERE careers_id = ?
SQL
  );

  $success = $query->execute(array(
    (int) $toDelete));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error deleting position';
  }

  return $success;
}

function savePositionContent (PDO $db, String $id, Array $content) {
  global $response;
  $success = false;

  $query = $db->prepare(<<<SQL
    UPDATE careers SET careers_title = ?, careers_location = ?, careers_duration = ?, careers_active = ?, careers_description = ?, careers_desc_extra = ? WHERE careers_id = ?
SQL
  );

  $success = $query->execute(array(
    $content['title'],
    $content['location'],
    $content['duration'],
    (int) $content['active'],
    $content['description'],
    $content['desc_extra'],
    $id
  ));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error saving position content update';
  }

  return $success;
}

function saveCareerOrder (PDO $db, Array $order) {
  global $response;
  $success = false;

  foreach ($order as $idx => $position) {
    $query = $db->prepare(<<<SQL
      UPDATE careers SET careers_order = ? WHERE careers_id = ?
SQL
    );

    $success = $query->execute(array(
      (int) $position['order'],
      $position['id']
    ));

    if(!$success) {
      http_response_code(500);
      $response['msg'] = 'Error saving career order';
      break;
    }

    unset($query);
  }

  return $success;
}