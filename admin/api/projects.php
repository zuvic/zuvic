<?php

function getProjectRelated(PDO $db, String $id) {
  global $response;
  $service_related = array();
  $success = false;

  try {
    $service_query = $db->prepare(<<<SQL
Select * from project_related where project_related_site_id = ?
SQL
);
    $success = $service_query->execute(array($id));

    if($success) {
      while($row=$service_query->fetch(PDO::FETCH_ASSOC)) {
        $related_cats = json_decode($row['project_related_key'], true);
        $service_related = ['project_related_id' => $row['project_related_id'], 'project_related_site_id' => $row['project_related_site_id'], 'project_related_key' => $related_cats];
      }
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting services related';
  }

  return $service_related;
}

function getProjectInfo(PDO $db) {
  $project_info = array();

  try {
    $query = $db->prepare('Select project_site_name, project_site_id from project_site');
    $query->execute();

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $project_info[] = array('id' => $row['project_site_id'], 'name' => $row['project_site_name']);
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  return $project_info;
}

function getProjectContent(PDO $db, String $id) {
  $project_content = array(
    'challenges' => array(),
    'highlights' => array(),
    'solutions' => array(),
    'title' => null,
    'subtitle' => null,
    'photos' => 0,
    'increment' => 0);

  try {
    $query = $db->prepare('Select * from project_content where project_content_site_id = ?');
    $query->execute(array($id));

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      switch ($row['project_content_type']) {
        case "sub-title":
          $project_content['subtitle'] = array( 'id' => $row['project_content_id'], 'value' => $row['project_content_value']);
          break;
        case "title":
          $project_content['title'] = array( 'id' => $row['project_content_id'], 'value' => $row['project_content_value']);
          break;
        case "photos":
          $project_content['photos'] = array( 'id' => $row['project_content_id'], 'value' => $row['project_content_value']);
          break;
        case "challenges":
          $project_content['challenges'][] = array('delete' => false, 'value' => $row['project_content_value'], 'id' => $row['project_content_id']);
          break;
        case "highlights":
          $project_content['highlights'][] = array('delete' => false, 'value' => $row['project_content_value'], 'id' => $row['project_content_id']);
          break;
        case "solutions":
          $project_content['solutions'][] = array('delete' => false, 'value' => $row['project_content_value'], 'id' => $row['project_content_id']);
          break;
      }
    }

    $inc_query = $db->prepare('SHOW TABLE STATUS LIKE "project_content"');
    $inc_query->execute();

    $row = $inc_query->fetch(PDO::FETCH_ASSOC);
    $project_content['increment'] = (int) $row['Auto_increment'];

  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  return $project_content;
}

function createProject(PDO $db, String $name) {
  global $response;
  $success = false;
  $existing_projects = getProjectInfo($db);

  foreach($existing_projects as $key => $project) {
    if($project['name'] == $name) {
      http_response_code(500);
      $response['msg'] = 'This project name already exists';

      return false;
    }
  }

  $query = $db->prepare('INSERT INTO project_site (project_site_id, project_site_name) VALUES (?,?)');
  $success = $query->execute(array(
    null,
    $name));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error creating project';
  }

  $new_query = $db->prepare('SELECT * FROM project_site WHERE project_site_id = LAST_INSERT_ID()');
  $success = $new_query->execute();

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error creating project';
  }

  $new_project = $new_query->fetch(PDO::FETCH_ASSOC);

  $rel_query = $db->prepare('INSERT INTO project_related (project_related_id, project_related_site_id) VALUES (?,?)');
  $success = $rel_query->execute(array(
    null,
    $new_project['project_site_id']));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error creating project';
  }

  return $success;
}

function deleteProject(PDO $db, String $name) {
  global $response;
  $success = false;
  $toDelete = null;
  $existing_projects = getProjectInfo($db);

  foreach($existing_projects as $key => $project) {
    if($project['name'] == $name) {
      $toDelete = $project['id'];
    }
  }

  if($toDelete == null) {
    http_response_code(500);
    $response['msg'] = 'The name entered does not match an existing project';

    return false;
  }

  $query = $db->prepare('DELETE FROM project_site WHERE project_site_id = ?');
  $success = $query->execute(array(
    (int) $toDelete));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error deleting project';
  }

  return $success;
}

function renameProject(PDO $db, String $id, String $name) {
  global $response;
  $success = false;
  $existing_projects = getProjectInfo($db);

  foreach($existing_projects as $key => $project) {
    if($project['name'] == $name) {
      http_response_code(500);
      $response['msg'] = 'This project name already exists';

      return false;
    }
  }

  $query = $db->prepare(<<<SQL
UPDATE project_site SET project_site_name = ? WHERE project_site_id = ?
SQL
);
  $success = $query->execute(array(
    $name,
    $id));

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error updating project name: ' . $query->errorInfo;
  }

  return $success;
}


function saveProjectContent(PDO $db, String $id, Array $content) {

  try {
    foreach ($content['challenges'] as $subId => $challenge) {
      $chal_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
      $chal_query->execute(array(
        $id,
        'challenges',
        $challenge['id'],
        $challenge['value'],
        $challenge['value']));
    }

    foreach ($content['solutions'] as $subId => $solution) {
      $sol_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
      $sol_query->execute(array(
        $id,
        'solutions',
        $solution['id'],
        $solution['value'],
        $solution['value']));
    }

    foreach ($content['highlights'] as $subId => $highlight) {
      $high_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
      $high_query->execute(array(
        $id,
        'highlights',
        $highlight['id'],
        $highlight['value'],
        $highlight['value']));
    }

    $title_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
    $title_query->execute(array(
      $id,
      'title',
      isset($content['title']['id']) ? $content['title']['id'] : null,
      $content['title']['value'],
      $content['title']['value']));

    $sub_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
    $sub_query->execute(array(
      $id,
      'sub-title',
      isset($content['subtitle']['id']) ? $content['subtitle']['id'] : null,
      $content['subtitle']['value'],
      $content['subtitle']['value']));

    $pho_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
    $pho_query->execute(array(
      $id,
      'photos',
      $content['photos']['id'],
      $content['photos']['value'],
      $content['photos']['value']));

    $clr_query = $db->prepare('DELETE FROM project_content WHERE project_content_value IS NULL OR project_content_value = ""');
    $clr_query->execute();

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }

  return true;
}

function saveProjectRelated(PDO $db, String $id, Array $related) {
  $success = false;

  $related_key = makeRelatedKey($related['keys']);

  try {
    $rel_query = $db->prepare(<<<SQL
INSERT INTO project_related (project_related_id, project_related_site_id, project_related_key) VALUES (?,?,?)
ON DUPLICATE KEY UPDATE project_related_key = ?
SQL
);

    $success = $rel_query->execute(array(
      isset($related['project_related_id']) ? $related['project_related_id'] : null,
      $id,
      $related_key,
      $related_key));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }

  return $success;
}

function updateProjectImages($db, $projID, $num) {
  $contentID = null;
  $success = false;

  try {
    $pho_query = $db->prepare('SELECT * FROM project_content WHERE project_content_site_id = ? AND project_content_type = "photos"');
    $success = $pho_query->execute(array(
      $projID
    ));

    while($row = $pho_query->fetch(PDO::FETCH_ASSOC)) {
      $contentID = $row['project_content_id'];
    }

    $upd_query = $db->prepare('INSERT INTO project_content (project_content_site_id, project_content_type, project_content_id, project_content_value) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE project_content_value = ?');
    $success = $upd_query->execute(array(
      $projID,
      "photos",
      $contentID,
      $num,
      $num
    ));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }



  return $success;
}

function deleteProjectContent(PDO $db, String $id) {

  try {

    $clr_query = $db->prepare('DELETE FROM project_content WHERE project_content_id = ?');
    $clr_query->execute(array($id));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }

  return true;
}

function updateImages($db, $order, $projectID) {
  global $settings;
  global $response;
  $success = true;

  $old_files = [];
  $files = [];

  if ($handle = opendir($settings['image_path'] . $projectID . '/')) {

    while (false !== ($entry = readdir($handle))) {
      if($entry!== '.' && $entry !== '..') {
        $files[] = $entry;
      }
    }

    closedir($handle);
  }

  $images = preg_grep('/\.jpg$/i', $files);
  sort($images);

  foreach($images as $idx => $image) {
    $path = pathinfo($image);
    $old_files[$idx] = $settings['image_path'] . $projectID . '/' . $path['filename'] . '_tmp' . '.jpg';
    $success = copy($settings['image_path'] . $projectID . '/' . $image, $settings['image_path'] . $projectID . '/' . $path['filename'] . '_tmp' . '.jpg');

    if(!$success) {
      http_response_code(500);
      die('Error updating project order');
    }
  }

  foreach($order as $idx => $newOrder) {
    $success = rename($old_files[$newOrder], $settings['image_path'] . $projectID . '/' . ($idx + 1) . '.jpg');

    if(!$success) {
      http_response_code(500);
      die('Error updating project order');
    }
  }

  return $success;
}

function uploadImage($db, $newFiles, $projectID) {
  global $settings;
  global $response;
  $success = true;

  $files = [];

  if (!file_exists($settings['image_path'] . $projectID . '/')) {
      mkdir($settings['image_path'] . $projectID . '/', 0777);
  }

  if ($handle = opendir($settings['image_path'] . $projectID . '/')) {

    while (false !== ($entry = readdir($handle))) {
      if($entry!== '.' && $entry !== '..') {
        $files[] = $entry;
      }
    }

    closedir($handle);
  }

  $images = preg_grep('/\.jpg$/i', $files);

  if(updateProjectImages($db, $projectID, count($images) + 1)) {
    $success = move_uploaded_file( $newFiles['tmp_name'],  $settings['image_path'] . $projectID . '/' . (count($images) + 1) . '.' . strtolower(pathinfo($newFiles['name'], PATHINFO_EXTENSION)));
    if(!$success) {
      updateProjectImages($db, $projectID, count($images));
    }
  } else {
    $success = false;
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error uploading project photo: ' . $name;
  }

  return $success;
}

function deleteImage($db, $filename, $projectID) {
  global $settings;
  global $response;
  $success = false;

  $num_existing_images = 0;
  $files = [];

  $success = unlink($settings['image_path'] . $projectID . '/' . $filename . '.jpg');

  if ($success && $handle = opendir($settings['image_path'] . $projectID . '/')) {

    while (false !== ($entry = readdir($handle))) {
      if($entry !== '.' && $entry !== '..') {
        $files[] = $entry;
      }
    }

    closedir($handle);
  } else {
    return false;
  }

  $images = preg_grep('/\.jpg$/i', $files);
  sort($images);

  foreach($images as $key => $image) {
    rename( $settings['image_path'] . $projectID . '/' . $image,  $settings['image_path'] . $projectID . '/' . ($key + 1) . '.jpg');
  }

  $success = updateProjectImages($db, $projectID, count($images));

  return $success;
}

function getRelatedProjects(PDO $db, String $id, Array $exclude_ids = [], Int $limit = 99999999) {
  global $response;

  $project_related = getProjectRelated($db, $id);
  $related_projects = array();
  $success = false;

  if(!is_array($project_related) || sizeof($project_related) <= 0 || !isset($project_related['project_related_key'])) return [];

  $project_key = '';

  foreach($project_related['project_related_key'] as $related_cat_id => $val) {
    if($val !== true) continue;
    $key = preg_replace('/\{|\}/', '', json_encode([$related_cat_id => $val]));

    $project_key .= "pr.project_related_key LIKE '%" . $key . "%' AND ";
  }

  $project_key .= "pr.project_related_key LIKE '%:true%' AND ";

  try {
    $project_query = $db->prepare(sprintf(<<<SQL
SELECT * FROM
(SELECT p.project_site_name AS `project_site_name`, pr.project_related_site_id AS project_site_id
FROM project_related pr

INNER JOIN project_site p
ON pr.project_related_site_id = p.project_site_id

WHERE %s) r
ORDER BY RAND()
LIMIT :limit
SQL
, preg_replace('/\sAND\s$/', '', $project_key)));

    $project_query->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $success = $project_query->execute();

    if($success) {
      while($row = $project_query->fetch(PDO::FETCH_ASSOC)) {
          if(in_array($row['project_site_id'], $exclude_ids) || $row['project_site_id'] == $id) continue;
          $related_projects[] = array('name' => $row['project_site_name'], 'id' => $row['project_site_id']);
      }
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting related projects';
  }

  return $related_projects;
}