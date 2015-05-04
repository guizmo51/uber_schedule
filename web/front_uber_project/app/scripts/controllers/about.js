'use strict';

/**
 * @ngdoc function
 * @name frontUberApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the frontUberApp
 */
angular.module('frontUberApp')
  .controller('AboutCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });
