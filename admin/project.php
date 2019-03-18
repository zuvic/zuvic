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

  <div class="main" ng-controller="ProjectCtrl" layout="row" flex="100">
  <div layout="column" flex="100">
    <md-progress-linear md-mode="indeterminate" class="child-page-loader" ng-hide="projectLoading === false"></md-progress-linear>

    <div layout="row" flex>
    <div layout="row" layout-padding layout-align="center center" flex="20">

      <div class="content-wrapper" flex>
        <div class="content project-selection" flex layout-padding md-whiteframe="4" layout="column" layout-align="center center">
          <h2 class="md-title">Select a Project</h2>
          <md-input-container layout-fill>
            <label><em>Project</em></label>
            <md-select ng-model="activeProject">
              <md-option></md-option>
              <md-option ng-repeat="project in projects | filter: ''| orderBy:'name'" ng-value="project.id">
                {{project.name}}
              </md-option>
            </md-select>
          </md-input-container>
          <br>
          <div layout="row" layout-md="column" layout-sm="column" layout-align-md="center center" layout-align-sm="center center" layout-align="center start" layout-fill class="no-margin">
            <md-input-container class="no-margin">
                <md-button class="md-raised md-primary" ng-click="createProject()" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
            </md-input-container>
            <md-input-container class="no-margin">
                <md-button class="md-warn" layout="row" ng-click="deleteProject()" layout-align="center center"><i class="material-icons">delete_sweep</i>&nbsp;Delete</md-button>
            </md-input-container>
          </div>
          <br>
        </div>
      </div>

    </div>

    <div layout="row" layout-padding layout-align="center center" flex="80">

      <div class="content main-container" ng-class="{'loading': projectLoading === true}" flex md-whiteframe="4">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="header" md-on-select="onTabChanges('content')" ng-disabled="activeProject === null">
            <md-content class="md-padding" layout="column">
            <div layout="row" class="no-margin">
              <p class="no-margin label smaller-text">URL Preview</p><br>
            </div>
            <div layout="row">
              <md-button class="md-primary" ng-disabled="activeProject === null" ng-href="{{projectURL}}" target="_blank">
                {{ projectURL }}
              </md-button>&nbsp;&nbsp;&nbsp;
              <div layout="row" flex="10" layout-align="center center">
                <md-button class="md-raised md-warn md-primary" ng-disabled="activeProject === null" ng-click="renameProject()" layout="row" layout-align="center center">
                  Rename
                </md-button>
              </div>
            </div>
              <md-input-container>
                <label>Page Name</label>
                <input ng-model="projectContent.title.value">
              </md-input-container>
              <md-input-container>
                <label>Sub-Title</label>
                <input ng-model="projectContent.subtitle.value">
              </md-input-container>
            </md-content>
          </md-tab>

          <md-tab label="challenges" md-on-select="onTabChanges('content')" ng-disabled="activeProject === null" >
            <md-content class="section-wrapper" layout-padding>
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" ng-repeat="(key, challenge) in projectContent.challenges track by $index" ng-show="challenge != null || challenge !== undefined" layout-fill>
                  <md-input-container>
                    <label>Challenge {{$index + 1}}</label>
                    <textarea ng-change="updateSection('challenges', key, projectContent.challenges[key].value)" ng-model="projectContent.challenges[key].value" rows="3" md-select-on-focus></textarea>
                  </md-input-container>
                  <div class="no-margin" layout="row" layout-align="end center">
                    <md-input-container class="no-margin">
                          <md-button class="md-warn no-margin" ng-click="deleteContent($index, 'challenges')" layout="row" layout-align="center center"><i class="material-icons" ng-hide="projectContent.challenges[key].delete">delete_sweep</i><md-progress-circular class="md-warn" ng-show="projectContent.challenges[key].delete" md-diameter="20px"></md-progress-circular></md-button>
                    </md-input-container>
                  </div>
                </div>
              </div>
              <div class="no-margin" layout="row" layout-align="start center">
                <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addContent('challenges')" ng-disabled="activeProject === null" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
                </md-input-container>
              </div>
            </md-content>
          </md-tab>

          <md-tab label="solutons" md-on-select="onTabChanges('content')" ng-disabled="activeProject === null">
          <md-content class="section-wrapper" layout-padding>
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" ng-repeat="(key, solution) in projectContent.solutions track by $index" layout-fill>
                  <md-input-container>
                    <label>Solution {{$index + 1}}</label>
                    <textarea ng-change="updateSection('solutions', key, projectContent.solutions[key].value)" ng-model="projectContent.solutions[key].value" md-select-on-focus></textarea>
                  </md-input-container>
                  <div class="no-margin" layout="row" layout-align="end center">
                    <md-input-container class="no-margin">
                          <md-button class="md-warn no-margin" ng-click="deleteContent($index, 'solutions')" layout="row" layout-align="center center"><i class="material-icons" ng-hide="projectContent.solutions[key].delete">delete_sweep</i><md-progress-circular class="md-warn" ng-show="projectContent.solutions[key].delete" md-diameter="20px"></md-progress-circular></md-button>
                    </md-input-container>
                  </div>
                </div>
              </div>
              <div class="no-margin" layout="row" layout-align="start center">
                <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addContent('solutions')" ng-disabled="activeProject === null" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
                </md-input-container>
              </div>
            </md-content>
          </md-tab>

          <md-tab label="highlights" md-on-select="onTabChanges('content')" ng-disabled="activeProject === null">
          <md-content class="section-wrapper" layout-padding>
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" ng-repeat="(key, highlight) in projectContent.highlights track by $index" layout-fill>
                  <md-input-container>
                    <label>Highlight {{$index + 1}}</label>
                    <textarea ng-change="updateSection('highlights', key, projectContent.highlights[key].value)" ng-model="projectContent.highlights[key].value" rows="3" md-select-on-focus></textarea>
                  </md-input-container>
                  <div class="no-margin" layout="row" layout-align="end center">
                    <md-input-container class="no-margin">
                          <md-button class="md-warn no-margin" ng-click="deleteContent($index, 'highlights')" layout="row" layout-align="center center"><i class="material-icons" ng-hide="projectContent.highlights[key].delete">delete_sweep</i><md-progress-circular class="md-warn" ng-show="projectContent.highlights[key].delete" md-diameter="20px"></md-progress-circular></md-button>
                    </md-input-container>
                  </div>
                </div>
              </div>
              <div class="no-margin" layout="row" layout-align="start center">
                <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addContent('highlights')" ng-disabled="activeProject === null" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add</md-button>
                </md-input-container>
              </div>
            </md-content>
          </md-tab>

          <md-tab label="images" md-on-select="onTabChanges('content')" ng-disabled="activeProject === null">
            <md-content class="section-wrapper">
              <md-subheader class="md-primary">
                <div layout="row" layout-align="space-between center">
                <span flex></span>
                <md-button class="md-raised md-primary no-margin" ng-click="showUpload()" ng-disabled="activeProject === null" layout="row" layout-align="center center">&nbsp;&nbsp;<i class="material-icons">add_photo_alternate</i>&nbsp;&nbsp;<span>Add an Image</span>&nbsp;&nbsp;</md-button>
                </div>
                </md-subheader>
                <image-loader project-content="projectContent" active-project="activeProject" time-stamp="timeStamp"></image-loader>
            </md-content>
          </md-tab>

          <md-tab label="related" md-on-select="onTabChanges('related')" ng-disabled="activeProject === null">
            <md-content class="section-wrapper" layout-padding>
                <div layout="row" layout-wrap flex>
                  <div flex="100" ng-repeat="(id, related) in projectRelated.keys track by $index">
                    <md-checkbox ng-checked="related.value" ng-click="setProjectRelatedKey(id)">
                     {{ related.related_cats_name | relatedkeys }}
                    </md-checkbox>
                  </div>
                </div>
            </md-content>
          </md-tab>
        </md-tabs>
        <div layout="row" layout-align="end end">
          <md-input-container>
                <md-button class="md-raised md-primary" ng-click="save()" ng-disabled="activeProject === null" layout="row" layout-align="center center"><i class="material-icons" ng-hide="saving">save_alt</i><md-progress-circular class="md-hue-1" ng-show="saving" md-theme="progressTheme" md-diameter="20px"></md-progress-circular><span ng-show="saving">&nbsp;</span>&nbsp;&nbsp;Save</md-button>
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
  <script src="js/AngularSortable.js"></script>
  <script src="js/main.js"></script>
  <script src="js/ng-file-upload-shim.min.js"></script> <!-- for no html5 browsers support -->
  <script src="js/ng-file-upload.js"></script>

</body>

</html>