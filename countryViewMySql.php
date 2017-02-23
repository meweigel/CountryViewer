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

$choice = filter_input(INPUT_GET, "choice", FILTER_VALIDATE_INT);
$continent = filter_input(INPUT_GET, 'continent', FILTER_SANITIZE_SPECIAL_CHARS);
$region = filter_input(INPUT_GET, 'region', FILTER_SANITIZE_SPECIAL_CHARS);
$government = filter_input(INPUT_GET, 'government', FILTER_SANITIZE_SPECIAL_CHARS);
$language = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_SPECIAL_CHARS);


if ($choice == NULL) {
    error_log("choice is NULL", 0);
}

query($choice, $continent, $region, $government, $language);

function query($choice, $continent, $region, $government, $language) {
    switch ($choice) {
        case 1:
            view1($continent, $region, $government);
            break;
        case 2:
            view2($continent, $region, $language);
            break;
        case 3:
            view3($continent, $region, $government);
            break;
        case 4:
            view4($continent);
            break;
        case 20:
            getContinents();
            break;
        case 30:
            getRegions();
            break;
        case 40:
            getGovernments();
            break;
        case 50:
            getLanguages();
            break;
    }
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
            $str = "([.a-zA-Z]+ = " . $cnt . "[ and ]*)";
            $string = preg_replace($str, "", $string);
        }
        $cnt++;
    }

    $string = preg_replace("([ and ]*$)", "", $string);
    $string = preg_replace("([0-9])", "?", $string);

    if (strcmp($string, "where") == 0) {
        $string = "";
    }

    return $string;
}

function getContinents() {
    try {
        global $conn;
        $stmt = $conn->prepare("select distinct Continent FROM Country order by Continent");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '"' . $row["Continent"] . '"';
        }
        $outp = '{"continents":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("getContinents() Error: " . $e->getMessage(), 0);
    }
}

function getRegions() {
    try {
        global $conn;
        $stmt = $conn->prepare("select distinct Region FROM Country order by Region");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '"' . $row["Region"] . '"';
        }
        $outp = '{"regions":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("getRegions() Error: " . $e->getMessage(), 0);
    }
}

function getGovernments() {
    try {
        global $conn;
        $stmt = $conn->prepare("select distinct GovernmentForm FROM Country order by GovernmentForm");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '"' . $row["GovernmentForm"] . '"';
        }
        $outp = '{"governments":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("getGovernments() Error: " . $e->getMessage(), 0);
    }
}

function getLanguages() {
    try {
        global $conn;
        $stmt = $conn->prepare("select distinct Language FROM CountryLanguage order by Language");
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '"' . $row["Language"] . '"';
        }
        $outp = '{"languages":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("getLanguages() Error: " . $e->getMessage(), 0);
    }
}

function view1($continent, $region, $government) {
    try {
        $params = array($continent, $region, $government);
        $where = editWhereClause($params, "where Continent = 1 and Region = 2 and GovernmentForm = 3");

        global $conn;
        $stmt = $conn->prepare("select Code, Name AS Country, Continent, Region, GovernmentForm, Population FROM Country " . $where);

        $cnt = 1;
        if (strcmp($continent, "All") != 0) {
            $stmt->bindParam($cnt, $continent);
            $cnt++;
        }
        if (strcmp($region, "All") != 0) {
            $stmt->bindParam($cnt, $region);
            $cnt++;
        }
        if (strcmp($government, "All") != 0) {
            $stmt->bindParam($cnt, $government);
        }
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '{"Code":"' . $row["Code"] . '",';
            $outp .= '"Country":"' . $row["Country"] . '",';
            $outp .= '"Continent":"' . $row["Continent"] . '",';
            $outp .= '"Region":"' . $row["Region"] . '",';
            $outp .= '"GovernmentForm":"' . $row["GovernmentForm"] . '",';
            $outp .= '"Population":"' . $row["Population"] . '"}';
        }
        $outp = '{"records":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("view1() Error: " . $e->getMessage(), 0);
    }
}

function view2($continent, $region, $language) {
    try {
        $params = array($continent, $region, $language);
        $where = editWhereClause($params, "where co.Continent = 1 and co.Region = 2 and col.Language = 3");

        global $conn;
        $stmt = $conn->prepare("select co.Name AS Country, co.Continent, co.Region, ci.Name AS Capital, col.Language AS 'Official Language', 
                                cpl.Language AS 'Primary Language', co.Population/1000000 AS 'Pop. Millions', co.LifeExpectancy
                                from Country AS co
                                inner join City AS ci on ci.ID = co.Capital
                                inner join CountryLanguage AS col on col.CountryCode = co.Code and col.IsOfficial='T'
                                inner join 
                                (
                                   select distinct * from CountryLanguage 
                                   order by CountryLanguage.Percentage desc
                                ) AS cpl on cpl.CountryCode = co.Code " . $where . " group by co.Name");

        $cnt = 1;
        if (strcmp($continent, "All") != 0) {
            $stmt->bindParam($cnt, $continent);
            $cnt++;
        }
        if (strcmp($region, "All") != 0) {
            $stmt->bindParam($cnt, $region);
            $cnt++;
        }
        if (strcmp($language, "All") != 0) {
            $stmt->bindParam($cnt, $language);
        }
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '{"Country":"' . $row["Country"] . '",';
            $outp .= '"Continent":"' . $row["Continent"] . '",';
            $outp .= '"Region":"' . $row["Region"] . '",';
            $outp .= '"Capital":"' . $row["Capital"] . '",';
            $outp .= '"OfficialLanguage":"' . $row["Official Language"] . '",';
            $outp .= '"PrimaryLanguage":"' . $row["Primary Language"] . '",';
            $outp .= '"PopMillions":"' . $row["Pop. Millions"] . '",';
            $outp .= '"LifeExpectancy":"' . $row["LifeExpectancy"] . '"}';
        }
        $outp = '{"records":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("view2() Error: " . $e->getMessage(), 0);
    }
}

function view3($continent, $region, $government) {
    try {
        $params = array($continent, $region, $government);
        $where = editWhereClause($params, "where co.Continent = 1 and co.Region = 2 and co.GovernmentForm = 3");

        global $conn;
        $stmt = $conn->prepare("select co.Name AS Country, co.Continent, co.Region, 
        co.GovernmentForm, ci.Name AS Capital, co.SurfaceArea AS km2, co.GNP, 
        FORMAT(Max(((co.GNP - co.GNPOld) / co.GNP) * 100), 2) AS 'GNP Increase'
        from Country AS co
        inner join City ci on ci.ID=co.Capital " . $where . " group by co.Name");

        $cnt = 1;
        if (strcmp($continent, "All") != 0) {
            $stmt->bindParam($cnt, $continent);
            $cnt++;
        }
        if (strcmp($region, "All") != 0) {
            $stmt->bindParam($cnt, $region);
            $cnt++;
        }
        if (strcmp($government, "All") != 0) {
            $stmt->bindParam($cnt, $government);
        }
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '{"Country":"' . $row["Country"] . '",';
            $outp .= '"Continent":"' . $row["Continent"] . '",';
            $outp .= '"Region":"' . $row["Region"] . '",';
            $outp .= '"GovernmentForm":"' . $row["GovernmentForm"] . '",';
            $outp .= '"Capital":"' . $row["Capital"] . '",';
            $outp .= '"km2":"' . $row["km2"] . '",';
            $outp .= '"GNP":"' . $row["GNP"] . '",';
            $outp .= '"GNPIncrease":"' . $row["GNP Increase"] . '"}';
        }
        $outp = '{"records":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("view3() Error: " . $e->getMessage(), 0);
    }
}

function view4($continent) {
    try {
        $params = array($continent);
        $where = editWhereClause($params, "where a.Continent = 1");

        global $conn;
        $stmt = $conn->prepare("select a.Continent, b.GNP_Increase, a.Name AS Country
	from Country as a
        inner join (
        select Continent, max(((GNP - GNPOld) / GNP) * 100) AS GNP_Increase
        from Country
        GROUP BY Continent
        ) b on (((a.GNP - a.GNPOld) / a.GNP) * 100) = b.GNP_Increase and a.Continent = b.Continent " . $where);

        if (strcmp($continent, "All") != 0) {
            $stmt->bindParam(1, $continent);
        }
        $stmt->execute();

        // set the resulting array to associative
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $outp = "";
        foreach ($result as $row) {
            if ($outp != "") {
                $outp .= ",";
            }
            $outp .= '{"Continent":"' . $row["Continent"] . '",';
            $outp .= '"GNP_Increase":"' . $row["GNP_Increase"] . '",';
            $outp .= '"Country":"' . $row["Country"] . '"}';
        }
        $outp = '{"records":[' . $outp . ']}';
        $conn = null;
        echo($outp);
    } catch (PDOException $e) {
        error_log("view4() Error: " . $e->getMessage(), 0);
    }
}
?>
