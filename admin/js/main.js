angular
  .module('AngularCMS', ['ngMaterial', 'ngMessages', 'ng-sortable', 'ngFileUpload', 'ui.router', 'ui.router.state.events', 'ui.tinymce'])
  .filter('projectkey', function() {
    return function(input) {
      return (!!input) ? input.replace(/project_related_/, '').charAt(0).toUpperCase() + input.replace(/project_related_/, '').substr(1).toLowerCase() : '';
    }
  })
  .filter('relatedkeys', function() {
    return function(input) {
      if (!input) return '';

      var words = input.split(/_|\s/);
      words = words.map(function(x){ return x.charAt(0).toUpperCase() + x.slice(1) });

      return words.join(' ');
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
    .state('careers', {
        url : '/careers',
        templateUrl : 'careers.php',
        controller : 'CareersCtrl',
        data : { pageTitle: 'Admin | Careers' },
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

    this.renameProject = function (id, name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'project', method: 'rename', id: id, name: name }
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

    this.getServices = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'list' }
      });
    };

    this.getServiceContent = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'content', id: id }
      });
    };

    this.getServiceRelated = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'get_related', id: id }
      });
    };

    this.saveServiceRelated = function (id, related) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'save_related', id: id, related: related }
      });
    };

    this.getServiceProjects = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'projects', id: id }
      });
    };

    this.saveServiceContent = function (id, content) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'save_content', id: id, content: content }
      });
    };

    this.saveServiceOrder = function (id, projects) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'service', method: 'save_order', id: id, projects: projects }
      });
    };


    this.getRelatedCats = function() {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'related', method: 'list'}
      });
    }

    // Careers API

    this.getCareers = function () {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'list' }
      });
    };

    this.getCareerPosition = function (id) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'get_position', id: id }
      });
    };

    this.createPosition = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'create_position', name: name }
      });
    };

    this.deletePosition = function (name) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'delete_position', name: name }
      });
    };

    this.savePositionContent = function (id, content) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'save_content', id: id, content: content }
      });
    };

    this.saveCareerOrder = function (order) {
      return $http({
        method: 'POST',
        url: 'api/',
        data: { type: 'careers', method: 'save_order', order: order }
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
        case 'careers':
          $rootScope.currentPage = 'careers';
          $state.go('careers');
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
    $scope.projectRenamed = {
      name: null,
      urlPreview: null
    };
    $scope.delete = '';
    $scope.activeProject = null;
    $scope.activeTab = null;
    $scope.saving = false;
    $scope.timeStamp = new Date().getTime();
    $scope.projectRelated = {};
    $scope.pendingUploads = {};
    $scope.projectLoading = false;
    $scope.projectURL = '';
    $scope.projectContent = {
      title: [''],
      subtitle: [''],
      photos: 0,
      challenges: [],
      highlights: [],
      solutions: []
    }

    // Load available projects menu
    $api.getProjectSite().then(function (response) {
      angular.forEach(response.data.data, function (value, key) {
        $scope.projects[parseInt(value.id)] = value;
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
        $scope.projectLoading = true;
        $api.getProjectContent(newVal).then(function (response) {
          $scope.projectContent = response.data.data;
          $scope.timeStamp = new Date().getTime();
          $scope.projectURL = window.location.protocol + "//" + base_url + "/project/" + $scope.projects[newVal]['name'].split(" ").join("-") + "/";
        

          $api.getRelatedCats(newVal).then(function (response) {
            $scope.projectRelated['keys'] = response.data.data;

            $api.getProjectRelated(newVal).then(function (response) {
              $timeout(function() {
                $scope.projectRelated['project_related_id'] = response.data.data['project_related_id'];
                angular.forEach($scope.projectRelated['keys'], function(value, id) {
                  $scope.projectRelated['keys'][id]['value'] = typeof response.data.data['project_related_key'] !== 'undefined' && response.data.data['project_related_key'] !== null && response.data.data['project_related_key'][id] === true ? true : false;
                });
                $scope.projectLoading = false;
              }, 1000);
            }, function (response) {
              $scope.projectLoading = false;
              if(response.data.msg != '') {
                Toast.showToast('error', response.data.msg);
              } else {
                Toast.showToast('error', 'Could not load related for project: ' + $scope.projects[parseInt(newVal) - 1].name, response.data.data);
              }
            });
          }, function (response) {
            $scope.projectLoading = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Could not load related categories');
            }
          });
        }, function (response) {        
          $scope.projectLoading = false;
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

    $scope.renameProject = function() {
      $mdDialog.show({
        controller: 'dialogProjectCtrl',
        templateUrl: 'rename_project.tmpl.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        scope: $scope,
        preserveScope: true,
        fullscreen: true
      });
    }


    $scope.setProjectRelatedKey = function (id) {
      $scope.projectRelated['keys'][id]['value'] = ($scope.projectRelated['keys'][id]['value'] === true) ? false : true;
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
      if($scope.projectRelated[service] == 0) {
        $scope.projectRelated[service] = 1;
      } else {
        $scope.projectRelated[service] = 0;
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
          $scope.projectRelated['project_related_id'] = response.data.data['project_related_id'];
          angular.forEach($scope.projectRelated['keys'], function(value, id) {
            $scope.projectRelated['keys'][id]['value'] = typeof response.data.data['project_related_key'] !== 'undefined' && response.data.data['project_related_key'] !== null && response.data.data['project_related_key'][id] === true ? true : false;
          });
          $scope.saving = false;
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
  .controller('CareersCtrl', ['$scope', '$mdDialog', 'Upload', '$timeout', 'Toast', 'API', function ($scope, $mdDialog, Upload, $timeout, Toast, $api) {

    $scope.careers = [];
    $scope.careerContent = {};
    $scope.activePosition = null;
    $scope.careerLoading = true;
    $scope.saving = false;
    $scope.activeTab = '';

    $scope.duration = [
      {'id': 1, 'name': 'Full Time'},
      {'id': 2, 'name': 'Part Time'},
      {'id': 3, 'name': 'Contract'}
    ];

    $scope.tinymceOptions = {
      menubar: false,
      plugins: "lists",
      block_formats: 'Header=h3;Sub-Header=h4;Text=p',
      toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
      content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        'css/services_mock.css'
      ]
    };

    // Load available projects menu
    $api.getCareers().then(function (response) {
      angular.forEach(response.data.data, function (value, key) {
        $scope.careers[key] = value;
      });

      $scope.careerLoading = false;
    }, function (response) {
      if(response.data.msg != '') {
        Toast.showToast('error', response.data.msg);
      } else {
        Toast.showToast('error', 'Could not load careers', response.data.data);
      }
    });

    // Watch for position switching
    $scope.$watch('activePosition', function (newVal, oldVal) {
      if (newVal != '' && newVal !== null) {
        $scope.careerLoading = true;

        $api.getCareerPosition(newVal).then(function (response) {
          $timeout(function() {
            $scope.careerContent = response.data.data;
            $scope.careerLoading = false;
          }, 1000);
        }, function (response) {        
          $scope.careerLoading = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Could not load data for career position: ' + $scope.careerss[parseInt(newVal)].title, response.data.data);
          }
        });
      }
    });

    $scope.save = function() {
      switch($scope.activeTab) {
        case 'content':
        case 'header':
          $scope.saving = true;
          $api.savePositionContent($scope.activePosition, $scope.careerContent).then(function (response) {
              $scope.careerContent = response.data.data;
              $api.getCareers().then(function (response) {
                $timeout(function() {
                  $scope.saving = false;
                  $scope.careers = [];
                  angular.forEach(response.data.data, function (value, key) {
                    $scope.careers[key] = value;
                  });
                
                    Toast.showToast('success', 'Successfully saved content for position: ' + $scope.careers[$scope.activePosition].title);
                }, 1000);
              }, function (response) {
                if(response.data.msg != '') {
                  Toast.showToast('error', response.data.msg);
                } else {
                  Toast.showToast('error', 'Could not load careers', response.data.data);
                }
              });
          }, function (response) {
            $scope.saving = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error saving content for position', response.data.data);
            }
          });
          break;
        case 'order':
          $scope.saving = true;
          $api.saveCareerOrder($scope.careers).then(function (response) {
                $timeout(function() {
                  $scope.saving = false;
                  $scope.careers = [];
                  angular.forEach(response.data.data, function (value, key) {
                    $scope.careers[key] = value;
                  });
                
                  Toast.showToast('success', 'Successfully saved career order');
                }, 1000);
          }, function (response) {
            $scope.saving = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error saving career order', response.data.data);
            }
          });
          break;
        default:
        Toast.showToast('error', 'Error saving changes');
      }
    }

    /* 
    * Page event handlers
    */
   $scope.createPosition = function() {
    $mdDialog.show({
      controller: 'dialogCareersCtrl',
      templateUrl: 'create_position.tmpl.html',
      parent: angular.element(document.body),
      clickOutsideToClose: true,
      scope: $scope,
      preserveScope: true,
      fullscreen: true
    });
  }

  $scope.deletePosition = function() {
    $mdDialog.show({
      controller: 'dialogCareersCtrl',
      templateUrl: 'delete_position.tmpl.html',
      parent: angular.element(document.body),
      clickOutsideToClose: true,
      scope: $scope,
      preserveScope: true,
      fullscreen: true
    });
  }

      /*
    * Tab handing
    */
   $scope.onTabChanges = function(currentTab){
    $scope.activeTab = currentTab;
  };

  // Utilities

  $scope.onUpdate = function($evt) {
    angular.forEach($scope.careers, function(value, key) {
      value.order = (key + 1);
    });
  }

  $scope.onStart = function($evt) {
    $scope.onUpdate();
  }

  $scope.sortableConf = {
    animation: 200,
    // forceFallback: true,
    onStart: $scope.onStart,
    onMove: $scope.onMove,
    onUpdate: $scope.onUpdate,
  }
    
  }])
  .controller('ServicesCtrl', ['$scope', '$mdDialog', 'Upload', '$timeout', 'Toast', 'API', function ($scope, $mdDialog, Upload, $timeout, Toast, $api) {
    $scope.activeService = null;
    $scope.services = [];
    $scope.serviceContent = {'id': 0, 'content': '', 'delete': false};
    $scope.serviceRelated = {};
    $scope.saving = false;
    $scope.serviceLoading = false;
    $scope.contentEditor = null;

    $scope.serviceProjects = [];

    $api.getServices().then(function (response) {
      $timeout(function() {
        $scope.services = response.data.data;
      }, 1000);
    }, function (response) {
      if(response.data.msg != '') {
        Toast.showToast('error', response.data.msg);
      } else {
        Toast.showToast('error', 'Could not load list of services: ', response.data.data);
      }
    });

    // Watch for project switching
    $scope.$watch('activeService', function (newVal, oldVal) {
      if (newVal != '' && newVal !== null) {
        $scope.serviceLoading = true;
        $api.getServiceContent(newVal).then(function (response) {
          $scope.serviceContent = response.data.data;

          $api.getServiceProjects(newVal).then(function (response) {
            $scope.serviceProjects = response.data.data;

            $api.getRelatedCats(newVal).then(function (response) {
              $scope.serviceRelated['keys'] = response.data.data;
  
              $api.getServiceRelated(newVal).then(function (response) {
                $timeout(function() {
                  $scope.serviceRelated['service_related_id'] = response.data.data['service_related_id'];
                  angular.forEach($scope.serviceRelated['keys'], function(value, id) {
                    $scope.serviceRelated['keys'][id]['value'] = typeof response.data.data['service_related_key'] !== 'undefined' && response.data.data['service_related_key'] !== null && response.data.data['service_related_key'][id] === true ? true : false;
                  });
                $scope.serviceLoading = false;
                }, 1000);
              }, function (response) {
                $scope.serviceLoading = false;
                if(response.data.msg != '') {
                  Toast.showToast('error', response.data.msg);
                } else {
                  Toast.showToast('error', 'Could not load related for service: ' + newVal.charAt(0).toUpperCase(), response.data.data);
                }
              });
            }, function (response) {
              $scope.serviceLoading = false;
              if(response.data.msg != '') {
                Toast.showToast('error', response.data.msg);
              } else {
                Toast.showToast('error', 'Could not load related categories');
              }
            });
          }, function (response) {
            $scope.serviceLoading = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Could not load projects for service: ' + newVal.charAt(0).toUpperCase(), response.data.data);
            }
          });
        }, function (response) {     
          $scope.serviceLoading = false;   
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
        case 'order':
          $scope.saveOrder();
          break;
        default:
          Toast.showToast('error', 'Invalid section to save');
      }
    }

    $scope.tinymceOptions = {
      menubar: false,
      plugins: "lists",
      block_formats: 'Header=h3;Sub-Header=h4;Text=p',
      toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
      content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        'css/services_mock.css'
      ]
    };

    $scope.saveRelated = function() {
      $scope.saving = true;

      $api.saveServiceRelated($scope.activeService, $scope.serviceRelated).then(function (response) {
        $scope.serviceRelated['service_related_id'] = response.data.data['service_related_id'];
        angular.forEach($scope.serviceRelated['keys'], function(value, id) {
          $scope.serviceRelated['keys'][id]['value'] = typeof response.data.data['service_related_key'] !== 'undefined' && response.data.data['service_related_key'] !== null && response.data.data['service_related_key'][id] === true ? true : false;
        });

        $api.getServiceProjects($scope.activeService).then(function (response) {
          $timeout(function() {
            $scope.serviceProjects = response.data.data;
            $scope.saving = false;
          }, 1000);
        }, function (response) {
          $scope.serviceLoading = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Could not load projects for service: ' + newVal.charAt(0).toUpperCase(), response.data.data);
          }
        });
      }, function (response) {
        $scope.saving = false;
        if(response.data.msg != '') {
          Toast.showToast('error', response.data.msg);
        } else {
          Toast.showToast('error', 'Error updating service related categories', response.data.data);
        }
      });
    }

    $scope.saveOrder = function () {
      $scope.saving = true;

      $api.saveServiceOrder($scope.activeService, $scope.serviceProjects).then(function (response) {
        $timeout(function() {
          $scope.serviceProjects = response.data.data;
          $scope.saving = false;
        }, 1000);
      }, function (response) {
        $scope.saving = false;
        if(response.data.msg != '') {
          Toast.showToast('error', response.data.msg);
        } else {
          Toast.showToast('error', 'Error updating service project order', response.data.data);
        }
      });
    }

    $scope.saveContent = function () {
      $scope.saving = true;
      $api.saveServiceContent($scope.activeService, $scope.serviceContent).then(function (response) {
        $timeout(function() {
          $scope.serviceContent = response.data.data;
          $scope.saving = false;
        }, 1000);
      }, function (response) {
        $scope.saving = false;
        if(response.data.msg != '') {
          Toast.showToast('error', response.data.msg);
        } else {
          Toast.showToast('error', 'Error updating service content', response.data.data);
        }
      });
    }

    // Utilities

    $scope.setServiceRelatedKey = function (id) {
        $scope.serviceRelated['keys'][id]['value'] = ($scope.serviceRelated['keys'][id]['value'] === true) ? false : true;
    }

    $scope.onUpdate = function($evt) {
      angular.forEach($scope.serviceProjects, function(value, key) {
        value.idx = (key + 1);
      });
    }

    $scope.onStart = function($evt) {
      $scope.onUpdate();
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
                $scope.projects[parseInt(value.id)] = value;
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
                $scope.projects[parseInt(value.id)] = value;
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

      $scope.renameSubmit = function() {
        var oldName = $scope.projects[$scope.activeProject]['name'];
        if($scope.renameProjectForm.$valid) {
          $scope.working = true;
          $api.renameProject($scope.activeProject, $scope.projectRenamed.name).then(function (response) {
            $timeout(function() {
              $scope.working = false;
              $scope.projects = [];
              angular.forEach(response.data.data, function (value, key) {
                $scope.projects[parseInt(value.id)] = value;
              });
              $scope.projectURL = window.location.protocol + "//" + base_url + "/project/" + $scope.projects[$scope.activeProject]['name'].split(" ").join("-") + "/";
              Toast.showToast('success', 'Successfully renamed project from: ' + oldName + 
              ' to ' + $scope.projectRenamed.name);
              $scope.projectRenamed.name = '';
              $mdDialog.hide();
            }, 500);
          }, function (response) {
            $scope.working = false;
            if(response.data.msg != '') {
              Toast.showToast('error', response.data.msg);
            } else {
              Toast.showToast('error', 'Error renaming project', response.data.data);
            }
          });
        } else {
          Toast.showToast('error', 'You must enter a project name');
        }
      }

      $scope.$watch(function() {
        return $scope.projectRenamed.name;
      }, function(newVal, oldVal) {
        if(typeof newVal === 'undefined' || newVal === null) return;
        $scope.projectRenamed.urlPreview = "/project/" + newVal.split(" ").join("-") + "/";
      });
  }])
  .controller('dialogCareersCtrl', ['$scope', '$mdDialog', 'Upload', '$timeout', 'Toast', 'API', function ($scope, $mdDialog, Upload, $timeout, Toast, $api) {
    $scope.working = false;

    $scope.createSubmit = function() {
      if($scope.createPositionForm.$valid) {
        $scope.working = true;
        $api.createPosition($scope.newPosition.name).then(function (response) {
          $timeout(function() {
            $scope.working = false;
            $scope.careers = [];
            angular.forEach(response.data.data, function (value, key) {
              $scope.careers[parseInt(value.id)] = value;
            });
            Toast.showToast('success', 'Successfully created career: ' + $scope.newPosition.name);
            $scope.newPosition.name = '';
            $mdDialog.hide();
          }, 500);
        }, function (response) {
          $scope.working = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error creating new position', response.data.data);
          }
        });
      } else {
        Toast.showToast('error', 'You must enter a position title');
      }
    }

    $scope.deleteSubmit = function() {
      if($scope.deletePostionForm.$valid) {
        $scope.working = true;
        $api.deletePosition($scope.delete).then(function (response) {
          $timeout(function() {
            $scope.working = false;
            $scope.careers = [];
            angular.forEach(response.data.data, function (value, key) {
              $scope.careers[parseInt(value.id)] = value;
            });
            Toast.showToast('success', 'Successfully deleted position: ' + $scope.delete);
            $scope.delete = '';
            $mdDialog.hide();
          }, 500);
        }, function (response) {
          $scope.working = false;
          if(response.data.msg != '') {
            Toast.showToast('error', response.data.msg);
          } else {
            Toast.showToast('error', 'Error deleting position', response.data.data);
          }
        });
      } else {
        Toast.showToast('error', 'You must enter a position name');
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
        }, function (response) {
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
          idx = [];
          angular.forEach(files, function (value, key) {
            $scope.projectContent.photos.value = String(parseInt($scope.projectContent.photos.value) + 1);
            idx.push(parseInt($scope.projectContent.photos.value));
            $scope.pendingUploads[parseInt($scope.projectContent.photos.value)] = 0
          });
          angular.forEach(files, function (file, key) {
            $scope.upload(file, idx[key]);
          });
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
