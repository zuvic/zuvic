<?php
require_once(__DIR__ . '/../../settings.inc');

global $db_settings;

preg_match('/(?:services)(.*$)/', $_SERVER['REQUEST_URI'], $url);

$url = preg_replace(array('/services\//', '/\//'), '', $url);

$service_name = $url[0];

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
  $query = $db->prepare('Select * from services_content where services_content_page=?');
  $query->execute(array($service_name));

  while($row=$query->fetch(PDO::FETCH_ASSOC)) {
    $services_content[$row['services_content_idx']][$row['services_content_type']] = $row['services_content_value'];
  }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

try {
  $project_service_query = 'Select project_related_site_id from project_related where project_related_' . $services_aliases[$service_name] . ' IS NOT NULL ORDER BY project_related_' . $services_aliases[$service_name] . ' = 0, project_related_' . $services_aliases[$service_name] . ' asc';
  $projects_query = $db->prepare($project_service_query);
  $projects_query->execute();

  while($row=$projects_query->fetch(PDO::FETCH_ASSOC)) {
    $nested_query = $db->prepare('Select project_site_name from project_site where project_site_id = ?');
    $nested_query->execute(array($row['project_related_site_id']));

    while($nested_row = $nested_query->fetch(PDO::FETCH_ASSOC)) {
        $services_projects[] = array('id' => $row['project_related_site_id'], 'title' => $nested_row['project_site_name']);
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
                    echo '<div class="title">' . $services_content[$i]['title'] . '</div>';
                    echo '<div class="sub-title">' . $services_content[$i]['content'] . '</div>';
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

                foreach($services_projects as $services_key => $service) {
                    if($max >= 11) continue;
                    $url = '/project/' . implode('-', explode(' ', $service['title'])) . '/';

                    echo '<a href="' . $url . '"><div class="project" style="background-image: url(\'/images/'.$service['id'].'/1.jpg\'), url(\'/images/blank.jpg\')"><div class="caption">'.$service['title'].'</div></div></a>';

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