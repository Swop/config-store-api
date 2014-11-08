(function(configStore, _, $) {
    "use strict";
    configStore.controller('DiffCtrl', ['$scope', '$window', function($scope, $window) {
        $scope.application = {};
        //$scope.otherApplication = {};
        $scope.newConfigKey = "";
        $scope.globalItemCounter = 0;
        $scope.states = {};
        $scope.viewType = 'edit';

        this.addConfigModal = $('#config-new-modal');
        this.addConfigModal.on('shown.bs.modal', function () {
            $('#new_config_key').focus();
        });

        this.copyRightToLeft = function(key) {
            var leftConf = this.getConfig($scope.application.config_items, key);
            var rightConfig = this.getConfig($scope.otherApplication.config_items, key);

            if (typeof leftConf == "undefined") {
                leftConf = _.clone(rightConfig, true);
                $scope.application.config_items[leftConf.key] = leftConf;
                $scope.application.config_items[leftConf.key].index = $scope.globalItemCounter;
                $scope.globalItemCounter += 1;
            } else {
                leftConf.value = rightConfig.value;
            }

            this.updateStates();
        };

        this.delete = function(key) {
            //$scope.application.config_items = _.reject($scope.application.config_items, {'key': key});
            delete $scope.application.config_items[key];

            this.updateStates();
        };

        this.submitNewConfigForm = function(isValid) {
            if (isValid) {
                this.addConfig();
            }
        };

        this.checkExistingConfigKey = function(key) {
            return !_.contains(_.keys($scope.application.config_items), key);
        };

        this.addConfig = function(){
            var key = $scope.newConfigKey;

            $scope.application.config_items[key] = {
                'key': key,
                'value': '',
                'index': $scope.globalItemCounter
            };
            $scope.globalItemCounter += 1;

            $scope.newConfigKey = '';
            $scope.newConfigForm.$setPristine();
            this.updateStates();

            this.addConfigModal.modal('hide');
        };

        this.updateStates = function() {
            var statesKeys;

            if (typeof $scope.otherApplication != 'undefined') {
                statesKeys = _.union(_.keys($scope.application.config_items), _.keys($scope.otherApplication.config_items));
            } else {
                statesKeys = _.keys($scope.application.config_items);
            }

            $scope.states = {};

            _.each(statesKeys, function(key) {
                if ($scope.viewType == 'edit') {
                    $scope.states[key] = 'edit';
                    return;
                }

                if ($scope.application.config_items.hasOwnProperty(key) && !$scope.otherApplication.config_items.hasOwnProperty(key)) {
                    $scope.states[key] = 'missing_right';
                } else if (!$scope.application.config_items.hasOwnProperty(key) && $scope.otherApplication.config_items.hasOwnProperty(key)) {
                    $scope.states[key] = 'missing_left';
                } else {
                    if ($scope.application.config_items[key].value === $scope.otherApplication.config_items[key].value) {
                        $scope.states[key] = 'identical';
                    } else {
                        $scope.states[key] = 'different';
                    }
                }
            });
        };


        this.saveConfig = function(){
            var form = $('form[data-role="save-config-form"]');

            var appSlug = $scope.application.slug;
            var formObject = form.serializeObject();

            if (typeof formObject.appConfig == "undefined") {
                formObject.appConfig = {
                    configItems: []
                };
            } else {
                var cpt = 0;
                formObject.appConfig.configItems = _.reduce(formObject.appConfig.configItems, function(results, config) {
                    if(typeof config != "undefined") {
                        results[cpt] = config;
                    }
                    cpt += 1;

                    return results;
                }, {});
            }

            var json = JSON.stringify(formObject);
            console.log(json);

            $.ajax({
                url: "/admin/apps/"+appSlug+"/config/update",
                data: json,
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                statusCode: {
                    200: function() {
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    if (xhr.status != 200) {
                        console.log(xhr.status);
                        alert('Error');
                    }
                }
            });
        };

        this.getConfig = function(configItems, key) {
            return _.find(configItems, {'key': key});
        };

        this.init = function () {
            var viewType = 'edit';
            $scope.application = $window.diff_data.application;

            $scope.application.config_items = _.reduce($window.diff_data.application.config_items, function(results, config) {
                config.index = $scope.globalItemCounter;
                $scope.globalItemCounter += 1;
                results[config.key] = config;

                return results;
            }, {});

            if (typeof $window.diff_data.otherApplication != 'undefined') {
                $scope.otherApplication = $window.diff_data.otherApplication;

                $scope.otherApplication.config_items = _.reduce($window.diff_data.otherApplication.config_items, function(results, config) {
                    results[config.key] = config;
                    return results;
                }, {});

                viewType = 'diff';
            }

            $scope.viewType = viewType;

            this.updateStates();
        };
    }]);
})(window.config_store, _, $);
