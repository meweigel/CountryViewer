<?php

/**
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
include("connectMySql.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$choice     = filter_input(INPUT_GET, "choice", FILTER_VALIDATE_INT);
$continent  = filter_input(INPUT_GET, 'continent', FILTER_SANITIZE_SPECIAL_CHARS);
$region     = filter_input(INPUT_GET, 'region', FILTER_SANITIZE_SPECIAL_CHARS);
$government = filter_input(INPUT_GET, 'government', FILTER_SANITIZE_SPECIAL_CHARS);
$language   = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_SPECIAL_CHARS);


if ($choice == NULL) {
   error_log("choice is NULL", 0);
}


query($choice, $continent, $region, $government, $language);


function query($choice, $continent, $region, $government, $language) {
   
   $json = " ";
   
   switch ($choice) {
      case 1:
         $json = view1($continent, $region, $government);
         break;
      case 2:
         $json = view2($continent, $region, $language);
         break;
      case 3:
         $json = view3($continent, $region, $government);
         break;
      case 4:
         $json = view4($continent);
         break;
      case 20:
         $json = getContinents();
         break;
      case 30:
         $json = getRegions();
         break;
      case 40:
         $json = getGovernments();
         break;
      case 50:
         $json = getLanguages();
         break;
   }
   
   echo $json;
}

/*
 * This method will edit a numerically tokenized where
 * clause and will removed tokens where 'All' was 
 * selected. Any remaining numerical tokens are then 
 * replaced with ? tokens. If there are no remaining
 * tokens (every parameter was 'All') the where clause
 * will become a zero length string. 
 */

function editWhereClause($params, $string) {
   $cnt = 1;
   foreach ($params as $param) {
      if (strcmp($param, "All") == 0) {
         $str    = "([.a-zA-Z]+ = " . $cnt . "['and' ]*)";
         $string = preg_replace($str, "", $string);
      }
      $cnt++;
   }
   
   $string = preg_replace("(['and' ]*$)", "", $string);
   $string = preg_replace("([0-9])", "?", $string);
   
   if (strcmp($string, "where") == 0) {
      $string = "";
   }
   
   return $string;
}


function getContinents() {
   
   $json = " ";
   
   try {

      $sql = "select distinct Continent FROM Country order by Continent";
      
      $result = runSearch($sql,null);
      
      $json = createJsonResponse($result, "Continent", "continents");
   }
   catch (PDOException $e) {
      error_log("getContinents() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function getRegions() {
   
   $json = " ";
   
   try {

      $sql = "select distinct Region FROM Country order by Region";
      
      $result = runSearch($sql,null);
      
      $json = createJsonResponse($result, "Region", "regions");
   }
   catch (PDOException $e) {
      error_log("getRegions() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function getGovernments() {
   
   $json = " ";
   
   try {

      $sql = "select distinct GovernmentForm FROM Country order by GovernmentForm";
      
      $result = runSearch($sql,null);
      
      $json = createJsonResponse($result, "GovernmentForm", "governments");
   }
   catch (PDOException $e) {
      error_log("getGovernments() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function getLanguages() {
   
   $json = " ";
   
   try {
 
      $sql = "select distinct Language FROM CountryLanguage order by Language";
      
      $result = runSearch($sql,null);
      
      $json = createJsonResponse($result, "Language", "languages");
   }
   catch (PDOException $e) {
      error_log("getLanguages() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function view1($continent, $region, $government) {
   
   $json = " ";
   
   try {
      $params = array(
         $continent,
         $region,
         $government
      );
	  
      $where  = editWhereClause($params, "where Continent = 1 and Region = 2 and GovernmentForm = 3");
      
      $sql = "select Code, Name AS Country, Continent, Region, GovernmentForm, Population FROM Country " . $where;
      
      $result = runSearch($sql,$params);
      
      $fieldNames = array(
         "Code",
         "Country",
         "Continent",
         "Region",
         "GovernmentForm",
         "Population"
      );
      
      $json = createJsonResponse($result, $fieldNames, "records");
   }
   catch (PDOException $e) {
      error_log("view1() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function view2($continent, $region, $language) {
   
   $json = " ";
   
   try {
      $params = array(
         $continent,
         $region,
         $language
      );
	  
      $where  = editWhereClause($params, "where co.Continent = 1 and co.Region = 2 and col.Language = 3");
      
      $sql = "select co.Name AS Country, co.Continent, co.Region, ci.Name AS Capital, col.Language AS 'Official Language', " .  
			 "cpl.Language AS 'Primary Language', co.Population/1000000 AS 'Pop. Millions', co.LifeExpectancy " .
             "from Country AS co " .
             "inner join City AS ci on ci.ID = co.Capital " .
             "inner join CountryLanguage AS col on col.CountryCode = co.Code and col.IsOfficial='T' " .
             "inner join " . 
             "( " .
             "select distinct * from CountryLanguage " .
             "order by CountryLanguage.Percentage desc " .
             ") AS cpl on cpl.CountryCode = co.Code " . $where . " group by co.Name";
      
      $result = runSearch($sql,$params);
      
      $fieldNames = array(
         "Country",
         "Continent",
         "Region",
         "Capital",
         "OfficialLanguage:Official Language",
         "PrimaryLanguage:Primary Language",
         "PopMillions:Pop. Millions",
         "LifeExpectancy"
      );
      
      $json = createJsonResponse($result, $fieldNames, "records");
   }
   catch (PDOException $e) {
      error_log("view2() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function view3($continent, $region, $government) {
   
   $json = " ";
   
   try {
      $params = array(
         $continent,
         $region,
         $government
      );
	  
      $where  = editWhereClause($params, "where co.Continent = 1 and co.Region = 2 and co.GovernmentForm = 3");

      $sql = "select co.Name AS Country, co.Continent, co.Region, " .
        "co.GovernmentForm, ci.Name AS Capital, co.SurfaceArea AS km2, co.GNP, " . 
        "FORMAT(Max(((co.GNP - co.GNPOld) / co.GNP) * 100), 2) AS 'GNP Increase' " .
        "from Country AS co " .
        "inner join City ci on ci.ID=co.Capital " . $where . " group by co.Name";
      
      $result = runSearch($sql,$params);
      
      $fieldNames = array(
         "Country",
         "Continent",
         "Region",
         "GovernmentForm",
         "Capital",
         "km2",
         "GNP",
         "GNPIncrease:GNP Increase"
      );
      
      $json = createJsonResponse($result, $fieldNames, "records");
   }
   catch (PDOException $e) {
      error_log("view3() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}

function view4($continent) {
   
   $json = " ";
   
   try {
      $params = array(
         $continent
      );
	  
      $where  = editWhereClause($params, "where a.Continent = 1");
      
      $sql = "select a.Continent, b.GNP_Increase, a.Name AS Country from Country as a " .
        "inner join ( " .
        "select Continent, max(((GNP - GNPOld) / GNP) * 100) AS GNP_Increase " .
        "from Country " .
        "GROUP BY Continent " .
        ") b on (((a.GNP - a.GNPOld) / a.GNP) * 100) = b.GNP_Increase and a.Continent = b.Continent " . $where;
      
      
      $result = runSearch($sql,$params);
      
      $fieldNames = array(
         "Continent",
         "GNP_Increase",
         "Country"
      );
      
      $json = createJsonResponse($result, $fieldNames, "records");
   }
   catch (PDOException $e) {
      error_log("view4() Error: " . $e->getMessage(), 0);
   }
   
   return $json;
}


// Create json response
function createJsonResponse($result, $parameters, $rootName) {
   
   $json    = "";
   $isArray = is_array($parameters);
   
   foreach ($result as $row) {
      if ($json != "") {
         $json .= ",";
      }
      
      if ($isArray) {
         
         $json .= '{';
         
         foreach ($parameters as $parameter) {
            $tokens = explode(":", $parameter);
            if (count($tokens) > 1) {
               $json .= '"' . $tokens[0] . '":"' . $row[$tokens[1]] . '",';
            } else {
               $json .= '"' . $parameter . '":"' . $row[$parameter] . '",';
            }
         }
         
         $json = chop($json, ",");
         
         $json .= '}';
      } else {
         $json .= '"' . $row[$parameters] . '"';
      }
   }
   
   $json = '{"' . $rootName . '":[' . $json . ']}';
   
   return $json;
}


/*
Conduct a keymatch search of a database table
*/
function runSearch($sql, $params) {
   global $conn;
   $result = "";
   
   try {
	   
	  $stmt = $conn->prepare($sql);
	  
	  if($params != null && is_array($params)){
		  $total = count($params);
		  $cnt = 1;
		  for ($x = 0; $x < $total; $x++) { 
			 if (strcmp($params[$x], "All") != 0) {
				 $stmt->bindParam($cnt, $params[$x]);
				 $cnt++;
			 }
		  }
	  }
	  
      $stmt->execute();
      
	  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
   catch (PDOException $e) {
      error_log("view1() Error: " . $e->getMessage(), 0);
   }
   
   
   return $result;
}
?>
