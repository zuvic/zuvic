<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

global $SesClient;
$SesClient = new SesClient([
  'region'  => 'us-east-1',
  'profile' => 'zuvic',
  'version' => 'latest'
]);

// Shutdown function
function ShutdownHandler() {
  $error = error_get_last();
  if ($error['type'] === E_ERROR) {
    http_response_code(500);
    die($error['message']);
  }

  if ($error['type'] === E_WARNING) {
    http_response_code(500);
    die($error['message']);
  }
}

register_shutdown_function('ShutdownHandler');
if(!isset($_SESSION)) { 
  session_start(); 
} 

try {
  require_once(__DIR__ . '/../../settings.inc');
} catch (Exception $e) {
  http_response_code(500);
  die($e);
}

global $db_settings;
global $ses_settings;
global $response;
$response = array('msg' => '', 'data' => null);

try {
  $db = new PDO("mysql:host=" . $db_settings['host'] . ";dbname=" . $db_settings['name'] . ";charset=utf8", $db_settings['username'], $db_settings['password']);
} catch (PDOException  $e ) {
  http_response_code(500);
  die('PDO Error: ' . $e);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  if($data || ($_SERVER['REQUEST_METHOD'] === 'POST' && count($_POST) > 0)) {
    if(count($_POST) > 0) $data = $_POST;
    http_response_code(200);
    switch ($data['type']) {
      case "login":
          if($data['method'] == 'get') {
            http_response_code(200);
            $response['data'] = getLogin($db);
            break;
          } else if ($data['method'] == 'login' && isset($data['username']) && isset($data['pass'])) {
            http_response_code(200);
            $response['data'] = doLogin($db, $data['username'], $data['pass']);
            break;
          } else if ($data['method'] == 'update_login' && isset($data['login'])) {
            http_response_code(200);
            if(saveLogin($db, $data['login'])) {
              $response['data'] = getLogin($db);
            }
            break;
          } else if ($data['method'] == 'create_login' && isset($data['login'])) {
            http_response_code(200);
            if(saveLogin($db, $data['login'], true)) {
              $response['data'] = getUsers($db);
            }
            break;
          } else if ($data['method'] == 'reset_password' && isset($data['id'])) {
            http_response_code(200);
            if(resetPassword($db, $data['id'])) {
              $response['data'] = getUsers($db);
            }
            break;
          } else if ($data['method'] == 'validate_token' && isset($data['token'])) {
            http_response_code(200);
            $response['data'] = validateToken($db, $data['token']);
            break;
          } else if ($data['method'] == 'activate_account' && isset($data['pass']) && isset($data['id'])) {
            http_response_code(200);
            if(!activateAccount($db, $data['pass'], $data['id'])) {
              http_response_code(500);
              $response['msg'] = 'Error activating account';
            }
            break;
          } else if ($data['method'] == 'new_login' && isset($data['login'])) {
            http_response_code(200);
            if(saveLogin($db, $data['login'])) {
              $response['data'] = getLogin($db);
            }
            break;
          } else if ($data['method'] == 'logout') {
            http_response_code(200);
            $response['data'] = doLogout();
            break;
          }
      case "profile":
          if(!getLogin($db)) {
            http_response_code(401);
            break;
          }
          if($data['method'] == 'get_users') {
            http_response_code(200);
            $response['data'] = getUsers($db);
            break;
          } else if ($data['method'] == 'update_users' && isset($data['users'])) {
            http_response_code(200);
            if(updateUsers($db, $data['users'])) {
              $response['data'] = getUsers($db);
            }
            break;
          }
      case "project":
          if(!getLogin($db)) {
            http_response_code(401);
            break;
          }
          if($data['method'] == 'site') {
            http_response_code(200);
            $response['data'] = getProjectInfo($db);
            break;
          } else if($data['method'] == 'create' && isset($data['name'])) {
            http_response_code(200);
            if(createProject($db, $data['name'])) {
              $response['data'] = getProjectInfo($db);
            }
            break;
          } else if($data['method'] == 'delete' && isset($data['name'])) {
            http_response_code(200);
            if(deleteProject($db, $data['name'])) {
              $response['data'] = getProjectInfo($db);
            }
            break;
          } else if($data['method'] == 'content' && isset($data['id'])) {
            http_response_code(200);
            $response['data'] = getProjectContent($db, $data['id']);
            break;
          } else if($data['method'] == 'related' && isset($data['id'])) {
            http_response_code(200);
            $response['data'] = getRelatedProjects($db, $data['id']);
            break;
          } else if($data['method'] == 'save_content' && isset($data['projID']) && isset($data['content'])) {
            if(saveProjectContent($db, $data['projID'], $data['content']) === true) {
              http_response_code(200);
              $response['data'] = getProjectContent($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error saving project: ' . $data['projID']);
            }
            break;
          } else if($data['method'] == 'save_related' && isset($data['projID']) && isset($data['related'])) {
            if(saveProjectRelated($db, $data['projID'], $data['related']) === true) {
              http_response_code(200);
              $response['data'] = getRelatedProjects($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error saving project: ' . $data['projID']);
            }
            break;
          } else if($data['method'] == 'delete_content' && isset($data['projID']) && isset($data['contentID'])) {
            if(deleteProjectContent($db, $data['contentID']) === true) {
              http_response_code(200);
              $response['data'] = getProjectContent($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error deleting project content ID: ' . $data['contentID']);
            }
            break;
          } else if($data['method'] == 'upload' && isset($data['projID']) && isset($_FILES['file'])) {
            if(uploadImage($db, $_FILES['file'], $data['projID']) === true) {
              http_response_code(200);
              $response['data'] = getProjectContent($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error uploading project photos');
            }
            break;
          } else if($data['method'] == 'delete_image' && isset($data['projID']) && isset($data['filename'])) {
            if(deleteImage($db, $data['filename'], $data['projID']) === true) {
              http_response_code(200);
              $response['data'] = getProjectContent($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error deleting project photo: ' . $data['filename'] . '.jpg');
            }
            break;
          } else if($data['method'] == 'update_images' && isset($data['projID']) && isset($data['order'])) {
            if(updateImages($db, $data['order'], $data['projID']) === true) {
              http_response_code(200);
              $response['data'] = getProjectContent($db, $data['projID']);
            } else {
              http_response_code(500);
              die('Error updating photo order');
            }
            break;
          }
          http_response_code(400);
          die('The request data is invalid');
      case "service":
          if($data['method'] == 'content' && isset($data['name'])) {
            http_response_code(200);
            $response['data'] = getServiceContent($db, $data['name']);
            break;
          }
      default:
          http_response_code(400);
          die('The request type is invalid');
    }
  } else {
    http_response_code(400);
    die();
  }

  echo json_encode($response);
}

function getLogin($db) {
  global $response;
  global $settings;
  $login = null;
  $expire_time = $settings['session_length']; // Expiration time = 1 hour

  if (isset($_SESSION['login']) && $_SESSION['login'] !== null) {
    if( !isset($_SESSION['login']['last_activity']) || $_SESSION['login']['last_activity'] < time() - $expire_time ) {
      $login = null;
      unset($_SESSION['login']);

      $response['msg'] = 'You have been logged out due to inactivity';
    } else {
      if(isset($_SESSION['login']['login_id'])) {
        $login = $_SESSION['login'] = getUsers($db, $_SESSION['login']['login_id']);
      }
      
      if($login['login_active'] == 0) {
        $login = null;
        unset($_SESSION['login']);
  
        $response['msg'] = 'This account has been deactivated';
      }

      $_SESSION['login']['last_activity'] = time();
    }
  }

  return $login;
}

function doLogin($db, $username, $pass) {
  global $response;
  $login = null;

  try {
    $query = $db->prepare('Select * from login where login_user = ?');
    $query->execute(array(
      $username
    ));
  
    $user = $query->fetchAll(PDO::FETCH_ASSOC);

    if($user !== null && count($user) > 0) {
      if($user[0]['login_pass'] == NULL || $user[0]['login_active'] == 0) {
        unset($user[0]['login_pass']);
        unset($pass);

        $response['msg'] = 'This account is currently inactive';
      } else if(password_verify($pass, $user[0]['login_pass'])) {
        unset($user[0]['login_pass']);
        unset($pass);

        $login = $_SESSION['login'] = $user[0];
        $_SESSION['login']['last_activity'] = time();
      } else {
        $response['msg'] = 'Username or password is incorrect';
      }
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  return $login;
}

function resetPassword(PDO $db, String $id) {
  global $response;
  $success = false;
  $token = bin2hex(openssl_random_pseudo_bytes(30));
  $token_exp = date('Y-m-d H:i:s', strtotime('now +1 day'));
  $user = getUsers($db, $id);

  try {
    $query = $db->prepare('UPDATE `login` SET `login_token` = ?, login_token_expiry = ? WHERE login_id = ?');
    $success = $query->execute(array(
      $token,
      $token_exp,
      (int) $id));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if($success == true) {
    $success = sendEmail($user['login_email'], $token, 'reset');
  }

  if($success !== true) {
    http_response_code(500);
    $response['msg'] = "Error resetting user's password";
  }

  return $success;
}

function saveLogin($db, $login, $create = false) {
  global $response;
  $success = false;
  $token = null;
  $token_exp = null;
  $users = getUsers($db);

  if($create == true) {
    // Validate new user info
    foreach ($users as $key => $user) {
      if(strtolower($login['login_user']) == $user['login_user']) {
        http_response_code(500);
        $response['msg'] = 'A user with this username already exists';
        return false;
      }
      if(strtolower($login['login_email']) == $user['login_email']) {
        http_response_code(500);
        $response['msg'] = 'A user with this email already exists';
        return false;
      }
    }
    $token = bin2hex(openssl_random_pseudo_bytes(30));
    $token_exp = date('Y-m-d H:i:s', strtotime('now +1 day'));
  }

  try {
    $login_query = $db->prepare('INSERT INTO `login` (login_id, `login_type`, login_first, login_last, login_email, login_user, login_token, login_token_expiry) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE login_id = ?, `login_type` = ?, login_first = ?, login_last = ?, login_email = ?, login_user = ?');
    $success = $login_query->execute(array(
      isset($login['login_id']) ? (int) $login['login_id'] : null,
      $login['login_type'],
      $login['login_first'],
      $login['login_last'],
      strtolower($login['login_email']),
      strtolower($login['login_user']),
      $token,
      $token_exp,
      isset($login['login_id']) ? (int) $login['login_id'] : null,
      $login['login_type'],
      $login['login_first'],
      $login['login_last'],
      strtolower($login['login_email']),
      strtolower($login['login_user'])));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if($create == true && $success == true) {
    $success = sendEmail($login['login_email'], $token, 'create');
  }

  if(!$success) {
    http_response_code(500);
    $response['msg'] = 'Error creating new user';
  }

  return $success;
}

function doLogout() {
  $login = null;
  unset($_SESSION['login']);

  return $login;
}

function validateToken(PDO $db, String $token) {
  global $response;
  $login = null;

  try {
    $query = $db->prepare('Select * from login where login_token = ?');
    $query->execute(array($token));
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $login = array('login_id' => $row['login_id'], 'login_first' => $row['login_first'], 'login_last' => $row['login_last'], 
                              'login_user' => $row['login_user'], 'login_email' => $row['login_email'], 'login_type' => $row['login_type'], 'login_token' => $row['login_token'],
                              'login_token_expiry' => $row['login_token_expiry'], 'login_active' => $row['login_active']);
    }

    if($login !== null) {
      if(date("Y-m-d H:i:s") >= $login['login_token_expiry']) {
        http_response_code(500);
        $response['msg'] = 'Confirmation token has expired - Contact your administrator';
      }

      // $query = $db->prepare('UPDATE `login` SET login_token = NULL, login_token_expiry = NULL WHERE login_token = ?');
      // $query->execute(array($token));
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if($login == null) {
    http_response_code(500);
    $response['msg'] = 'Token is invalid';
  }

  return $login;
}

function activateAccount(PDO $db, String $pass, String $id) {
  global $response;
  $success = false;

  try {
    $query = $db->prepare('UPDATE `login` SET login_pass = ?, login_active = 1, login_token = null, login_token_expiry = null WHERE login_id = ?');
    $query->execute(array(password_hash($pass, PASSWORD_BCRYPT), $id));

    $success = $query->rowCount() == 1;
    unset($pass);
    unset($data['pass']);
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  return $success;
}

function updateUsers(PDO $db, Array $users) {
  global $response;
  $success = false;

  foreach ($users as $id => $user) {
    if($user == null) continue;
    try {
      $query = $db->prepare('UPDATE `login` SET `login_type` = ?, login_active = ? WHERE login_id = ?');
      $success = $query->execute(array(
        (int) $user['login_type'],
        (int) $user['login_active'],
        (int) $id));

    } catch (PDOException  $e ) {
      http_response_code(500);
      die('PDO Error: ' . $e);
    }

    if(!$success) {
      http_response_code(500);
      $response['msg'] = 'Error updating users';
      return false;
    }
  }

  return $success;
}

function getUsers(PDO $db, String $id = null) {
  $users = array();

  try {
    if($id !== null) {
      $query = $db->prepare('Select * from login where login_id = ?');
      $query->execute(array($id));
    } else {
      $query = $db->prepare('Select * from login');
      $query->execute();
    }
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $users[$row['login_id']] = array('login_id' => $row['login_id'], 'login_first' => $row['login_first'], 'login_last' => $row['login_last'], 
                              'login_user' => $row['login_user'], 'login_email' => $row['login_email'], 'login_type' => $row['login_type'], 'login_token' => $row['login_token'],
                              'login_token_expiry' => $row['login_token_expiry'], 'login_active' => $row['login_active']);
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  if($id !== null) {
    $users = $users[$id];
  }

  return $users;
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

  try {

    $rel_query = $db->prepare('INSERT INTO project_related (project_related_id, project_related_site_id, project_related_survey, project_related_planning, project_related_civil, project_related_transport, project_related_structural, project_related_bridges, project_related_utility, project_related_water, project_related_const, project_related_perm, project_related_enviro) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE project_related_id = ?, project_related_site_id = ?, project_related_survey = ?, project_related_planning = ?, project_related_civil = ?, project_related_transport = ?, project_related_structural = ?, project_related_bridges = ?, project_related_utility = ?, project_related_water = ?, project_related_const = ?, project_related_perm = ?, project_related_enviro = ?');
    $rel_query->execute(array(
      $related['project_related_id'],
      $related['project_related_site_id'],
      $related['project_related_survey'], 
      $related['project_related_planning'], 
      $related['project_related_civil'], 
      $related['project_related_transport'], 
      $related['project_related_structural'], 
      $related['project_related_bridges'], 
      $related['project_related_utility'], 
      $related['project_related_water'], 
      $related['project_related_const'], 
      $related['project_related_perm'], 
      $related['project_related_enviro'],
      $related['project_related_id'],
      $related['project_related_site_id'],
      $related['project_related_survey'], 
      $related['project_related_planning'], 
      $related['project_related_civil'], 
      $related['project_related_transport'], 
      $related['project_related_structural'], 
      $related['project_related_bridges'], 
      $related['project_related_utility'], 
      $related['project_related_water'], 
      $related['project_related_const'], 
      $related['project_related_perm'], 
      $related['project_related_enviro']));

  } catch (PDOException  $e ) {
    http_response_code(500);
    die("Error: " . $e);
  }
  
  return true;
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

  foreach($newFiles['name'] as $idx => $name) {
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
      $success = move_uploaded_file( $newFiles['tmp_name'][$idx],  $settings['image_path'] . $projectID . '/' . (count($images) + 1) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION)));
    }

    if(!$success) {
      http_response_code(500);
      $response['msg'] = 'Error uploading project photo: ' . $name;
    }
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

function getRelatedProjects($db, $projectID) {
  $project_related = array();

  try {
    $query = $db->prepare('SELECT * FROM project_related WHERE project_related_site_id = ?');
    $query->execute(array($projectID));

    $results = $query->fetch(PDO::FETCH_ASSOC);

    foreach($results as $key => $value) {
      $project_related[$key] = $value;
    }
  } catch (PDOException  $e ) {
    http_response_code(500);
    die('PDO Error: ' . $e);
  }

  return $project_related;
}

function getServiceContent(PDO $db, String $name) {
  global $response;
  $service_content = array();
  $success = false;

  try {
    $query = $db->prepare('Select * from services_content where services_content_page = ?');
    $success = $query->execute(array($name));
  
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $service_content[$row['services_content_type']] = $row['services_content_value'];
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

function sendEmail($email, $token, $method) {
  global $SesClient;
  global $ses_settings;
  $success = false;
  $link = '';
  $subject = '';
  $plaintext_body = '';

  switch ($method) {
    case 'create':
      $subject = 'Verify your new account';
      $link = $ses_settings['verify_url'] . '?token=' . $token;
      $plaintext_body = 'You must confirm your email address in order to activate your account. Follow this link to verify: ' . $link;
      $html_body = sprintf(file_get_contents(__DIR__ . '/../email_verify.tmpl.html'), $link, $link);
      break;
    case 'reset':
      $subject = 'Reset your password';
      $link = $ses_settings['reset_url'] . '?token=' . $token;
      $plaintext_body = 'A request has been made to reset the password for this account. Follow this link to reset: ' . $link;
      $html_body = sprintf(file_get_contents(__DIR__ . '/../email_reset.tmpl.html'), $link, $link);
      break; 
    default:
      break;
  }

  $char_set = 'UTF-8';

  try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => [$email],
        ],
        'ReplyToAddresses' => [$ses_settings['sender']],
        'Source' => $ses_settings['sender'],
        'Message' => [
          'Body' => [
              'Html' => [
                  'Charset' => $char_set,
                  'Data' => $html_body,
              ],
              'Text' => [
                  'Charset' => $char_set,
                  'Data' => $plaintext_body,
              ],
          ],
          'Subject' => [
              'Charset' => $char_set,
              'Data' => $subject,
          ],
        ],
        // If you aren't using a configuration set, comment or delete the
        // following line
        // 'ConfigurationSetName' => $configuration_set,
    ]);
    $messageId = $result['MessageId'];
    $success = true;
  } catch (AwsException $e) {
      // output error message if fails
      echo $e->getMessage();
      echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
      echo "\n";
  }

  return $success;
}

?>