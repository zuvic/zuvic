<?php
require_once(__DIR__ . '/../../settings.inc');
require_once(__DIR__ . '/../../admin/api/index.php');
require_once(__DIR__ . '/../../admin/api/careers.php');

global $db_settings;

$host = $db_settings["host"];
$db_name = $db_settings["name"];
$username = $db_settings["username"];
$password = $db_settings["password"];

try {
  $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

$careers = array();
$careers_duration = array(
  '1' => 'Full Time',
  '2' => 'Part Time',
  '3' => 'Contract'
);

try {
  $query = $db->prepare(<<<SQL
Select * from careers order by careers_order
SQL
  );
  $success = $query->execute();

  while($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $careers[] = array('id' => $row['careers_id'], 'title' => $row['careers_title'], 'location' => $row['careers_location'], 'duration' => $careers_duration[$row['careers_duration']], 'active' => (bool) $row['careers_active'],
    'description' => $row['careers_description'], 'desc_extra' => $row['careers_desc_extra']);
  }
} catch (PDOException  $e ) {
  http_response_code(500);
  die('PDO Error: ' . $e);
}

?>
<html lang="en">

<head>
    <meta charset="utf-8">

    <title>Zuvic | Careers</title>
    <meta name="description" content="Zuvic">
    <meta name="author" content="Michael Zapatka">
    <link rel="icon" type="image/svg" href="/images/favicon.svg?v=2">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <link rel="stylesheet" href="/css/masterPage.css">
    <link rel="stylesheet" href="/css/careers.css">

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

    <div class="row banner" style="background-image: url(/images/interview.jpg);">
        <div class="inner-wrapper">
            <div class="text-wrapper">
                <div class="title">
                    Careers
                </div>
                <div class="sub-title">
                    Join Our Team
                </div>
            </div>
        </div>
    </div>

    <?php
    foreach ($careers as $idx => $content) {
        if ($content['active'] !== true) continue;
        echo <<<HTML
                <div class="row job">
                    <div class="inner-wrapper">
                        <div class="column header">
                            <img src="/images/social_small.svg" style="height: 18px">
                            <div class="title">{$content['title']}</div>
                            <div class="sub-title">{$content['duration']} | {$content['location']}</div>
                        </div>
                        <div class="column content">
                            <div class="title">DESCRIPTION</div>
                            <div class="sub-title">{$content['description']}</div>
                            <div class="drawer-button">Read More ></div>
                            <div class="drawer">{$content['desc_extra']}
                            </div>
                        </div>
                    </div>
                </div>
HTML;
    }
?>

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