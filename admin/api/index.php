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
  require_once(__DIR__ . '/services.php');
  require_once(__DIR__ . '/projects.php');
  require_once(__DIR__ . '/common.php');
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
          } else if($data['method'] == 'rename' && isset($data['id']) && isset($data['name'])) {
            http_response_code(200);
            if(renameProject($db, $data['id'], $data['name'])) {
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
            $response['data'] = getProjectRelated($db, $data['id']);
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
              $response['data'] = getProjectRelated($db, $data['projID']);
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
          if($data['method'] == 'content' && isset($data['id'])) {
            http_response_code(200);
            $response['data'] = getServiceContent($db, $data['id']);
            break;
          } else if($data['method'] == 'list') {
            http_response_code(200);
            $response['data'] = getServices($db);
            break;
          } else if($data['method'] == 'projects' && isset($data['id'])) {
            http_response_code(200);
            $response['data'] = getServiceProjects($db, $data['id']);
            break;
          } else if($data['method'] == 'get_related' && isset($data['id'])) {
            http_response_code(200);
            $response['data'] = getServiceRelated($db, $data['id'], true);
            break;
          } else if($data['method'] == 'save_order' && isset($data['id']) && isset($data['projects'])) {
            http_response_code(200);
            if(saveServiceOrder($db, $data['id'], $data['projects']) === true) {
              http_response_code(200);
              $response['data'] = getServiceProjects($db, $data['id']);
            } else {
              http_response_code(500);
              die('Error saving service project order: ' . strtoupper($data['id']));
            }
            break;
          } else if($data['method'] == 'save_related' && isset($data['id'])) {
            http_response_code(200);
            if(saveServiceRelated($db, $data['id'], $data['related']) === true) {
              http_response_code(200);
              $response['data'] = getServiceRelated($db, $data['id'], true);
            } else {
              http_response_code(500);
              die('Error saving service related categories: ' . strtoupper($data['id']));
            }
            break;
          } else if($data['method'] == 'save_content' && isset($data['id'])) {
            http_response_code(200);
            if(saveServiceContent($db, $data['id'], $data['content']) === true) {
              http_response_code(200);
              $response['data'] = getServiceContent($db, $data['id']);
            } else {
              http_response_code(500);
              die('Error saving service content: ' . strtoupper($data['id']));
            }
            break;
          }
      case "related":
        if($data['method'] == 'list') {
          http_response_code(200);
          $response['data'] = getRelatedCats($db);
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