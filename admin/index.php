<?php
if(!isset($_SESSION)) { 
    session_start(); 
}

require_once(__DIR__ . '/../settings.inc');

global $settings;

?>
<!DOCTYPE html>
<html lang="en" ng-app="AngularCMS">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title ng-bind="$state.current.data.pageTitle"></title>
  <!-- Angular Material style sheet -->
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.css">
  <link rel="stylesheet" href="css/main.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="shortcut icon" href="/images/favicon_admin.png">
  <base href="/">

  <script>
    var base_url = '<?php echo $settings['base_url']; ?>';
  </script>
</head>

<body class="main">

  <div class="main" ng-controller="MainCtrl" layout="row">

    <md-sidenav class="main-menu md-sidenav-left" md-is-locked-open="true" md-component-id="left" md-whiteframe="4">

      <md-toolbar class="tall-toolbar" layout="column" layout-align="center center">
        <img layout-padding id="logo" src="/images/logo_vector_white.svg" alt="Zuvic Logo">
        <!-- <h1 class="md-toolbar-tools" flex layout="row" layout-align="center center">Zuvic Admin</h1> -->
      </md-toolbar>

      <md-menu-content class="nav">
        <md-menu-item>
          <md-button ng-click="gotoPage('projects')" class="md-primary" ng-class="{'md-raised': currentPage == 'projects'}">
            <span flex layout="row" layout-align="center center">Projects <i class="material-icons">keyboard_arrow_right</i></span>
          </md-button>
        </md-menu-item>
        <md-menu-item>
            <md-button ng-click="gotoPage('services')" class="md-primary" ng-class="{'md-raised': currentPage == 'services'}">
              <span flex layout="row" layout-align="center center">Services <i class="material-icons">keyboard_arrow_right</i></span>
            </md-button>
        </md-menu-item>
        <md-menu-item>
            <md-button ng-click="gotoPage('careers')" class="md-primary" ng-class="{'md-raised': currentPage == 'careers'}">
              <span flex layout="row" layout-align="center center">Careers <i class="material-icons">keyboard_arrow_right</i></span>
            </md-button>
        </md-menu-item>
        <span flex></span>
        <md-menu-item layout="row" layout-align="center end">
            <md-button ng-click="showMenu()" ng-disabled="false" class="md-primary" class="active">
              <div flex layout="row" layout-align="space-around center">
                <img ng-src="https://raw.githubusercontent.com/Ashwinvalento/cartoon-avatar/master/lib/images/male/105.png" class="md-avatar" />
                <div flex layout="row" layout-align="center center">{{ login.login_first }} {{ login.login_last }} <i class="material-icons">expand_less</i></div>
              </div>
            </md-button>
        </md-menu-item>
      </md-menu-content>
    </md-sidenav>

    <div layout="column" flex="100">
      <div ng-show="stateIsLoading" flex layout="column" layout-align="center center">
        <md-progress-circular layout-align="center center" md-mode="indeterminate"></md-progress-circular>
      </div>
      <div ui-view ng-hide="stateIsLoading" flex="100"></div>
    </div>
  </div>


  <!-- Angular Material requires Angular.js Libraries -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/1.0.20/angular-ui-router.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/1.0.20/stateEvents.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-messages.min.js"></script>

  <!-- Angular Material Library -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.js"></script>
  
  <script src="//cloud.tinymce.com/5/tinymce.min.js?apiKey=71egil4mb6tjwbu71s3vi3mpwrlaodf8tpnr74vwqucoayjx"></script>
  <script type="text/javascript" src="bower_components/angular-ui-tinymce/src/tinymce.js"></script>

  <script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
  <script src="js/AngularSortable.js"></script>
  <script src="js/main.js"></script>
  <script src="js/ng-file-upload-shim.min.js"></script> <!-- for no html5 browsers support -->
  <script src="js/ng-file-upload.js"></script>

</body>

</html>