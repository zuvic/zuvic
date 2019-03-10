<?php
require_once(__DIR__ . '/../../settings.inc');

global $db_settings;

preg_match('/(?:services)(.*$)/', $_SERVER['REQUEST_URI'], $url);

$url = preg_replace(array('/services\//', '/\//'), '', $url);

$service_name = $url[0];
$servive_id = null;

$services_content = [];

$services_projects = array();

$services_aliases = [
  'environmental' => 'enviro',
  'permitting' => 'perm',
  'water' => 'water',
  'transport' => 'transport',
  'civil' => 'civil',
  'survey' => 'survey',
  'planning' => 'planning',
  'construction' => 'const',
  'structural' => 'structural',
  'bridges' => 'bridges',
  'utility' => 'utility'
];

$host = $db_settings["host"];
$db_name = $db_settings["name"];
$username = $db_settings["username"];
$password = $db_settings["password"];

try {
  $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

try {
    $query = $db->prepare('Select * from services_site where services_site_name=?');
    $query->execute(array($service_name));
  
    while($row=$query->fetch(PDO::FETCH_ASSOC)) {
      $service_id = $row['services_site_id'];
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }

try {
  $query = $db->prepare('Select * from services_content where services_content_site_id=?');
  $query->execute(array($service_id));

  while($row=$query->fetch(PDO::FETCH_ASSOC)) {
    $services_content[$row['services_content_idx']][$row['services_content_type']] = $row['services_content_value'];
  }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

$service_related_cats = [];

try {
    $service_query = $db->prepare('Select * from services_related where services_related_site_id = ?');
    $success = $service_query->execute(array($service_id));
  
    if($success) {
      while($row=$service_query->fetch(PDO::FETCH_ASSOC)) {
        foreach($row as $cat => $val) {
          if($cat == 'services_related_id' || $cat == 'services_related_site_id') continue;

          if((int) $val > 0) $service_related_cats[] = str_replace("services_related_", "", $cat);
        }
      }
    }
  } catch (PDOException  $e ) {
    echo "Error: " . $e;
  }


$order_sql = '';
$service_projects = array();
$success = false;

$service_sql = '';

  $service_sql .= in_array('survey', $service_related_cats) ? ' pr.project_related_survey > 0 AND ' : ' pr.project_related_survey <= 0 AND ';
  $service_sql .= in_array('planning', $service_related_cats) ? ' pr.project_related_planning > 0 AND ' : ' pr.project_related_planning <= 0 AND ';
  $service_sql .= in_array('civil', $service_related_cats) ? ' pr.project_related_civil > 0 AND ' : ' pr.project_related_civil <= 0 AND ';
  $service_sql .= in_array('transport', $service_related_cats) ? ' pr.project_related_transport > 0 AND ' : ' pr.project_related_transport <= 0 AND ';
  $service_sql .= in_array('structural', $service_related_cats) ? ' pr.project_related_structural > 0 AND ' : ' pr.project_related_structural <= 0 AND ';
  $service_sql .= in_array('bridges', $service_related_cats) ? ' pr.project_related_bridges > 0 AND ' : ' pr.project_related_bridges <= 0 AND ';
  $service_sql .= in_array('utility', $service_related_cats) ? ' pr.project_related_utility > 0 AND ' : ' pr.project_related_utility <= 0 AND ';
  $service_sql .= in_array('water', $service_related_cats) ? ' pr.project_related_water > 0 AND ' : ' pr.project_related_water <= 0 AND ';
  $service_sql .= in_array('const', $service_related_cats) ? ' pr.project_related_const > 0 AND ' : ' pr.project_related_const <= 0 AND ';
  $service_sql .= in_array('perm', $service_related_cats) ? ' pr.project_related_perm > 0 AND ' : ' pr.project_related_perm <= 0 AND ';
  $service_sql .= in_array('esa', $service_related_cats) ? ' pr.project_related_esa > 0 AND ' : ' pr.project_related_esa <= 0 AND ';
  $service_sql .= in_array('sgc', $service_related_cats) ? ' pr.project_related_sgc > 0 AND ' : ' pr.project_related_sgc <= 0 AND ';
  $service_sql .= in_array('rap', $service_related_cats) ? ' pr.project_related_rap > 0 AND ' : ' pr.project_related_rap <= 0 AND ';
  $service_sql .= in_array('design_remid', $service_related_cats) ? ' pr.project_related_design_remid > 0 AND ' : ' pr.project_related_design_remid <= 0 AND ';
  $service_sql .= in_array('hazmat', $service_related_cats) ? ' pr.project_related_hazmat > 0 AND ' : ' pr.project_related_hazmat <= 0 AND ';
  $service_sql .= in_array('exp_test', $service_related_cats) ? ' pr.project_related_exp_test > 0 AND ' : ' pr.project_related_exp_test <= 0 AND ';
  $service_sql .= in_array('ast_ust', $service_related_cats) ? ' pr.project_related_ast_ust > 0 ' : ' pr.project_related_ast_ust <= 0 ';

 $order_sql .= in_array('survey', $service_related_cats) ? ' op.order_project_survey > 0 AND ' : ' op.order_project_survey <= 0 AND ';
 $order_sql .= in_array('planning', $service_related_cats) ? ' op.order_project_planning > 0 AND ' : ' op.order_project_planning <= 0 AND ';
 $order_sql .= in_array('civil', $service_related_cats) ? ' op.order_project_civil > 0 AND ' : ' op.order_project_civil <= 0 AND ';
 $order_sql .= in_array('transport', $service_related_cats) ? ' op.order_project_transport > 0 AND ' : ' op.order_project_transport <= 0 AND ';
 $order_sql .= in_array('structural', $service_related_cats) ? ' op.order_project_structural > 0 AND ' : ' op.order_project_structural <= 0 AND ';
 $order_sql .= in_array('bridges', $service_related_cats) ? ' op.order_project_bridges > 0 AND ' : ' op.order_project_bridges <= 0 AND ';
 $order_sql .= in_array('utility', $service_related_cats) ? ' op.order_project_utility > 0 AND ' : ' op.order_project_utility <= 0 AND ';
 $order_sql .= in_array('water', $service_related_cats) ? ' op.order_project_water > 0 AND ' : ' op.order_project_water <= 0 AND ';
 $order_sql .= in_array('const', $service_related_cats) ? ' op.order_project_const > 0 AND ' : ' op.order_project_const <= 0 AND ';
 $order_sql .= in_array('perm', $service_related_cats) ? ' op.order_project_perm > 0 AND ' : ' op.order_project_perm <= 0 AND ';
 $order_sql .= in_array('esa', $service_related_cats) ? ' op.order_project_esa > 0 AND ' : ' op.order_project_esa <= 0 AND ';
 $order_sql .= in_array('sgc', $service_related_cats) ? ' op.order_project_sgc > 0 AND ' : ' op.order_project_sgc <= 0 AND ';
 $order_sql .= in_array('rap', $service_related_cats) ? ' op.order_project_rap > 0 AND ' : ' op.order_project_rap <= 0 AND ';
 $order_sql .= in_array('design_remid', $service_related_cats) ? ' op.order_project_design_remid > 0 AND ' : ' op.order_project_design_remid <= 0 AND ';
 $order_sql .= in_array('hazmat', $service_related_cats) ? ' op.order_project_hazmat > 0 AND ' : ' op.order_project_hazmat <= 0 AND ';
 $order_sql .= in_array('exp_test', $service_related_cats) ? ' op.order_project_exp_test > 0 AND ' : ' op.order_project_exp_test <= 0 AND ';
 $order_sql .= in_array('ast_ust', $service_related_cats) ? ' op.order_project_ast_ust > 0 AND ' : ' op.order_project_ast_ust <= 0 AND ';


$order_sql .= 'op.order_project_type = "service"';
$service_sql .= 'ORDER BY op.order_project_idx ASC';

try {
  $service_query = $db->prepare(sprintf(<<<SQL
SELECT p.project_site_name AS `name`, pr.project_related_id AS project_id, pr.project_related_site_id, op.order_project_id AS id, op.order_project_idx AS idx, p.project_site_name AS title
FROM project_related pr

LEFT JOIN
(
    SELECT op.order_project_id,
           op.order_project_site_id,
           op.order_project_idx
    FROM order_project op

    WHERE %s
) op ON pr.project_related_site_id = op.order_project_site_id

LEFT JOIN project_site p
ON pr.project_related_site_id = p.project_site_id

WHERE %s
SQL
, $order_sql, $service_sql));
  $success = $service_query->execute();

  if($success) {
    while($row = $service_query->fetch(PDO::FETCH_ASSOC)) {
        $service_projects[] = array('name' => $row['name'], 'project_id' => $row['project_id'], 'id' => $row['id'], 'idx' => $row['idx'], 'title' => $row['title']);
    }
  }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

?>
<html lang="en">

<head>
    <meta charset="utf-8">

    <title>Zuvic Services - <? echo $service_name; ?></title>
    <meta name="description" content="Zuvic">
    <meta name="author" content="Michael Zapatka">
    <link rel="icon" type="image/svg" href="/images/favicon.svg?v=2">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <link rel="stylesheet" href="/css/masterPage.css">
    <link rel="stylesheet" href="/css/services.css">

    <script src="/js/master.js"></script>

</head>

<body>
    <!-- Nav -->
    <div class="nav">
        <div class="navwrapper">
            <div class="links" id="logo_wrapper">
                <a href="/">
                    <img class="logo" src="/images/logo_vector.svg">
                    </img>
                    <img class="logo_small" src="/images/logo_vector_small.svg">
                    </img>
                </a>
            </div>
            <div class="menu">
                <div class="links">
                    <a href="/#servicesnav">Services</a>
                    <div class="underline"></div>
                </div>
                <!-- <div class="links">
                        <a href="/news/">News</a>
                        <div class="underline"></div>
                    </div> -->
                <div class="links">
                    <a href="/careers/">Careers</a>
                    <div class="underline"></div>
                </div>
                <div class="links">
                    <a href="/about/">About</a>
                    <div class="underline"></div>
                </div>
            </div>
            <div id="contact-button">
                <a href="/contact/">Contact Us</a>
            </div>
        </div>
    </div>

    <div class="row banner" style="background-image: url(/images/services_<?php echo $service_name; ?>.jpg);">
        <div class="inner-wrapper">
            <div class="text-wrapper">
                <div class="title">
                    <?php echo strtoupper($service_name); ?>
                    <br>
                    SERVICES
                </div>
                <!-- <div class="sub-title">
                </div> -->
            </div>
        </div>
    </div>

    <div class="row content">
        <div class="inner-wrapper">
            <div class="column header">
                <div class="title">
                  FOCUS.
                  <br>
                  EXPRESSION.
                  <br>
                  CO-CREATION.
                </div>
                <!-- <div class="sub-title">Full Time | Rocky Hill, CT</div> -->
                <img src="/images/social_small.svg" style="height: 18px">
            </div>
            <div class="column content">
                <?php 
                  for($i = 0; $i < count($services_content); $i++) {
                    echo '<div>' . $services_content[$i]['content'] . '</div>';
                  }
                ?>
            </div>
        </div>
    </div>

    <div class="row projects">
        <div class="inner-wrapper">
            <div class="title-caption">
                Projects
            </div>
            <?php 
                $max = 0;

                foreach($service_projects as $service_key => $service) {
                    if($max >= 11) continue;
                    $url = '/project/' . implode('-', explode(' ', $service['title'])) . '/';

                    echo '<a href="' . $url . '"><div class="project" style="background-image: url(\'/images/'.$service['project_id'].'/1.jpg\'), url(\'/images/blank.jpg\')"><div class="caption">'.$service['title'].'</div></div></a>';

                    $max++;
                }    
            ?>
        </div>
    </div>

    <div id="footer">
        <img src="/images/logo_vector_white.svg">
        <img src="/images/social.svg" style="height: 28px; top:50%; float: right; margin-right:15%">
        <!-- <div class="info">40 Cold Spring Road, Rocky Hill, CT 06067<br>
            Phone: 860.436.4901<br>
            Fax: 860.436.4953<br>
            Email: info@zuvic.com</div> -->
    </div>

</body>

</html>