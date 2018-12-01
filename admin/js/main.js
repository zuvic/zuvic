angular
  .module('AngularCMS', ['ngMaterial', 'ngMessages', 'ng-sortable', 'angular-sortable-view', 'ngFileUpload', 'ui.router', 'ui.router.state.events'])
  .filter('servicekey', function() {
    return function(input) {
      return (!!input) ? input.replace(/project_related_/, '').charAt(0).toUpperCase() + input.replace(/project_related_/, '').substr(1).toLowerCase() : '';
    }
  })
  .filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
  })
  .config(function ($mdThemingProvider) {
    $mdThemingProvider.theme('default')
      .primaryPalette('blue')
      .accentPalette('red');

    $mdThemingProvider
      .definePalette('progressPalette', {
        '50': 'fff',
        '100': 'ff5722',
        '200': '039be5',
        '300': '84de69',
        '400': 'ef5350',
        '500': 'f44336',
        '600': 'e53935',
        '700': 'd32f2f',
        '800': 'c62828',
        '900': 'b71c1c',
        'A100': '84de69',
        'A200': 'f9df23',
        'A400': 'ff4a4a',
        'A700': 'd50000',
        'contrastDefaultColor': 'light',

        'contrastDarkColors': ['50', '100',
          '200', '300', '400', 'A100'],
        'contrastLightColors': undefined
      }).theme('progressTheme')
      .primaryPalette('progressPalette', {
        'default': '200',
        'hue-1': '50',
        'hue-2': '100',
        'hue-3': 'A400',
      });
  })
  .config(["$locationProvider", function($locationProvider) {
    $locationProvider.html5Mode(true);
  }])
  .service('TemplateInterceptor', function($rootScope, $state, $q) {
    var service = this;

    service.request = function(config) {
      return $q.resolve(config);
    };
    service.responseError = function(response) {
      if(response.status == 401) {
        $state.go('login');
        return $q.reject(response);
      } else {
        return $q.reject(response);
      }
    };
  })
  .config(['$stateProvider', '$urlRouterProvider', '$httpProvider', function($stateProvider, $urlRouterProvider, $httpProvider) {
    $urlRouterProvider.otherwise('/login');
    $httpProvider.interceptors.push('TemplateInterceptor');
    
    $stateProvider
      .state('services', {
        url : '/services',
        templateUrl : 'services.php',
        controller : 'ServicesCtrl',
        data : { pageTitle: 'Admin | Services' },
        resolve: {
          auth: ['$q', 'Login', '$rootScope', function ($q, Login, $rootScope) {
            var d = $q.defer();

            Login.status().then(function (response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
    
              d.resolve(true);
            }, function(response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
            });

            return d.promise;
          }]
        }
      })
      .state('reset', {
        url : '/reset?token',
        controller : 'ResetCtrl',
        data : { pageTitle: 'Admin | Reset Password' },
        resolve: {
          validate: ['$q', 'API', '$rootScope', '$stateParams', function ($q, $api, $rootScope, $stateParams) {
            var d = $q.defer();

            if(typeof $stateParams.token !== undefined) {
              $api.validateToken($stateParams.token).then(function (response) {
                d.resolve(response.data);
              }, function(response) {
                if(response.data.msg != '') {
                  d.reject(response.data.msg);
                } else {
                  d.reject(false);
                }
              });
            } else {
              d.reject(false);
            }

            return d.promise;
          }]
        }
      })
      .state('verify', {
        url : '/verify?token',
        controller : 'VerifyCtrl',
        data : { pageTitle: 'Admin | Activate Account' },
        resolve: {
          validate: ['$q', 'API', '$rootScope', '$stateParams', function ($q, $api, $rootScope, $stateParams) {
            var d = $q.defer();

            if(typeof $stateParams.token !== undefined) {
              $api.validateToken($stateParams.token).then(function (response) {
                d.resolve(response.data);
              }, function(response) {
                if(response.data.msg != '') {
                  d.reject(response.data.msg);
                } else {
                  d.reject(false);
                }
              });
            } else {
              d.reject(false);
            }

            return d.promise;
          }]
        }
      })
      .state('projects', {
        url : '/projects',
        templateUrl : 'project.php',
        controller : 'ProjectCtrl',
        data : { pageTitle: 'Admin | Projects' },
        resolve: {
          auth: ['$q', 'Login', '$rootScope', function ($q, Login, $rootScope) {
            var d = $q.defer();

            Login.status().then(function (response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
    
              d.resolve(true);
            }, function(response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
            });

            return d.promise;
          }]
        }
      })
      .state('profile', {
        url : '/profile',
        templateUrl : 'profile.php',
        controller : 'ProfileCtrl',
        data : { pageTitle: 'Admin | Profile' },
        resolve: {
          auth: ['$q', 'Login', '$rootScope', function ($q, Login, $rootScope) {
            var d = $q.defer();

            Login.status().then(function (response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
    
              d.resolve(true);
            }, function(response) {
              if(eval($rootScope.login) == null) {
                d.reject(false);
              }
            });

            return d.promise;
          }]
        }
      })
      .state('login', {
        url : '/login',
        controller : 'LoginCtrl',
        params: {
          msg: '',
          success: ''
        },
        data : { pageTitle: 'Admin | Login' }
      });
  }])
  .run(function($rootScope, $timeout, $state, $stateParams, Login, $transitions) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;

    $state.defaultErrorHandler(function(error) {
      // This is a naive example of how to silence the default error handler.
      // console.log(error);
    });

    $rootScope.$on('$stateChangeStart', 
      function(event, toState, toParams, fromState, fromParams){ 
        $rootScope.stateIsLoading = true;
      });

    $rootScope.$on('$stateChangeError', function (event, toState, toParams, fromState, fromParams, error) {
        /* 90% of the time, this is caused by a 401 of a destination page - In that case, we want to redirect to login
            in the unlikely case that it's not, login will redirect back to the project page with a successful auth */
        $state.go('login', {'msg': error.detail});
        angular.element(document.querySelector('.main-menu')).removeClass('active');
     });    
    
    $rootScope.$on('$stateChangeSuccess', 
      function(event, toState, toParams, fromState, fromParams){
        $rootScope.currentPage = toState.name;

        $timeout(function(){
          $rootScope.stateIsLoading = false;
          if(eval($rootScope.login) != null) {
            angular.element(document.querySelector('.main-menu')).addClass('active');
          }
        }, 1000);

        // Reset UI state for login
        if(toState.name == 'login') {
          angular.element(document.querySelector('.main-menu')).removeClass('active');
        }
    });

  })
  .service('API', function ($http) {

    this.getProjectSite = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'site' }
      });
    };

    this.getLogin = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'get' }
      });
    };

    this.login = function (username, pass) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'login', username: username, pass: pass }
      });
    };

    this.saveLogin = function (login) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'update_login', login: login }
      });
    };

    this.createLogin = function (login) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'create_login', login: login }
      });
    };

    this.validateToken = function (token) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'validate_token', token: token }
      });
    };

    this.resetPassword = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'reset_password', id: id }
      });
    };

    this.activateAccount = function (pass, id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'activate_account', pass: pass, id: id }
      });
    };

    this.updateUsers = function (users) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'profile', method: 'update_users', users: users }
      });
    };

    this.logout = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'login', method: 'logout' }
      });
    };

    this.getUsers = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'profile', method: 'get_users' }
      });
    };

    this.getProjectContent = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'content', id: id }
      });
    };

    this.getProjectRelated = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'related', id: id }
      });
    };

    this.createProject = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'create', name: name }
      });
    };

    this.deleteProject = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'delete', name: name }
      });
    };

    this.saveProjectContent = function (projID, content) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'save_content', projID: projID, content: content }
      });
    };

    this.saveProjectRelated = function (projID, related) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'save_related', projID: projID, related: related }
      });
    };

    this.deleteProjectContent = function (projID, contentID) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'delete_content', projID: projID, contentID: contentID }
      });
    };

    this.deleteImage = function (projID, filename) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'delete_image', projID: projID, filename: filename }
      });
    };

    this.updateImages = function (projID, order) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'update_images', projID: projID, order: order }
      });
    };

    // Service Page API

    this.getServiceContent = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'content', name: name }
      });
    };

    this.getServiceProjects = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'projects', name: name }
      });
    };

    this.saveServiceContent = function (name, content) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'save_content', name: name, content: content }
      });
    };

  })
  .service('Toast', function ($mdToast) {
    this.showToast = function (type, msg, details) {
      $mdToast.show({
        hideDelay: 10000,
        position: 'bottom right',
        controller: 'ToastCtrl',
        templateUrl: 'toast.tmpl.html',
        locals: { msg: msg, details: details },
        toastClass: type + '-toast'
      });
    };
  })
  .service('Login', ['API', '$q', '$rootScope', 'Toast', function ($api, $q, $rootScope, Toast) {
    var self = this;
    this.login = null;

    this.status = function() {
      var defer = $q.defer();

      $api.getLogin().then(function (response) {
        $rootScope.login = self.login = response.data.data;

        if(eval(self.login) != null) {
          defer.resolve(true);
        } else {
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          }
          defer.reject(false);
        }
      }, function (response) {
        $rootScope.login = self.login = null;
        defer.reject(false);
      });

      return defer.promise;
    }

    this.doLogin = function(username, password) {
      var defer = $q.defer();

      $api.login(username, password).then(function (response) {
        $rootScope.login = self.login = response.data.data;

        if(eval(self.login) != null) {
          defer.resolve(true);
        } else {
          defer.reject(response);
        }
      }, function (response) {
        $rootScope.login = self.login = null;
        defer.reject(response);
      });

      return defer.promise;
    }

    this.saveLogin = function() {
      var defer = $q.defer();

      $api.saveLogin(self.login).then(function (response) {
        $rootScope.login = self.login = response.data.data;

        if(eval(self.login) != null) {
          defer.resolve(true);
        } else {
          defer.reject(false);
        }
      }, function (response) {
        $rootScope.login = self.login = null;
        defer.reject(false);
      });

      return defer.promise;
    }

    this.doLogout = function(username, password) {
      var defer = $q.defer();

      $api.logout().then(function (response) {
        $rootScope.login = self.login = null;
        defer.resolve(true);
      }, function (response) {
        $rootScope.login = self.login = response.data.data;
        defer.reject(false);
      });

      return defer.promise;
    }

  }])
  .controller('MainCtrl', function($scope, $mdBottomSheet, Login, $state, $rootScope, $timeout) {
    $scope.login = Login.login;

    $scope.$watch(function() { return Login.login }, function(newVal) {
      $scope.login = newVal;
    });

    // Page handling
    $scope.gotoPage = function (page) {
      switch(page) {
        case 'projects':
          $rootScope.currentPage = 'projects';
          $state.go('projects');
          break;
        case 'services':
          $rootScope.currentPage = 'services';
          $state.go('services');
          break;
        default:
          break;
      }
    }

    $scope.showMenu = function() {
      $mdBottomSheet.show({
        templateUrl: 'login_menu.tmpl.html',
        controller: 'MenuCtrl',
        disableBackdrop: false,
        parent: '.nav',
        preserveScope: true,
        clickOutsideToClose: true,
        scope: $scope
      }).then(function(state) {
        switch(state) {
          case 'profile':
            $rootScope.currentPage = 'profile';
            $state.go('profile');
            break;
          case 'logout':
            Login.doLogout().then(function (response) {
              $state.go('login');
            }, function (response) {
              $state.go('login');
            });
          default:
        }
        $state.go('profile');
      }).catch(function(error) {
        // console.log(error);
      });
    };
  })
  .controller('MenuCtrl', function($scope, $mdBottomSheet) {
    $scope.navigateTo = function(state) {
      $mdBottomSheet.hide(state);
    }

    $scope.logout = function() {
      $mdBottomSheet.hide('logout');
    }
  })
  .controller('ProfileCtrl', ['$scope', 'Toast', 'Login', 'API', '$timeout', '$mdDialog', function ($scope, Toast, Login, $api, $timeout, $mdDialog) {
    $scope.login = Login.login;
    $scope.activeTab = null;
    $scope.creating_user = false;
    $scope.users = [];
    $scope.newUser = {};

    // Make sure to update local scope with active login session
    $scope.$watch(function() { return Login.login }, function(newVal) {
      $scope.login = newVal;
    });

    /*
    * Tab handing
    */
    $scope.onTabChanges = function(currentTab) {
     $scope.activeTab = currentTab;
    };

    /*
    * Initial Data Population
    */
    $api.getUsers().then(function (response) {
      handleUserData(response.data.data);
    }, function (response) {
      if(response.data.msg != '') {
        Toast.showToast('error', response.data.msg);
      } else {
        Toast.showToast('error', 'Could not load users', response.data.data);
      }
    });

    $scope.addUser = function () {
      $mdDialog.show({
        controller: 'ProfileCtrl',
        templateUrl: 'user.tmpl.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        scope: $scope,
        preserveScope: true,
        fullscreen: false
      });
    }

    $scope.save = function () {
      switch($scope.activeTab) {
        case 'profile':
          $scope.saveProfile();
          break;
        case 'users':
          $scope.saveUsers();
          break;
        default:
          Toast.showToast('error', 'Error saving changes');
      }
    }

    $scope.resetPassword = function(idx) {
      $scope.users[idx].reset = true;

      $timeout(function () {
        $api.resetPassword($scope.users[idx].login_id).then(function (response) {
          handleUserData(response.data.data);
          Toast.showToast('success', 'An email has been sent to the user with a link to reset their password');
        }, function (response) {
          $scope.users[idx].reset = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', "Error resetting user's password", response.data.data);
          }
        });
      }, 500);
    }

    $scope.saveProfile = function () {
      $scope.saving = true;

      $timeout(function () {
        Login.saveLogin().then(function (response) {
          $scope.saving = false;
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating profile', response.data.data);
          }
        });
      }, 500);
    }

    $scope.saveUsers = function () {
      $scope.saving = true;

      $timeout(function () {
        $api.updateUsers($scope.users).then(function (response) {
          $scope.saving = false;

          Toast.showToast('success', 'Updated user data');
          handleUserData(response.data.data);
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating users', response.data.data);
          }
        });
      }, 500);
    }

    $scope.submitNewUser = function() {
      $scope.creating_user = true;

      $timeout(function () {
        $api.createLogin($scope.newUser).then(function (response) {
          $scope.creating_user = false;

          $mdDialog.hide();
          Toast.showToast('success', 'Successfully created user: ' + $scope.newUser.login_user);
          handleUserData(response.data.data);
          $scope.newUser = {};
        }, function (response) {
          $scope.creating_user = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error creating new user', response.data.data);
          }
        });
      }, 500);

    }

    function handleUserData(data) {
      angular.forEach(data, function (value, key) {
        $scope.users[key] = value;

        // Add-in for password reset UX
        $scope.users[key]['reset'] = false;
      });
    }

  }])
  .controller('VerifyCtrl', ['$scope', '$mdDialog', '$state', '$stateParams', 'validate',  function ($scope, $mdDialog, $state, $stateParams, $validate) {
    $scope.id = $validate.data.login_id;

    $mdDialog.show({
      controller: 'VerifyDialogCtrl',
      templateUrl: 'verify.tmpl.html',
      parent: angular.element(document.body),
      clickOutsideToClose: false,
      scope: $scope,
      preserveScope: true,
      fullscreen: true
    });

  }])
  .controller('VerifyDialogCtrl', ['$scope', '$state', '$timeout', 'API', 'Toast', function($scope, $state, $timeout, $api, Toast) {
    $scope.logging_in = false;
    $scope.password = '';
    $scope.confirm_password = '';

    $scope.submit = function () {
      if($scope.verifyForm.$valid) {
        $scope.logging_in = true;
        
        $api.activateAccount($scope.confirm_password, $scope.id).then(function (response) {
          $timeout(function() {
            $state.go('login', {'success': 'Your account has been activated'});
          }, 500);
        }, function (response) {
          $scope.logging_in = false;  

          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error activating account');
          }
        });
      } else {
        Toast.showToast('error', 'Passwords must be valid and match before you can activate your account');
      }
    }
  }])
  .controller('ResetCtrl', ['$scope', '$mdDialog', '$state', '$stateParams', 'validate',  function ($scope, $mdDialog, $state, $stateParams, $validate) {
    $scope.id = $validate.data.login_id;

    $mdDialog.show({
      controller: 'ResetDialogCtrl',
      templateUrl: 'reset.tmpl.html',
      parent: angular.element(document.body),
      clickOutsideToClose: false,
      scope: $scope,
      preserveScope: true,
      fullscreen: true
    });

  }])
  .controller('ResetDialogCtrl', ['$scope', '$state', '$timeout', 'API', 'Toast', function($scope, $state, $timeout, $api, Toast) {
    $scope.logging_in = false;
    $scope.password = '';
    $scope.confirm_password = '';

    $scope.submitReset = function () {
      if($scope.resetForm.$valid) {
        $scope.logging_in = true;
        
        $api.activateAccount($scope.confirm_password, $scope.id).then(function (response) {
          $timeout(function() {
            $state.go('login', {'success': 'Your password has been reset'});
          }, 500);
        }, function (response) {
          $scope.logging_in = false;  

          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error resetting password');
          }
        });
      } else {
        Toast.showToast('error', 'Passwords must be valid and match before you can reset them');
      }
    }
  }])
  .controller('LoginCtrl', ['$scope', '$mdDialog', '$stateParams', function ($scope, $mdDialog, $stateParams) {
    $scope.msg = $stateParams['msg'];
    $scope.success = $stateParams['success'];

    $mdDialog.show({
      controller: 'LoginDialogCtrl',
      templateUrl: 'login.tmpl.html',
      parent: angular.element(document.body),
      clickOutsideToClose: false,
      scope: $scope,
      preserveScope: true,
      fullscreen: true
    });

  }])
  .controller('LoginDialogCtrl', ['$scope', '$state', '$timeout', 'Login', 'Toast', '$stateParams', function($scope, $state, $timeout, Login, Toast, $stateParams) {
    $scope.logging_in = true;
    $scope.username = '';
    $scope.password = '';

    /* Catch any prior errors in case we know we came from
       an error state */
    if($scope['msg'] != '') {
      $scope.logging_in = false;
      Toast.showToast('error', $scope['msg']);
    } else if($scope['success'] != '') {
      $scope.logging_in = false;
      Toast.showToast('success', $scope['success']);
    } else {
      // Initial login validation
      Login.status().then(function (response) {
        $timeout(function() {
          $state.go('projects');
        }, 500);
      }, function (response) {
        $scope.logging_in = false;
      });
    }

    $scope.submit = function () {
      $scope.logging_in = true;
      $timeout(function() {
        Login.doLogin($scope.username, $scope.password).then(function (response) {
            $state.go('projects');
        }, function (response) {
          $scope.logging_in = false;  

          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error logging in');
          }
        });
      }, 500);
    }
  }])
  .directive('equal', [
    function() {
    
    var link = function($scope, $element, $attrs, ctrl) {
    
      var validate = function(viewValue) {
        var comparisonModel = $attrs.equal;
          // console.log(viewValue + ':' + comparisonModel);
    
        if(!viewValue || !comparisonModel){
          // It's valid because we have nothing to compare against
          ctrl.$setValidity('equal', true);
        }
    
        // It's valid if model is lower than the model we're comparing against
        ctrl.$setValidity('equal', viewValue === comparisonModel );
        return viewValue;
      };
    
      ctrl.$parsers.unshift(validate);
      ctrl.$formatters.push(validate);
    
      $attrs.$observe('equal', function(comparisonModel){
            return validate(ctrl.$viewValue);
      });
    
    };
    
    return {
      require: 'ngModel',
      link: link
    };
  }])
  .controller('ProjectCtrl', ['$scope', '$timeout', '$mdSidenav', '$log', '$mdToast', '$timeout', 'API', 'Upload', '$mdDialog', 'Toast', '$mdSidenav', function ($scope, $timeout, $mdSidenav, $log, $mdToast, $timeout, $api, Upload, $mdDialog, Toast, $mdSidenav) {

    /* 
    * Initial global variable set
    */
    $scope.projects = [];
    $scope.newProject = {
      name: null
    };
    $scope.delete = '';
    $scope.activeProject = null;
    $scope.activeTab = null;
    $scope.saving = false;
    $scope.timeStamp = new Date().getTime();
    $scope.projectRelated = null;
    $scope.pendingUploads = {};
    $scope.projectContent = {
      title: [''],
      subtitle: [''],
      photos: 0,
      challenges: [],
      highlights: [],
      solutions: []
    }


    $scope.$watch(function () { return $scope.pendingUploads; }, function (newVal, oldVal) {
      console.log('Project level:', newVal);
    });

    // Load available projects menu
    $api.getProjectSite().then(function (response) {
      angular.forEach(response.data.data, function (value, key) {
        $scope.projects[key] = value;
      });
    }, function (response) {
      if(response.data.msg != '') {
        Toast.showToast('error', response.data.msg);
      } else {
        Toast.showToast('error', 'Could not load projects', response.data.data);
      }
    });

    // Watch for project switching
    $scope.$watch('activeProject', function (newVal, oldVal) {
      if (newVal != '' && newVal !== null) {
        $api.getProjectContent(newVal).then(function (response) {
          $scope.projectContent = response.data.data;
          $scope.timeStamp = new Date().getTime();

          $api.getProjectRelated(newVal).then(function (response) {
            $scope.projectRelated = response.data.data;
          }, function (response) {
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Could not load data for project: ' + $scope.projects[parseInt(newVal) - 1].name, response.data.data);
            }
          });
        }, function (response) {        
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Could not load data for project: ' + $scope.projects[parseInt(newVal) - 1].name, response.data.data);
          }
        });
      }
    });

    /*
    * Tab handing
    */
    $scope.onTabChanges = function(currentTab){
      $scope.activeTab = currentTab;
    };

    /* 
    * Page event handlers
    */
    $scope.createProject = function() {
      $mdDialog.show({
        controller: 'dialogProjectCtrl',
        templateUrl: 'create_project.tmpl.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        scope: $scope,
        preserveScope: true,
        fullscreen: true
      });
    }

    $scope.deleteProject = function() {
      $mdDialog.show({
        controller: 'dialogProjectCtrl',
        templateUrl: 'delete_project.tmpl.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        scope: $scope,
        preserveScope: true,
        fullscreen: true
      });
    }

    $scope.updateSection = function (type, id, value) {
      $scope.projectContent[type][id].value = value;
    }

    $scope.addContent = function (type) {
      $scope.projectContent[type].push({ delete: false, value: '', 'id': null});
    }

    $scope.deleteContent = function (key, type) {
      $scope.projectContent[type][key].delete = true;

      $timeout(function () {
        $api.saveProjectContent($scope.activeProject, $scope.projectContent).then(function (response) {
          $api.deleteProjectContent($scope.activeProject, $scope.projectContent[type][key].id).then(function (response) {
            $scope.projectContent = response.data.data;
            $scope.timeStamp = new Date().getTime();
          }, function (response) {
            $scope.projectContent[type][key].delete = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error updating project content', response.data.data);
            }
          });
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating project content', response.data.data);
          }
        });
      }, 500);
    }

    $scope.toggleRelatedCheckbox = function (service) {
      if($scope.projectRelated[service] !== null) {
        $scope.projectRelated[service] = null;
      } else {
        $scope.projectRelated[service] = "0";
      }
    }

    $scope.save = function () {
      switch($scope.activeTab) {
        case 'content':
          $scope.saveContent();
          break;
        case 'related':
          $scope.saveRelated();
          break;
        default:
          Toast.showToast('error', 'Error saving changes');
      }
    }

    $scope.saveRelated = function () {
      $scope.saving = true;

      $timeout(function () {
        $api.saveProjectRelated($scope.activeProject, $scope.projectRelated).then(function (response) {
          $scope.saving = false;
          $scope.projectRelated = response.data.data;
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating project services', response.data.data);
          }
        });
      }, 500);
    }

    $scope.saveContent = function () {
      $scope.saving = true;

      $timeout(function () {
        $api.saveProjectContent($scope.activeProject, $scope.projectContent).then(function (response) {
          $scope.saving = false;
          $scope.projectContent = response.data.data;
          $scope.timeStamp = new Date().getTime();
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating project content', response.data.data);
          }
        });
      }, 500);
    }

    

    // Handles file uploading
    $scope.showUpload = function () {
      console.log('Func level:', $scope.pendingUploads);
      $mdDialog.show({
        controller: 'UploadCtrl',
        templateUrl: 'upload.tmpl.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        scope: $scope,
        preserveScope: true,
        fullscreen: false
      });
    };

  }])
  .controller('ServicesCtrl', ['$scope', '$mdDialog', 'Upload', '$timeout', 'Toast', 'API', function ($scope, $mdDialog, Upload, $timeout, Toast, $api) {
    $scope.activeService = null;
    $scope.serviceContent = {};
    $scope.saving = false;

    $scope.serviceProjects = [];

    // Watch for project switching
    $scope.$watch('activeService', function (newVal, oldVal) {
      if (newVal != '' && newVal !== null) {
        $api.getServiceContent(newVal).then(function (response) {
          $scope.serviceContent = response.data.data;

          $api.getServiceProjects(newVal).then(function (response) {
            $scope.serviceProjects = response.data.data;
          }, function (response) {
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Could not load projects for service: ' + newVal.charAt(0).toUpperCase(), response.data.data);
            }
          });
        }, function (response) {        
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Could not load content for service: ' + newVal.charAt(0).toUpperCase(), response.data.data);
          }
        });
      }
    });

    /*
     * Tab handing
     */
    $scope.onTabChanges = function(currentTab){
      $scope.activeTab = currentTab;
    };

    $scope.save = function () {
      switch($scope.activeTab) {
        case 'content':
          $scope.saveContent();
          break;
        case 'related':
          $scope.saveRelated();
          break;
        default:
          Toast.showToast('error', 'Error saving changes');
      }
    }

    $scope.addContent = function() {
      $scope.serviceContent.push({'title': '', 'content': ''});
    }

    $scope.deleteContent = function(idx) {
      $scope.serviceContent[idx]['delete'] = true;

      $scope.saveContent();
    }

    $scope.saveContent = function () {
      $scope.saving = true;

      $timeout(function () {
        $api.saveServiceContent($scope.activeService, $scope.serviceContent).then(function (response) {
          $scope.saving = false;
          $scope.serviceContent = response.data.data;
        }, function (response) {
          $scope.saving = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error updating service content', response.data.data);
          }
        });
      }, 500);
    }

    $scope.onUpdate = function($evt) {
      // $timeout(function () {
      //   API.updateImages($scope.activeProject, $evt.models).then(function (response) {
      //     $scope.projectContent = response.data.data;
      //     $scope.timeStamp = new Date().getTime();
      //   }, function (response) {
      //     if(response.data.msg != '') {
      //       Toast.showToast('error', response.data.msg);
      //     } else {
      //       Toast.showToast('error', 'Could not update image order', response.data.data);
      //     }
      //   });
      // });
      console.log('stuff');
    }

    $scope.sortableConf = {
      animation: 200,
      // forceFallback: true,
      onStart: $scope.onStart,
      onMove: $scope.onMove,
      onUpdate: $scope.onUpdate,
    }
  }])
  .controller('dialogProjectCtrl', ['$scope', '$mdDialog', 'Upload', '$timeout', 'Toast', 'API', function ($scope, $mdDialog, Upload, $timeout, Toast, $api) {
      $scope.working = false;

      $scope.createSubmit = function() {
        if($scope.createProjectForm.$valid) {
          $scope.working = true;
          $api.createProject($scope.newProject.name).then(function (response) {
            $timeout(function() {
              $scope.working = false;
              $scope.projects = [];
              angular.forEach(response.data.data, function (value, key) {
                $scope.projects[key] = value;
              });
              Toast.showToast('success', 'Successfully created project: ' + $scope.newProject.name);
              $scope.newProject.name = '';
              $mdDialog.hide();
            }, 500);
          }, function (response) {
            $scope.working = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error creating new project', response.data.data);
            }
          });
        } else {
          Toast.showToast('error', 'You must enter a project name');
        }
      }

      $scope.deleteSubmit = function() {
        if($scope.deleteProjectForm.$valid) {
          $scope.working = true;
          $api.deleteProject($scope.delete).then(function (response) {
            $timeout(function() {
              $scope.working = false;
              $scope.projects = [];
              angular.forEach(response.data.data, function (value, key) {
                $scope.projects[key] = value;
              });
              Toast.showToast('success', 'Successfully deleted project: ' + $scope.delete);
              $scope.delete = '';
              $mdDialog.hide();
            }, 500);
          }, function (response) {
            $scope.working = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error deleting project', response.data.data);
            }
          });
        } else {
          Toast.showToast('error', 'You must enter a project name');
        }
      }
  }])
  .controller('UploadCtrl', function ($scope, $mdDialog, Upload, $timeout, Toast) {
      
      /*
      *  Initialize controller variables
      */
      $scope.uploading = false;
      $scope.percentage = 0;

      /*
      * General event handlers
      */
      $scope.hide = function () {
        $mdDialog.hide();
      };

      $scope.cancel = function () {
        $mdDialog.cancel();
      };

      $scope.pendingUploads[0] = 2;

      /*
      *  NG File Uploader Stuff
      */

      // Validate the form and file
      $scope.submit = function () {
        if ($scope.form.files.$valid && $scope.files) {
          $scope.uploadMultiple($scope.files);
        }
      };

      $scope.upload = function (file, idx) {
        $scope.uploading = true;

        Upload.upload({
          method: 'POST',
          url: 'api/',
          headers: { 'Content-Type': false, 'Cache-Control': 'no-cache' },
          data: { 'file': file, 'method': 'upload', 'type': 'project', 'projID': $scope.activeProject }
        }).then(function (response) {
          $scope.projectContent = response.data.data;
          $scope.pendingUploads[idx] = 50;
          $timeout(function () {
            $scope.uploading = false;
            $scope.hide();
            $scope.files = null;
            $scope.timeStamp = new Date().getTime();
            $scope.pendingUploads[idx] = 100
          }, 500);
        }, function (resp) {
          $timeout(function () {
            $scope.uploading = false;
            $scope.hide();
            $scope.files = null;
            $scope.pendingUploads[idx] = null
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Could not upload project image', resp.data);
            }
          }, 500);
        }, function (evt) {
          $scope.uploading = true;
          // $scope.pendingUpload[idx] = parseInt(100.0 * evt.loaded / evt.total);
          // console.log($scope.pendingUpload[idx]);
        });
      };

      // Handling multiple files
      $scope.uploadMultiple = function (files) {
        console.log($scope.pendingUploads, $scope.projectContent.photos);
        if (files && files.length) {
          if(files.length > 1) {
            idx = [];
            angular.forEach(files, function (value, key) {
              $scope.projectContent.photos.value = String(parseInt($scope.projectContent.photos.value) + 1);
              idx.push(parseInt($scope.projectContent.photos.value));
              $scope.pendingUploads[parseInt($scope.projectContent.photos.value)] = 0
            });
            angular.forEach(files, function (file, key) {
              $scope.upload(file, idx[key]);
            });
          } else {
            $scope.projectContent.photos.value = String(parseInt($scope.projectContent.photos.value) + 1);
            $scope.pendingUploads[parseInt($scope.projectContent.photos.value)] = 0
            $scope.upload(files, parseInt($scope.projectContent.photos.value));
          }
        }
        console.log($scope.pendingUploads, parseInt($scope.projectContent.photos.value));
      }
  })
  .controller('ToastCtrl', function ($scope, $mdToast, $mdDialog, msg, details) {
    var isDlgOpen = false;

    // Set local vars
    $scope.msg = msg;

    if (typeof details !== 'undefined') $scope.details = details;

    $scope.closeToast = function () {
      if (isDlgOpen) return;

      $mdToast
        .hide()
        .then(function () {
          isDlgOpen = false;
        });
    };

    $scope.openMoreInfo = function (e) {
      if (isDlgOpen) return;
      isDlgOpen = true;

      $mdDialog
        .show($mdDialog
          .alert()
          .title('Error Details')
          .textContent($scope.details)
          .ok('Close')
          .targetEvent(e)
        )
        .then(function () {
          isDlgOpen = false;
        });
    };
  })
  .directive('imageLoader', function ($timeout, Toast, API) {
    return {
      restrict: 'E',
      scope: {
        projectContent: '=',
        activeProject: '=',
        pendingUploads: '=',
        timeStamp: '='
      },
      template: `<ul ng-sortable="sortableConf" layout="row" layout-wrap>
                  <li ng-repeat="photo in photos" class="unselectable projectImage" style="background-image: url('/images/{{ activeProject }}/{{ $index + 1 }}.jpg?{{ timeStamp }}')" flex="33">
                    <md-progress-linear class="upload-progress" md-mode="determinate" value="{{ pendingUpload[$index + 1] }}"></md-progress-linear>
                    <md-button class="md-fab md-mini md-warn delete-image" ng-click="deleteImage($index)" layout="row" layout-align="center center">
                      <md-progress-circular class="md-hue-1" ng-show="delete[$index]" md-theme="progressTheme" md-diameter="20px"></md-progress-circular>
                      <i class="material-icons" ng-hide="delete[$index]">close</i>
                    </md-button>
                    <img ng-src="/images/{{ activeProject }}/{{ $index + 1 }}.jpg?{{ timeStamp }}" style="visibility: hidden">
                  </li>
                </ul>`,
      link: function ($scope, el) {

        $scope.delete = [];
        $scope.timeStamp = new Date().getTime();
        $scope.photos = [];

        $scope.$watch(function () { return $scope.projectContent; }, function (newVal, oldVal) {
          if (typeof newVal !== 'undefined') {
            $scope.photos = $scope.range(newVal.photos.value);
            angular.forEach($scope.photos, function (field, key) {
              $scope.delete[key] = false;
            });
          }
        });

        $scope.$watch(function () { return $scope.pendingUploads; }, function (newVal, oldVal) {
            console.log('Img:', newVal);
        });

        $scope.onUpdate = function($evt) {
          $timeout(function () {
            API.updateImages($scope.activeProject, $evt.models).then(function (response) {
              $scope.projectContent = response.data.data;
              $scope.timeStamp = new Date().getTime();
            }, function (response) {
              if(response.data.msg != '') {
                Toast.showToast('error', response.data.msg);
              } else {
                Toast.showToast('error', 'Could not update image order', response.data.data);
              }
            });
          });

        }


        $scope.deleteImage = function (idx) {
          $scope.delete[idx] = true;

          $timeout(function () {
            API.deleteImage($scope.activeProject, (idx + 1)).then(function (response) {
              $scope.projectContent = response.data.data;
              $scope.timeStamp = new Date().getTime();
            }, function (response) {
              $scope.delete[idx] = false;
              if(response.data.msg != '') {
                Toast.showToast('error', response.data.msg);
              } else {
                Toast.showToast('error', 'Could not delete image', response.data.data);
              }
            });
          });
        }

        $scope.sortableConf = {
          animation: 200,
          // forceFallback: true,
          onStart: $scope.onStart,
          onMove: $scope.onMove,
          onUpdate: $scope.onUpdate,
        }

        $scope.range = function (count) {
          var ratings = [];

          for (var i = 0; i < count; i++) {
            ratings.push(i)
          }
          return ratings;
        }
      }
    };
  })    
  .directive('eopdEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if (event.which === 13) {
                scope.$apply(function () {
                    scope.$eval(attrs.eopdEnter, {'event': event});
                });

                event.preventDefault();
            }
        });
    };
  });
