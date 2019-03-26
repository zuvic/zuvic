<?php
if(!isset($_SESSION)) { 
    session_start(); 
} 

require_once(__DIR__ . '/api/index.php');


if(!getLogin($db)) {
  http_response_code(401);
  exit();
}

?>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title ng-bind="$state.current.data.pageTitle">Admin</title>
  <!-- Angular Material style sheet -->
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.css">
  <link rel="stylesheet" href="css/main.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="main" ng-app="AngularCMS">

  <div class="main" ng-controller="CareersCtrl" layout="row" flex="100">
  <div layout="column" flex="100">
    <md-progress-linear md-mode="indeterminate" class="child-page-loader" ng-hide="careerLoading === false"></md-progress-linear>

    <div layout="row" flex>
    <div layout="row" layout-padding layout-align="center center" flex="20">

      <div class="content-wrapper" flex>
        <div class="content project-selection" flex layout-padding md-whiteframe="4" layout="column" layout-align="center center">
          <h2 class="md-title">Select a Position</h2>
          <md-input-container layout-fill>
            <label><em>Position</em></label>
            <md-select ng-model="activePosition">
              <md-option></md-option>
              <md-option ng-repeat="position in careers | filter: ''| orderBy:'title'" ng-value="position.id">
                {{position.title}}
              </md-option>
            </md-select>
          </md-input-container>
          <br>
          <div layout="row" layout-md="column" layout-sm="column" layout-align-md="center center" layout-align-sm="center center" layout-align="center start" layout-fill class="no-margin">
            <md-input-container class="no-margin">
                <md-button class="md-raised md-primary" ng-click="createPosition()" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
            </md-input-container>
            <md-input-container class="no-margin">
                <md-button class="md-warn" layout="row" ng-click="deletePosition()" layout-align="center center"><i class="material-icons">delete_sweep</i>&nbsp;Delete</md-button>
            </md-input-container>
          </div>
          <br>
        </div>
      </div>

    </div>

    <div layout="row" layout-padding layout-align="center center" flex="80">

      <div class="content main-container" ng-class="{'loading': careerLoading === true}" flex md-whiteframe="4">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="header" md-on-select="onTabChanges('header')" ng-disabled="activePosition === null">
            <md-content class="md-padding" layout="column">
              <md-switch md-invert ng-model="careerContent.active" ng-disabled="activePosition === null">
                Active
              </md-switch>
              <md-input-container>
                <label>Position Title</label>
                <input ng-model="careerContent.title">
              </md-input-container>
              <md-input-container>
                <label>Location</label>
                <input ng-model="careerContent.location">
              </md-input-container>
              <md-input-container>
                <label><em>Duration</em></label>
                <md-select ng-model="careerContent.duration">
                  <md-option></md-option>
                  <md-option ng-repeat="dur in duration" ng-value="dur.id">
                    {{dur.name}}
                  </md-option>
                </md-select>
              </md-input-container>
            </md-content>
          </md-tab>

          <md-tab label="content" md-on-select="onTabChanges('content')" ng-disabled="activePosition === null">
            <md-content class="section-wrapper" ng-class="{'layout-padding': activePosition !== null}">
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" layout-fill ng-hide="activePosition === null || careerLoading === true">
                  <md-subheader class="md-primary">Description</md-subheader>
                  <md-input-container>
                    <textarea id="career-description" ui-tinymce="tinymceOptions" ng-model="careerContent.description" rows="20" md-select-on-focus></textarea>
                  </md-input-container>
                  <md-subheader class="md-primary">Read More</md-subheader>
                  <md-input-container>
                    <textarea id="career-desc-extra" ui-tinymce="tinymceOptions" ng-model="careerContent.desc_extra" rows="20" md-select-on-focus></textarea>
                  </md-input-container>
                </div>
              </div>
              <div class="no-margin" layout="row" layout-align="start center">
              </div>
            </md-content>
          </md-tab>

          <md-tab label="order" md-on-select="onTabChanges('order')" ng-disabled="activePosition === null">
            <md-content class="section-wrapper" layout-padding sv-root>
                <ul ng-sortable="sortableConf">
                  <li ng-repeat="position in careers">
                    <div class="md-list-item-text" layout="row">
                      <md-button ng-disabled="careers.length < 2" class="md-fab md-mini md-primary">
                        <i class="material-icons">import_export</i>
                      </md-button>
                      <p>{{ position.title }}</p>
                    </div>
                    <md-divider ng-hide="careers.length < 2"></md-divider>
                  </li>
                </ul>
            </md-content>
          </md-tab>
        </md-tabs>
        <div layout="row" layout-align="end end">
          <md-input-container>
                <md-button class="md-raised md-primary" ng-click="save()" ng-disabled="activePosition === null" layout="row" layout-align="center center"><i class="material-icons" ng-hide="saving">save_alt</i><md-progress-circular class="md-hue-1" ng-show="saving" md-theme="progressTheme" md-diameter="20px"></md-progress-circular><span ng-show="saving">&nbsp;</span>&nbsp;&nbsp;Save</md-button>
          </md-input-container>
        </div>
      </div>
    </div>
    </div>
    </div>
  </div>


  <!-- Angular Material requires Angular.js Libraries -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.18/angular-ui-router.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-messages.min.js"></script>

  <!-- Angular Material Library -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.js"></script>

  <script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/ng-file-upload-shim.min.js"></script> <!-- for no html5 browsers support -->
  <script src="js/ng-file-upload.js"></script>

</body>

</html>