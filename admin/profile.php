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
  <title ng-bind="$state.current.data.pageTitle"></title>
  <!-- Angular Material style sheet -->
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.css">
  <link rel="stylesheet" href="css/main.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="main" ng-app="AngularCMS">

  <div class="main" ng-controller="ProfileCtrl" layout="row" flex="100">

    <div layout="row" layout-padding layout-align="center center" flex="100">

      <div class="content" flex md-whiteframe="4">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="profile" md-on-select="onTabChanges('profile')">
            <md-content class="md-padding" layout="row">
              <md-input-container flex>
                <label>First Name</label>
                <input ng-model="login.login_first">
              </md-input-container>
              <md-input-container flex>
                <label>Last Name</label>
                <input ng-model="login.login_last">
              </md-input-container>
            </md-content>
            <md-content class="md-padding" layout="row">
              <md-input-container flex>
                <label>Email</label>
                <input ng-model="login.login_email">
              </md-input-container>
              <md-input-container flex>
                <label>Username</label>
                <input ng-disabled="login.login_type == 2" ng-model="login.login_user">
              </md-input-container>
            </md-content>
          </md-tab>

          <md-tab ng-disabled="login.login_type == 2" label="users" md-on-select="onTabChanges('users')">
            <md-content class="section-wrapper" layout-padding>
              <div class="no-margin" layout="row" layout-align="end center">
                <md-input-container class="no-margin">
                      <md-button class="md-raised md-primary" ng-click="addUser()" ng-disabled="users === []" layout="row" layout-align="center center"><i class="material-icons">add</i>&nbsp;Add User</md-button>
                </md-input-container>
              </div>
              <div layout="column" layout-align="start start">
                <div class="content-sections" layout="column" layout-fill>
                  <md-list class="md-dense">
                    <md-subheader class="md-no-sticky">Administrators</md-subheader>
                    <md-list-item class="md-3-line" ng-show="user.login_type == 1 && user.login_active == 1" ng-repeat="(key, user) in users track by $index">
                      <img ng-src="https://raw.githubusercontent.com/Ashwinvalento/cartoon-avatar/master/lib/images/male/105.png" class="md-avatar" />
                      <div class="md-list-item-text" layout="column">
                        <h3>{{ user.login_first }} {{ user.login_last }}</h3>
                        <h4>{{ user.login_user }}</h4>
                        <p>{{ user.login_type == 1 && 'Administrator' || 'Standard'}}</p>
                      </div>
                      <div layout="row" layout-align="center center">
                        <md-input-container>
                          <md-switch ng-model="user.login_type" ng-true-value="'{{1}}'" ng-false-value="'{{2}}'" ng-disabled="user.login_user == 'admin'" aria-label="Priviledge">
                          {{ user.login_type == 1 && 'Administrator' || 'Standard'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-switch ng-model="user.login_active" ng-true-value="'{{1}}'" ng-false-value="'{{0}}'" ng-disabled="user.login_user == 'admin'" aria-label="Active">
                          {{ user.login_active == 1 && 'Active' || 'Inactive'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-button class="md-raised md-warn" ng-click="resetPassword($index)" layout="row" layout-align="center center" ng-disabled="user.login_user == 'admin'"><span ng-hide="user.reset">Reset Password</span><md-progress-circular class="md-hue-1" md-theme="progressTheme" ng-show="user.reset" md-diameter="20px"></md-progress-circular></md-button>
                        </md-input-container>
                      </div>
                    </md-list-item>
                  </md-list>

                  <md-list class="md-dense">
                    <md-subheader class="md-no-sticky">Standard Users</md-subheader>
                    <md-list-item class="md-3-line" ng-show="user.login_type == 2 && user.login_active == 1" ng-repeat="(key, user) in users track by $index">
                      <img ng-src="https://raw.githubusercontent.com/Ashwinvalento/cartoon-avatar/master/lib/images/male/105.png" class="md-avatar" />
                      <div class="md-list-item-text" layout="column">
                        <h3>{{ user.login_first }} {{ user.login_last }}</h3>
                        <h4>{{ user.login_user }}</h4>
                        <p>{{ user.login_type == 1 && 'Administrator' || 'Standard'}}</p>
                      </div>
                      <div layout="row" layout-align="center center">
                        <md-input-container>
                          <md-switch ng-model="user.login_type" ng-true-value="'{{1}}'" ng-false-value="'{{2}}'" ng-disabled="user.login_user == 'admin'" aria-label="Priviledge">
                          {{ user.login_type == 1 && 'Administrator' || 'Standard'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-switch ng-model="user.login_active" ng-true-value="'{{1}}'" ng-false-value="'{{0}}'" ng-disabled="user.login_user == 'admin'" aria-label="Active">
                          {{ user.login_active == 1 && 'Active' || 'Inactive'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-button class="md-raised md-warn" ng-click="resetPassword($index)" layout="row" layout-align="center center" ng-disabled="user.login_user == 'admin'"><span ng-hide="user.reset">Reset Password</span><md-progress-circular class="md-hue-1" md-theme="progressTheme" ng-show="user.reset" md-diameter="20px"></md-progress-circular></md-button>
                        </md-input-container>
                      </div>
                    </md-list-item>
                  </md-list>

                  <md-list class="md-dense">
                    <md-subheader class="md-no-sticky">Inactive</md-subheader>
                    <md-list-item class="md-3-line" ng-show="user.login_active == 0" ng-repeat="(key, user) in users track by $index">
                      <img ng-src="https://raw.githubusercontent.com/Ashwinvalento/cartoon-avatar/master/lib/images/male/105.png" class="md-avatar" />
                      <div class="md-list-item-text" layout="column">
                        <h3>{{ user.login_first }} {{ user.login_last }}</h3>
                        <h4>{{ user.login_user }}</h4>
                        <p>{{ user.login_token !== null && 'Pending' || user.login_active == 0 && 'Deactivated'}}</p>
                      </div>
                      <div layout="row" layout-align="center center">
                        <md-input-container>
                          <md-switch ng-model="user.login_type" ng-true-value="'{{1}}'" ng-false-value="'{{2}}'" ng-disabled="user.login_user == 'admin'" aria-label="Priviledge">
                          {{ user.login_type == 1 && 'Administrator' || 'Standard'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-switch ng-model="user.login_active" ng-true-value="'{{1}}'" ng-false-value="'{{0}}'" ng-disabled="user.login_user == 'admin'" aria-label="Active">
                          {{ user.login_active == 1 && 'Active' || 'Inactive'}}
                          </md-switch>
                        </md-input-container>
                        <md-input-container>
                          <md-button class="md-raised md-warn" ng-click="resetPassword($index)" layout="row" layout-align="center center" ng-disabled="user.login_user == 'admin'"><span ng-hide="user.reset">Reset Password</span><md-progress-circular class="md-hue-1" md-theme="progressTheme" ng-show="user.reset" md-diameter="20px"></md-progress-circular></md-button>
                        </md-input-container>
                      </div>
                    </md-list-item>
                  </md-list>
                </div>
              </div>
            </md-content>
          </md-tab>

        </md-tabs>
        <div layout="row" layout-align="end end">
          <md-input-container>
                <md-button class="md-raised md-primary" ng-click="save()" ng-disabled="users === []" layout="row" layout-align="center center"><i class="material-icons" ng-hide="saving">save_alt</i><md-progress-circular class="md-hue-1" ng-show="saving" md-theme="progressTheme" md-diameter="20px"></md-progress-circular><span ng-show="saving">&nbsp;</span>&nbsp;&nbsp;Save</md-button>
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
  <script src="js/AngularSortable.js"></script>
  <script src="js/main.js"></script>
  <script src="js/ng-file-upload-shim.min.js"></script> <!-- for no html5 browsers support -->
  <script src="js/ng-file-upload.js"></script>

</body>

</html>

<!--
Copyright 2016-2018 Google Inc. All Rights Reserved. 
Use of this source code is governed by an MIT-style license that can be found in the LICENSE file at https://material.angularjs.org/latest/license.
-->