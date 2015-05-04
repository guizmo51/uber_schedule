'use strict';
var clientId = 'IAhDficz9G3oQXGqJk70G42F3eDEaZ-W';
var responseType = 'code';
/**
 * @ngdoc function
 * @name frontUberApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the frontUberApp
 */
angular.module('frontUberApp')
  .controller('MainCtrl', function ($scope, $window) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
    $scope.login = function() {
        $window.location.href='https://login.uber.com/oauth/authorize?client_id=rA_xJyeKF3srdRgdoGYlfPLG6eLA-uno&response_type='+responseType+'&scope=profile%20history_lite';
    };
  });
