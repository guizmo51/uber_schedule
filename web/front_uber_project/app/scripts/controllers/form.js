'use strict';

/**
 * @ngdoc function
 * @name mapsApp.controller:FormCtrl
 * @description
 * # FormCtrl
 * Controller of the mapsApp
 */


angular.module('frontUberApp').config(function ($routeProvider, $stateProvider, $urlRouterProvider) {

 $routeProvider.
      when('/requests', {
        templateUrl: 'views/requests.html',
      }).

$stateProvider
    
    
        
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
            templateUrl: 'views/form-where.html'
        })
        
        // url will be /form/payment
        .state('form.how', {
            url: '/how',
            templateUrl: 'views/form-how.html'
        });
        
    // catch all route
    // send users to the form page 
   
}
).controller('formController', function($scope) {
    
    // we will store all of our form data in this object
    $scope.formData = {};
    console.log("ok");
    // function to process the form
    $scope.processForm = function() {
        alert('awesome!');
    };
    
});
