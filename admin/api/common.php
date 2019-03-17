<?php

function getRelatedCats(PDO $db) {
  global $response;
  $related_categories = array();
  $success = false;

  try {
    $cats_query = $db->prepare(<<<SQL
SELECT * FROM related_cats ORDER BY related_cats_id
SQL
);
    $success = $cats_query->execute();
  
    if($success) {
      while($row=$cats_query->fetch(PDO::FETCH_ASSOC)) {
        $related_categories[$row['related_cats_id']] = ['related_cats_name' => $row['related_cats_name']];
      }
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error getting related categories';
  }

  return $related_categories;
}

function makeRelatedKey($related) {
  $related_key = [];

  foreach($related as $id => $key) {
    $related_key[$id] = isset($key['value']) ? $key['value'] : $key;
  }

  return json_encode($related_key);
}

