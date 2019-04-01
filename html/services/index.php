<?php
require_once(__DIR__ . '/../../settings.inc');
require_once(__DIR__ . '/../../admin/api/index.php');
require_once(__DIR__ . '/../../admin/api/services.php');

global $db_settings;

preg_match('/(?:services)(.*$)/', $_SERVER['REQUEST_URI'], $url);

$url = preg_replace(array('/services\//', '/\//'), '', $url);

$service_name = $url[0];
$service_id = null;

$service_content = [];

$service_projects = array();

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
      $service_name = $row['services_site_name'];
    }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

$service_content = getServiceContent($db, $service_id);

$service_projects = getServiceProjects($db, $service_id);

?>
<html lang="en">

<head>
    <meta charset="utf-8">

    <title>Zuvic Services - <?php echo $service_name; ?></title>
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

    <div class="row banner" style="background-image: url(/images/services_<?php echo strtolower($service_name); ?>.jpg);">
        <div class="inner-wrapper">
            <div class="text-wrapper">
                <div class="title <?php echo strtolower($service_name); ?>">
                    <?php echo $service_name; ?>
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
                CUSTOMER FOCUS.<br>FOLLOW THROUGH.<br>TEAM WORK.<br>CONFIDENCE.<br>INITIATIVE.
                </div>
                <!-- <div class="sub-title">Full Time | Rocky Hill, CT</div> -->
                <img src="/images/social_small.svg" style="height: 18px">
            </div>
            <div class="column content">
                <?php 
                    echo '<div>' . $service_content['content'] . '</div>';
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

                foreach($service_projects as $idx => $service) {
                    if($idx >= 10) continue;
                    $url = '/project/' . implode('-', explode(' ', $service['project_site_name'])) . '/';

                    echo '<a href="' . $url . '"><div class="project" style="background-image: url(\'/images/'.$service['project_site_id'].'/1.jpg\'), url(\'/images/blank.jpg\')"><div class="caption">'.$service['project_site_name'].'</div></div></a>';
                }    
            ?>
        </div>
    </div>

    <div id="footer">
        <div class="top">
            <img src="/images/logo_vector_white.svg">
            <img class="social" src="/images/social.svg">
        </div>
        <div class="bottom">
            <div>&copy; 2019 Zuvic, Carr & Associates, Inc.</div>
            <div class="callout">Designed/Developed by Michael Zapatka: mzaptk@gmail.com</div>
        </div>
    </div>

</body>

</html>