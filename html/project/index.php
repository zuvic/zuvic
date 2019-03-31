<?php

require_once(__DIR__ . '/../../settings.inc');
require_once(__DIR__ . '/../../admin/api/index.php');
require_once(__DIR__ . '/../../admin/api/projects.php');

global $db_settings;

preg_match('/(?:project)(.*$)/', $_SERVER['REQUEST_URI'], $url);

$url = preg_replace(array('/project\//', '/\//'), '', $url);

$project_name_url = ucwords(preg_replace('/\_|\-/', ' ', $url[0]));

$host = $db_settings["host"];
$db_name = $db_settings["name"];
$username = $db_settings["username"];
$password = $db_settings["password"];

$project_site_id = '';
$project_site_name = '';

$project_content = [
    'challenges' => [],
    'solutions' => [],
    'highlights' => [],
    'sub-title' => [],
    'title' => []
];
$project_related = array();
$related_projects = array();

try {
  $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

try {
  $query = $db->prepare('Select * from project_site where project_site_name=?');
  $query->execute(array($project_name_url));

  while($row=$query->fetch(PDO::FETCH_ASSOC)) {
    if($row['project_site_id']) {
      $project_site_id = $row['project_site_id'];
      $project_site_name = $row['project_site_name'];
    } else {
      header('Location: /');
      die();
    }
  }

  if($project_site_id == '') {
    header('Location: /');
    die();
  }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

$related_projects = getRelatedProjects($db, $project_site_id);

try {
    $query = $db->prepare(<<<SQL
        Select * from project_content where project_content_site_id=?
SQL
);
    $query->execute(array($project_site_id));
  
    while($row=$query->fetch(PDO::FETCH_ASSOC)) {
      if(!is_array(@$project_content[$row['project_content_type']])) $project_content[$row['project_content_type']] = [];
      $project_content[$row['project_content_type']][] = $row['project_content_value'];
    }

    if(count($project_content['sub-title']) == 0) $project_content['sub-title'][] = '';
    if(count($project_content['title']) == 0) $project_content['title'][] = '';
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

?>
<html lang="en">

<head>
    <meta charset="utf-8">

    <title>Zuvic</title>
    <meta name="description" content="Zuvic">
    <meta name="author" content="Michael Zapatka">
    <link rel="icon" type="image/svg" href="/images/favicon.svg?v=2">

    <link rel="stylesheet" href="/css/masterPage.css">
    <link rel="stylesheet" href="/css/project.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

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

    <div class="row banner" style="background-image: url(/images/<?php echo $project_site_id ?>/1.jpg);">
        <div class="inner-wrapper">
            <div class="text-wrapper">
                <div class="title">
                    <?php
                    echo isset($project_content['title']) && $project_content['title'][0] != '' ? $project_content['title'][0] : $project_name_url; 
                    ?>
                </div>
                <div class="sub-title">
                    <?php echo $project_content['sub-title'][0] ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row callouts">
        <div class="inner-wrapper">
            <div class="text-wrapper">
                <div class="title">Challenges</div>
                <div class="text">
                    <ul>
                        <?php 
                            foreach($project_content['challenges'] as $challenge) {
                                echo "<li>$challenge</li>";
                            }    
                            ?>
                    </ul>
                </div>
            </div>
            <div class="text-wrapper">
                <div class="title">Solutions</div>
                <div class="text">
                    <ul>
                        <?php 
                                foreach($project_content['solutions'] as $challenge) {
                                    echo "<li>$challenge</li>";
                                }    
                            ?>
                    </ul>
                </div>
            </div>
<?php
    if(count($project_content['highlights']) >= 1) {
        echo <<<HTML
            <div class="text-wrapper">
                <div class="title">Highlights</div>
                <div class="text">
                    <ul>
HTML;
    }
?>

                        <?php 
                                foreach($project_content['highlights'] as $challenge) {
                                    echo "<li>$challenge</li>";
                                }    
                            ?>
<?php
    if(count($project_content['highlights']) >= 1) {
        echo <<<HTML
                    </ul>
                </div>
            </div>
HTML;
    }
?>
        </div>
    </div>

    <div class="row">
        <div class="inner-wrapper">
            <div class="m-p-g">
                <div class="m-p-g__thumbs" data-google-image-layout data-max-height="400"></div>

                <div class="m-p-g__fullscreen"></div>
            </div>
        </div>
    </div>

    <div class="row related">
        <div class="inner-wrapper">
            <div class="title-caption">
                Related Projects
            </div>
            <?php

                foreach($related_projects as $related_key => $related) {
                    $url = '/project/' . implode('-', explode(' ', $related['name'])) . '/';

                    echo '<a href="' . $url . '"><div class="project" style="background-image: url(\'/images/'.$related['id'].'/1.jpg\'), url(\'/images/blank.jpg\')"><div class="caption">'.$related['name'].'</div></div></a>';
                }    
            ?>
        </div>
    </div>

    <div id="contact-section">
        <div class="header">Need a solution?</div>
        <a href="/contact/">
            <button type="button">Let's Connect.</button>
        </a>
    </div>

    <div id="footer">
        <img src="/images/logo_vector_white.svg">
        <img src="/images/social.svg" style="height: 28px; top:50%; float: right; margin-right:15%">
    </div>

    </div>
    </div>
</body>

<script src="/js/material-photo-gallery.min.js"></script>
<script>
    var gallery = generateProjImgs(<?php echo $project_site_id ?>, <?php echo(int) isset($project_content['photos']) && sizeof($project_content['photos']) > 0 ? $project_content['photos'][0] : 0 ?>);
</script>

</html>