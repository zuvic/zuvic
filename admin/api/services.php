<?php

function getServices(PDO $db) {
  global $response;
  global $services_aliases;
  $services = array();
  $success = false;

  try {
    $query = $db->prepare('Select * from services_site');
    $success = $query->execute();
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $service_content[] = array('id' => $row['services_site_id'], 'name' => $row['services_site_name']);
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting services list';
  }

  return $service_content;
}

function getServiceContent(PDO $db, String $id) {
  global $response;
  $service_content = array('id' => null, 'content' => '', 'delete' => false);
  $success = false;

  try {
    $query = $db->prepare('Select * from services_content where services_content_site_id = ?');
    $success = $query->execute(array($id));
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $service_content[$row['services_content_type']] = $row['services_content_value'];
      $service_content['id'] = $row['services_content_id'];
      $service_content['delete'] = false;
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting services content';
  }

  return $service_content;
}

function getServiceRelated(PDO $db, String $id) {
  global $response;
  $service_related = array();
  $success = false;

  try {
    $service_query = $db->prepare(<<<SQL
Select * from services_related where services_related_site_id = ?
SQL
);
    $success = $service_query->execute(array($id));
  
    if($success) {
      while($row=$service_query->fetch(PDO::FETCH_ASSOC)) {
        $related_cats = json_decode($row['services_related_key'], true);
        $service_related = ['service_related_id' => $row['services_related_id'], 'service_related_site_id' => $row['services_related_site_id'], 'service_related_key' => $related_cats];
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

function getServiceProjects(PDO $db, String $id) {
  global $response;

  $service_related = getServiceRelated($db, $id);
  $service_projects = array();
  $success = false;

  if(!is_array($service_related) || sizeof($service_related) <= 0 || !isset($service_related['service_related_key'])) return [];

  $project_key = '';

  foreach($service_related['service_related_key'] as $related_cat_id => $val) {
    if($val !== true) continue;
    $key = preg_replace('/\{|\}/', '', json_encode([$related_cat_id => $val]));

    $project_key .= "pr.project_related_key LIKE '%" . $key . "%' OR ";
  }

  try {
    $service_query = $db->prepare(sprintf(<<<SQL
SELECT p.project_site_name AS `project_site_name`, op.order_project_id AS order_project_id, pr.project_related_site_id AS project_site_id, op.order_project_id AS id, op.order_project_idx AS idx
FROM project_related pr

LEFT JOIN
(
      SELECT op.order_project_id,
             op.order_project_site_id,
             op.order_project_idx
      FROM order_project op

      WHERE op.order_project_key = ? AND op.order_project_type = "service"
) op ON pr.project_related_site_id = op.order_project_site_id

LEFT JOIN project_site p
ON pr.project_related_site_id = p.project_site_id

WHERE %s
ORDER BY ISNULL(op.order_project_idx), op.order_project_idx ASC
SQL
, preg_replace('/\sOR\s$/', '', $project_key)));

    $success = $service_query->execute([$id]);
  
    if($success) {
      while($row = $service_query->fetch(PDO::FETCH_ASSOC)) {
          $service_projects[] = array('project_site_name' => $row['project_site_name'], 'project_site_id' => $row['project_site_id'], 'order_project_id' => $row['order_project_id'], 'idx' => $row['idx']);
      }
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting services projects';
  }

  return $service_projects;
}



function saveServiceOrder(PDO $db, String $id, Array $projects) {
  $service_related = getServiceRelated($db, $id);
  $success = true;

  $related_key = makeRelatedKey($service_related['service_related_key']);

  foreach ($projects as $project) {
    try {
      $upd_query = $db->prepare(<<<SQL
INSERT INTO order_project (order_project_id, order_project_site_id, order_project_idx, order_project_type, order_project_key) 
VALUES (?,?,?,?,?) 
ON DUPLICATE KEY UPDATE order_project_idx = ?
SQL
);
      $success = $upd_query->execute(array(
        $project['order_project_id'],
        $project['project_site_id'],
        $project['idx'],
        'service',
        $id,
        $project['idx']
      ));

      if(!$success) {
        break;
      }
    } catch (PDOException  $e ) {
      echo "Error: " . $e;
    }
  }

  return $success;
}

function saveServiceContent(PDO $db, String $id, Array $content) {
  global $services_aliases;
  $success = false;

  try {
    // foreach ($content as $idx => $content) {
      if ($content['delete'] == False) {
        $query = $db->prepare('INSERT INTO services_content (services_content_id, services_content_type, services_content_site_id, services_content_value, services_content_idx) VALUES (?,?,?,?,0) ON DUPLICATE KEY UPDATE services_content_value = ?');
        // $success = $query->execute(array(
        //   null,
        //   'title',
        //   $name,
        //   $content['title'],
        //   $idx,
        //   $content['title'],
        // ));

        $success = $query->execute(array(
          $content['id'],
          'content',
          $id,
          $content['content'],
          $content['content'],
        ));
      } else {
        $query = $db->prepare('DELETE FROM services_content WHERE services_content_type = ? and services_content_page = ? and services_content_idx = ?');
        $success = $query->execute(array(
          'title',
          $name,
          $idx
        ));

        $success = $query->execute(array(
          'content',
          $name,
          $idx
        ));
      }

      if(!$success) {
        http_response_code(500);
        $response['msg'] = 'Error updating services content';
        // break;
      }
    // }

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }
  
  return $success;
}

function saveServiceRelated(PDO $db, String $id, Array $related) {
  $success = false;

  $related_key = makeRelatedKey($related['keys']);

  try {
    $rel_query = $db->prepare(<<<SQL
INSERT INTO services_related (services_related_id, services_related_site_id, services_related_key) VALUES (?,?,?) 
ON DUPLICATE KEY UPDATE services_related_key = ?
SQL
);

    $success = $rel_query->execute(array(
      isset($related['service_related_id']) ? $related['service_related_id'] : null,
      $id,
      $related_key,
      $related_key));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }
  
  return $success;
}