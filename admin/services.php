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

  <div class="main" ng-controller="ServicesCtrl" layout="row" flex="100">

    <div layout="row" layout-padding layout-align="center center" flex="20">

      <div class="content-wrapper" flex>
        <div class="content project-selection" flex layout-padding md-whiteframe="4" layout="column" layout-align="center center">
          <h2 class="md-title">Select a Service</h2>
          <md-input-container layout-fill>
            <label><em>Service</em></label>
            <md-select ng-model="activeService">
              <md-option></md-option>
              <md-option ng-repeat="service in ['civil', 'transportation', 'construction', 'permitting', 'site-assessments', 'remidation', 'hazardous-materials', 'asts/usts']" ng-value="service">
                {{service | capitalize}}
              </md-option>
            </md-select>
          </md-input-container>
        </div>
      </div>

    </div>

    <div layout="row" layout-padding layout-align="center center" flex="80">

      <div class="content" flex md-whiteframe="4">
        <md-tabs md-dynamic-height md-border-bottom>
          <!-- <md-tab label="header" md-on-select="onTabChanges('content')">
            <md-content class="md-padding" layout="column">
              <md-input-container>
                <label>Header</label>
                <input ng-model="serviceContent.title">
              </md-input-container>
              <md-input-container>
                <label>Content</label>
                <textarea ng-model="serviceContent.content" rows="6"></textarea>
              </md-input-container>
            </md-content>
          </md-tab> -->

          <md-tab label="content" md-on-select="onTabChanges('content')">
            <md-content class="section-wrapper" layout-padding>
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" layout-fill> <!--  ng-repeat="(key, content) in serviceContent track by $index" -->
                  <!-- <md-input-container class="md-block" flex-gt-sm>
                    <label>Title</label>
                    <input ng-model="content.title">
                  </md-input-container> -->

                  <md-input-container>
                    <!-- <label>Content</label> -->
                    <textarea id="service-content" ui-tinymce="tinymceOptions" ng-model="serviceContent.content" rows="20" md-select-on-focus></textarea>
                  </md-input-container>
                  <div class="no-margin" layout="row" layout-align="end center">
                    <!-- <md-input-container class="no-margin">
                          <md-button class="md-warn no-margin" ng-click="deleteContent($index)" layout="row" layout-align="center center"><i class="material-icons" ng-hide="content.delete">delete_sweep</i><md-progress-circular class="md-warn" ng-show="content.delete" md-diameter="20px"></md-progress-circular></md-button>
                    </md-input-container> -->
                  </div>
                </div>
              </div>
              <div class="no-margin" layout="row" layout-align="start center">
                <!-- <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addContent()" ng-disabled="activeService === null" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
                </md-input-container> -->
              </div>
            </md-content>
          </md-tab>

          <md-tab label="order" md-on-select="onTabChanges('order')">
            <md-content class="section-wrapper" layout-padding sv-root>
              <md-divider ></md-divider>
              <!-- <md-list class="md-dense" flex> -->
                <ul ng-sortable="sortableConf">
                  <li ng-repeat="project in serviceProjects">
                    <div class="md-list-item-text" layout="row">
                      <md-button class="md-fab md-mini md-primary">
                        <i class="material-icons">import_export</i>
                      </md-button>
                      <p>{{ project.project_site_name }}</p>
                      <!-- <h4>{{ item.what }}</h4>
                      <p>{{ item.notes }}</p> -->
                    </div>
                    <md-divider ></md-divider>
                  </li>
                  <!-- <span flex></span>
                  <div layout="column" layout-align="center center">
                    <md-button class="md-fab md-mini md-raised md-primary">
                      <md-icon md-font-icon="material-icons" style="font-size:24px;">swap_vert</md-icon>
                    </md-button>
                  </div> -->
                </ul>
              <!-- </md-list> -->
              <!-- <div class="no-margin" layout="row" layout-align="start center">
                <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addContent('challenges')" ng-disabled="activeProject === null" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
                </md-input-container>
              </div> -->
            </md-content>
          </md-tab>

        </md-tabs>
        <div layout="row" layout-align="end end">
          <md-input-container>
                <md-button class="md-raised md-primary" ng-click="save()" ng-disabled="activeService === null" layout="row" layout-align="center center"><i class="material-icons" ng-hide="saving">save_alt</i><md-progress-circular class="md-hue-1" ng-show="saving" md-theme="progressTheme" md-diameter="20px"></md-progress-circular><span ng-show="saving">&nbsp;</span>&nbsp;&nbsp;Save</md-button>
          </md-input-container>
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

    <!-- <script src="vendor/jquery.min.js"></script>
    <script src="vendor/jquery.simulate.js"></script>
    <script src="vendor/jquery.simulate.ext.js"></script>
    <script src="vendor/jquery.simulate.drag-n-drop.js"></script> -->

  <script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.min.js"></script>
  <!-- <script src="https://kamilkp.github.io/angular-sortable-view/src/angular-sortable-view.js"></script> -->
  <!-- <script src="js/AngularSortable.js"></script> -->
  <script src="js/ng-file-upload-shim.min.js"></script> <!-- for no html5 browsers support -->
  <script src="js/ng-file-upload.js"></script>

  <script src="//cloud.tinymce.com/5/tinymce.min.js?apiKey=71egil4mb6tjwbu71s3vi3mpwrlaodf8tpnr74vwqucoayjx"></script>
  <script type="text/javascript" src="bower_components/angular-ui-tinymce/src/tinymce.js"></script>

  <script src="js/main.js"></script>

</body>

</html>

<!--
Copyright 2016-2018 Google Inc. All Rights Reserved. 
Use of this source code is governed by an MIT-style license that can be found in the LICENSE file at https://material.angularjs.org/latest/license.
-->