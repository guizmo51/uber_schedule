'use strict';

/**
 * @ngdoc overview
 * @name frontUberApp
 * @description
 * # frontUberApp
 *
 * Main module of the application.
 */
angular
  .module('frontUberApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch','ui.bootstrap', 'ui.router'
  ])
  .config(function ($routeProvider, $stateProvider, $urlRouterProvider) {

     $routeProvider.
      when('/requests', {
        templateUrl: 'views/requests.html',
      }) 

    $stateProvider
    
        // route to show our basic form (/form)
        .state('form', {
            url: '/form',
            templateUrl: 'views/form.html',
            controller: 'formController'
        })
        
        // nested states 
        // each of these sections will have their own view
        // url will be nested (/form/profile)
        .state('form.when', {
            url: '/when',
            templateUrl: 'views/form-when.html'
        })
        
        // url will be /form/interests
        .state('form.where', {
            url: '/where',
            templateUrl: 'views/form-where.html',
            controller : 'mapCtrl'
        })
        
        // url will be /form/payment
        .state('form.what', {
            url: '/what',
            templateUrl: 'views/form-what.html'

        }).state('toto', {
             templateUrl: 'views/requests.html',
             url:'/requests'
        });
        
    // catch all route
    // send users to the form page 

           
}).controller('formController', function($scope, $http) {
    
    // we will store all of our form data in this object

    $scope.formData = {};
    
    // function to process the form
    $scope.processForm = function() {
        alert('awesome!');
    };



}).controller('MenuCtrl', function($scope, $http) {
    
    
   $scope.login = function() { 
$http.get('https://localhost/uber_schedule/app_dev.php/api/user/redirect').
                
        success(function(data) {
          
         console.log(data);
        });
    };
   });



$(function() {
    console.log( "ready!" );
    console.log(new Date().getTimezoneOffset());
});