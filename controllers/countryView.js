
/*
 * Copyright 2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * The country view angular controller
 * 
 * Author: Michael E. Weigel
 */
var app = angular.module('countryViewApp', []);
app.controller('countryViewCtrl', function ($scope, $http) {
    $scope.selectedContinent = "All";
    $scope.selectedRegion = "All";
    $scope.selectedGovernment = "All";
    $scope.selectedLanguage = "All";
    $scope.n = 0;
    $scope.hide = true;
    $scope.continents = [];
    $scope.regions = [];
    $scope.governments = [];
    $scope.languages = [];
    $scope.countries = [];
    $scope.cols = [];
    $scope.searchCountry = '';  // set the default search/filter term
    $scope.sortType = 'Country'; // set the default sort type
    $scope.sortReverse = false;  // set the default sort order
    $scope.hideInput = true;
    $scope.table = undefined;
    $scope.viewList = ["Overview", "Capitol/Language/Population/Life Span", "Government/Capitol/Area/GNP/GNPIncr", "Continent/GNPIncr"];
    $scope.selectedView;


    window.onload = function () {
        $scope.table = document.getElementById("table");
    }


    // Functions for loading up selections lists

    // load Continents 
    $scope.loadContinents = function () {
        $http.get("http://localhost/countryView/countryViewMySql.php?choice=" + 20)
                .then(function (response) {
                    $scope.continents = response.data.continents;
                    $scope.continents.splice(0, 0, "All");
                }, function (response) {
                    alert("loadContinents(): " + response.toString());
                });
    }

    // load Regions
    $scope.loadRegions = function () {
        $http.get("http://localhost/countryView/countryViewMySql.php?choice=" + 30)
                .then(function (response) {
                    $scope.regions = response.data.regions;
                    $scope.regions.splice(0, 0, "All");
                }, function (response) {
                    alert("loadRegions(): " + response.toString());
                });
    }

    // load Governments
    $scope.loadGovernments = function () {
        $http.get("http://localhost/countryView/countryViewMySql.php?choice=" + 40)
                .then(function (response) {
                    $scope.governments = response.data.governments;
                    $scope.governments.splice(0, 0, "All");
                }, function (response) {
                    alert("loadGovernments(): " + response.toString());
                });
    }

    // load Languages
    $scope.loadLanguages = function () {
        $http.get("http://localhost/countryView/countryViewMySql.php?choice=" + 50)
                .then(function (response) {
                    $scope.languages = response.data.languages;
                    $scope.languages.splice(0, 0, "All");
                }, function (response) {
                    alert("loadLanguages(): " + response.toString());
                });
    }

    // Function for loading up the country views  
    $scope.loadCountries = function (i) {
        $scope.selectedView = $scope.viewList[i];
        $scope.n = i;
        $scope.hide = false;
        $scope.cols = []; //flush columns
        $http.get("http://localhost/countryView/countryViewMySql.php?choice=" + (i + 1) + "&continent=" + $scope.selectedContinent + "&region=" +
                $scope.selectedRegion + "&government=" + $scope.selectedGovernment + "&language=" + $scope.selectedLanguage)
                .then(function (response) {
                    $scope.countries[i] = response.data.records;
                    if ($scope.countries[i][0] !== undefined) {
                        $scope.cols[i] = Object.keys($scope.countries[i][0]);
                    } else {
                        $scope.hide = true;
                    }
                }, function (response) {
                    alert("loadCountries(" + i + "): " + response.toString());
                });
    }

    $scope.notSorted = function (obj) {
        if (!obj) {
            return [];
        }

        return Object.keys(obj);
    }

    $scope.getCountry = function (n) {
        var country = [];
        var row = $scope.countries[n]
        var dat;
        var i = 0;
        for (dat in row) {
            country[i++] = row[dat];
        }
        return country;
    }

    $scope.strcmp = function (str1, str2) {
        var val = 0;

        if (str1 !== str2) {
            var i = 0;
            while (str1.charAt(i) === str2.charAt(i)) {
                i++;
            }

            if (str1.charAt(i) > str2.charAt(i)) {
                val = 1
            } else {
                val = -1;
            }
        }

        return val
    }

    $scope.sortTable = function (col, asc) {
        if ($scope.table) {
            var tbody = $scope.table.tBodies[0];
            var placeholder = document.createElement('tbody');
            table.replaceChild(placeholder, tbody);
            $scope.sortRows(tbody, function compare(topRow, bottomRow) {
                var top = topRow.cells[col].textContent;
                var bot = bottomRow.cells[col].textContent;
                if (isNaN(top) && isNaN(bot)) {
                    return $scope.strcmp(top, bot);
                } else {
                    return (parseInt(top, 10) - parseInt(bot, 10));
                }
            }, asc);
            $scope.table.replaceChild(tbody, placeholder);
        }
    }

    $scope.sortRows = function (tbody, compare, sortDesc) {
        //convert html collection to array
        var rows = [].slice.call(tbody.rows);
        //var rows = Array.prototype.slice.call(tbody.rows);

        //sort to desired order
        rows.sort(compare);
        if (sortDesc) {
            rows.reverse();
        }

        //update table
        var fragment = document.createDocumentFragment();
        rows.forEach(function (row) {
            fragment.appendChild(row);
        });
        tbody.appendChild(fragment);
    }

    // Call the load functions
    $scope.loadContinents();
    $scope.loadRegions();
    $scope.loadGovernments();
    $scope.loadLanguages();
});
