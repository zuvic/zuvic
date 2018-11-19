<?php

require_once(__DIR__ . '/../../settings.inc');

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

try {
    $query = $db->prepare('Select * from project_related where project_related_site_id=?');
    $query->execute(array($project_site_id));
  
    while($row=$query->fetch(PDO::FETCH_ASSOC)) {
        foreach($row as $key => $value) {
            $project_related[$key] = $value;
        }
    }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

try {

    $primary = '';
    $secondary = '( ';

    foreach($project_related as $related_key => $related) {
        if($related_key == 'project_related_id' || $related_key == 'project_related_site_id') continue;

        if($project_related[$related_key] != null) {
            $primary .= $related_key . ' IS NOT NULL AND ';
        } else {
            if($secondary !== '( ') {
                $secondary .= 'OR ';
            }
            $secondary .= $related_key . ' IS NOT NULL ';
        }

    }

    $secondary .= ')';

    $stmt = 'Select * from project_related where ' . $primary . $secondary;
    $query = $db->prepare($stmt);
    $query->execute();
        
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $related_projects[$row['project_related_id']] = array();

        foreach($row as $key => $value) {
            $related_projects[$row['project_related_id']][$key] = $value;
        }

        $nested_query = $db->prepare('Select project_site_name from project_site where project_site_id = ?');
        $nested_query->execute(array($row['project_related_site_id']));

        while($nested_row = $nested_query->fetch(PDO::FETCH_ASSOC)) {
            $related_projects[$row['project_related_id']]['title'] = $nested_row['project_site_name'];
        }
    }
} catch (PDOException  $e ) {
  echo "Error: " . $e;
}

try {
    $query = $db->prepare('Select * from project_content where project_content_site_id=?');
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
  echo "";
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
                    <?php echo isset($project_content['title']) ? $project_content['title'][0] : $project_name_url; ?>
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
            <div class="text-wrapper">
                <div class="title">Highlights</div>
                <div class="text">
                    <ul>
                        <?php 
                                foreach($project_content['highlights'] as $challenge) {
                                    echo "<li>$challenge</li>";
                                }    
                            ?>
                    </ul>
                </div>
            </div>
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
                    $url = '/project/' . implode('-', explode(' ', $related['title'])) . '/';

                    echo '<a href="' . $url . '"><div class="project" style="background-image: url(\'/images/'.$related['project_related_site_id'].'/1.jpg\'), url(\'/images/blank.jpg\')"><div class="caption">'.$related['title'].'</div></div></a>';
                }    
            ?>
        </div>
    </div>

    <div id="contact-section">
        <div class="header">Need a solution?</div>
        <button type="button">Let's Connect.</button>
    </div>

    <div id="footer">
        <img src="/images/logo_vector_white.svg">
        <img src="/images/social.svg" style="height: 28px; top:50%; float: right; margin-right:15%">
    </div>

    </div>
    </div>
</body>

<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/45226/material-photo-gallery.min.js"></script>
<script>
    var gallery = generateProjImgs(<?php echo $project_site_id ?>, <?php echo(int) $project_content['photos'][0] ?>);
</script>

</html>