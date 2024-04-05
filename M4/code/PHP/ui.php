<!-- Project file for UBC CPSC304
  Created by Omar Dawoud, Seif ElKemary, Zaid AlAttar
-->

<?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_zalattar";			// change "cwl" to your own CWL
$config["dbpassword"] = "a18135475";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>Health & Nutrition Tracker</title>
	<style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    h1, h2 {
      color: #333;
    }

    form {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    input[type="text"],
    select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"] {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }

    th, td {
      text-align: left;
      padding: 8px;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    th {
      background-color: #4CAF50;
      color: white;
    }
  </style>
</head>

<body>

	<h1>Health & Nutrition Tracker</h1>

	<h2>Insert Nutrition Data</h2>
    <form method="POST" action="ui.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            DeviceID: <input type="text" name="insertDeviceID"> <br /><br />
			User ID: <input type="text" name="insertUserID"> <br /><br />
            Calories: <input type="text" name="insertCalories"> <br /><br />
			Date (DD-MMM-YYYY): <input type = "text" name="insertDate"> <br /><br />
            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>
	<hr />

	<h2> Aggregate Calories Data</h2>
<form method="POST" action="ui.php">
    <select name="aggregationType">
        <option value="MAX">Max</option>
        <option value="MIN">Min</option>
        <option value="AVG">Average</option>
        <option value="COUNT">Count</option>
    </select>
    <input type="hidden" name="aggregateCaloriesRequest">
    <input type="submit" value="Aggregate" name="submitAggregate">
</form>

<hr />

<h2>Delete Device</h2>

	<form method="POST" action="ui.php">
    	<input type="hidden" id="deleteDeviceRequest" name="deleteDeviceRequest">
    	Device ID: <input type="text" name="deleteDeviceID"> <br /><br />

		<input type="submit" value="Delete" name="deleteSubmit">
	</form>

	<hr />

<h2>Users with Multiple Devices</h2>
<form method="POST" action="ui.php">
    <input type="hidden" name="multiDeviceUsersRequest">
    <input type="submit" value="Find Users" name="submitMultiDeviceUsers">
</form>

<hr />

<h2>Users with average sleep duration greater than average of all users</h2>
<form method="POST" action="ui.php">
    <input type="hidden" name="nestedAggRequest">
    <input type="submit" value="Execute Nested Aggregation" name="submitNestedAgg">
</form>

<hr />


<h2>Update User info</h2>
	<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
	<form method="POST" action="ui.php">
		<input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
		UserID: <input type="text" name="updateUserID"> <br /><br />
		New Email: <input type="text" name="updateEmail"> <br /><br />
		New Weight: <input type="text" name="updateWeight"> <br /><br />

		<input type="submit" value="Update" name="updateSubmit"></p>
	</form>

	<hr />

	<h2>Display Tuples</h2>
<form method="POST" action="ui.php">
    <input type="hidden" id="displayQueryRequest" name="displayQueryRequest">
    Table Name: <input type="text" name="tableName"> <br /><br />
    Attributes(Leave blank to display full table | Seperate By Commas): <input type="text" name="attributes"> <br /><br />
    <input type="submit" value="Display" name="displaySubmit"></p>
</form>

<hr />

<h2>Select Insights With the following conditions</h2>
<form method="POST" action="ui.php">
    <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
	UserID: <input type="text" name="userSelect"> <br /><br />
	Calories: <input type="text" name="caloriesSelect"> <br /><br />
	<select name="caloriesOperator">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
        </select>
	Date: <input type="text" name="dateSelect"> <br /><br />
	<input type="submit" value="Select" name="selectSubmit"></p>
</form>

<hr />

<h2>Join: Find the emails and ages of users who sleep at a specific time</h2>
<form method="POST" action="ui.php">
    <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
	Bedtime: <input type="text" name="bedtimeJoin"> <br /><br />
	<input type="submit" value="Join" name="joinSubmit"></p>
</form>

<hr />

<h2>Division: Find all Users who accomplished all their goals</h2>
<form method="GET" action="ui.php">
    <input type="hidden" id="divisionQueryRequest" name="divisionQueryRequest">
    <!-- UserID: <input type="text" name="userDivision"> <br /><br /> -->
    <input type="submit" value="Division" name="divisionSubmit"></p>
</form>

<hr />

<h3>Created By: Omar Dawoud, Seif ElKemary, Zaid Al Attar</h3>
<hr />

	<?php

	// SQL

	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{ //takes a plain (no bound variables) SQL command and executes it
		// echo "<br>running ".$cmdstr."<br>";
		// echo "Running SQL: $cmdstr<br>";
		global $db_conn, $success;

		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			echo htmlentities($e['message']);
			$success = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "Error: Table does not exist";
			$success = False;
		}

		return $statement;
	}

	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			// echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				//echo $val;
				//echo "<br>".$bind."<br>";
				oci_bind_by_name($statement, $bind, $val);
				unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
			}

			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				echo htmlentities($e['message']);
				echo "<br>";
				$success = False;
			}
		}
	}

	function connectToDB()
	{
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	// HANDLERS

	// ERROR HANDLED
	function handleDeleteDeviceRequest()
	{
    global $db_conn;
    echo "<br>Processing Delete<br/>";
    
    // Retrieve DeviceID from the form submission
    $deviceID = $_POST['deleteDeviceID'];

    // Check if the Device ID exists
    $checkQuery = "SELECT COUNT(*) AS deviceCount FROM Device WHERE DeviceID = $deviceID";
    $checkTuple = array(":DeviceID" => $deviceID);
    $checkResult = executePlainSQL($checkQuery, array($checkTuple));

    // Check if the check query execution was successful
    if (!$checkResult) {
        echo "<br>Error: This Device Does not exist.<br/>";
        return;
    }

    // Fetch the result row
    $checkRow = oci_fetch_assoc($checkResult);
    $deviceCount = $checkRow['DEVICECOUNT'];

    if ($deviceCount == 0) {
        echo "<br> Device with ID " . $deviceID . " does not exist. <br>";
        return;
    }

    // Prepare the SQL statement for deletion
    $deleteQuery = "DELETE FROM Device WHERE DeviceID = :DeviceID";
    $deleteTuple = array(":DeviceID" => $deviceID);

    // Execute the deletion query
    $deleteResult = executeBoundSQL($deleteQuery, array($deleteTuple));

    if ($deleteResult) {
        // Notify the user of successful deletion
        echo "<br> Device with ID " . $deviceID . " has been deleted successfully. <br>";
    }

	echo "Device Deleted Successfully";

    oci_commit($db_conn);
	}

	// NO ERROR HANDLING
	function handleUpdateRequest()
	{
		global $db_conn;

		echo "Processing Update";

            $tuple = array (
                ":UserID" => $_POST['updateUserID'],
                ":Email" => $_POST['updateEmail'],
                ":UserWeight" => $_POST['updateWeight'],
            );

            $alltuples = array (
                $tuple
            );

			

			// echo "<br>RESULT BEFORE UPDATE:</br>";
			// printUpdateRequestResult();

			executeBoundSQL("
				UPDATE User_table U
				SET U.Email = :Email, U.UserWeight = :UserWeight
				WHERE U.UserID = :UserID
				
			", $alltuples);

			// echo "<br>RESULT AFTER UPDATE:</br>";
			// printUpdateRequestResult();
            
            oci_commit($db_conn);
	}

	// NO ERROR HANDLING
	function handleInsertRequest()
	{
		global $db_conn;

		//Getting the values from user and insert data into the table
		$tuple = array (
			":DeviceID" => $_POST['insertDeviceID'],
            ":UserID" => $_POST['insertUserID'],
            ":Calories" => $_POST['insertCalories'],
			":inputDate" => $_POST['insertDate'],
        );

		$alltuples = array (
			$tuple
		);

		if(!$tuple[":DeviceID"] || !$tuple[":UserID"] || !$tuple[":Calories"] || !$tuple[":inputDate"]){
			echo "Error: Enter all required fields";
			return;
		}

		if(!is_numeric($tuple[":Calories"])){
			echo "Error: Calories must be a number.";
			return;
		}

		if (!doesForeignKeyExist('User_table', 'UserID', $tuple[":UserID"])) {
			echo "Error: No such User ID found.";
			return;
		}

		if (!doesForeignKeyExist('Device', 'DeviceID', $tuple[":DeviceID"])) {
			echo "Error: No such Device ID found.";
			return;
		}

		$nutritionTable = executePlainSQL("SELECT * FROM NUTRITIONINPUTS ORDER BY NutritionID");
		

		echo "<br>User_table BEFORE INSERT:</br>";
        printResult($nutritionTable);

		executeBoundSQL("
            INSERT INTO NutritionInputs (
                NutritionID,
                DeviceID,
                UserID,
				Calories,
				NutritionInputsDate
            )
            VALUES (
				NutritionID_seq.NEXTVAL,
                :DeviceID,
                :UserID,
                :Calories,
				:inputDate)
            ",
        $alltuples);

		echo "<br>User_table AFTER INSERT:</br>";
		$nutritionTable1 = executePlainSQL("SELECT * FROM NUTRITIONINPUTS ORDER BY NutritionID");
		printResult($nutritionTable1);


		oci_commit($db_conn);
	}

	function handleNested() {
		global $db_conn;
	
	
		// The SQL query
		$sql = "SELECT SleepID, AVG(Duration) AS AvgDuration
				FROM Sleep
				GROUP BY SleepID
				HAVING AVG(Duration) > (SELECT AVG(Duration) FROM Sleep)";
	
		$result = executePlainSQL($sql);
	
		// Call a function to print results
		printResult($result);
	}

	// ERROR HANDLED
	function handleAggregateCaloriesRequest()
	{
		global $db_conn;

		$aggregationType = $_POST['aggregationType'];
		$query = "SELECT UserID, {$aggregationType}(Calories) AS Aggregated_Calories FROM NutritionInputs GROUP BY UserID ORDER BY Aggregated_Calories DESC";

		$result = executePlainSQL($query);
		printResult($result);

		echo "Aggregate completed Successfully<br>";
	}

	// ERROR HANDLED
	function handleMultiDeviceUsersRequest() 
	{
		global $db_conn;
		
		echo "User with multiple devices Processing<br>";
	
		$query = "SELECT UserID, COUNT(DISTINCT DeviceID) AS DeviceCount
				  FROM NutritionInputs
				  GROUP BY UserID
				  HAVING COUNT(DISTINCT DeviceID) > 1";
	
		$result = executePlainSQL($query);

		printResult($result);
		echo "User with multiple devices completed successfully<br>";
	}
	
	// ERROR HANDLED EXCEPT ATTRIBUTES
	function handleDisplayRequest()
	{
    	global $db_conn;
    	echo "Projection Processing<br>";

   		if (isset($_POST['tableName'])) {
        $tableName = $_POST['tableName'];
        $attributes = $_POST['attributes'];

        
        if (!doesTableExist($tableName)) {
            echo "Error: Table '{$tableName}' does not exist in the database. <br>";
            return;
        }

        $sql = "";

        if (!empty($attributes)) {
            $sql = "SELECT {$attributes} FROM {$tableName}";
        } else {
            $sql = "SELECT * FROM {$tableName}";
        }

        $result = executePlainSQL($sql);

        // Check if result is not null before printing
        if ($result) {
            
            printResult($result);
        } else {
            // Handle case when result is null
            echo "No data found for table: " . $tableName;
        }
    	} else {
        // Handle case when tableName is not set in POST
        echo "Table Name not provided. <br>";
    	}

		echo "Projection Completed Successfully <br>";
	}

	// ERROR HANDLED
	// function getTableColumns($tableName)
	// {
   	// 	global $db_conn;
    // 	$columns = array();

	// 	$upperName = strtoupper($tableName);

    // 	// Query to get column names for the given table
    // 	$sql = "SELECT column_name FROM all_tab_columns WHERE table_name = :tableName";
    // 	$statement = oci_parse($db_conn, $sql);
    // 	oci_bind_by_name($statement, ":tableName", $upperName);
    // 	oci_execute($statement);

    // 	// Fetch column names and store them in an array
    // 	while ($row = oci_fetch_assoc($statement)) {
    //    		$columns[] = $row['COLUMN_NAME'];
    // 	}

    // 	return $columns;
	// }
	
	// ERROR HANDLED
	function handleSelectRequest() 
	{
		global $db_conn;
	
		// Check if form is submitted
		if (isset($_POST['selectSubmit'])) {
			$userID = $_POST['userSelect'];
	
			// Validate User ID
			if (empty($userID)) {
				echo "Please provide a User ID.";
				return;
			}
	
			$calories = $_POST['caloriesSelect'];
			$date = $_POST['dateSelect'];
			$operator = $_POST['caloriesOperator'];
	
			// Define attribute names and corresponding table names
			$tuple = array(
				":Calories" => $_POST['caloriesSelect'],
				":Date" => $_POST['dateSelect'],
			);
	
			$alltuples = array(
				$tuple
			);
	
			// Formulate SQL query
			if ($operator == 'AND') {
				$sql = "SELECT IM.UserID, IM.Result, IM.InsightMonitorsDate, NI.Calories 
						FROM InsightMonitors IM, NutritionInputs NI
						WHERE IM.UserID = $userID AND NI.UserID = IM.UserID AND NI.Calories = $calories AND IM.InsightMonitorsDate = TO_DATE('$date', 'DD-MON-YYYY')
						";
			} else {
				$sql = "SELECT IM.UserID, IM.Result, IM.InsightMonitorsDate, NI.Calories 
						FROM InsightMonitors IM, NutritionInputs NI
						WHERE IM.UserID = $userID AND NI.UserID = IM.UserID AND (NI.Calories = $calories OR IM.InsightMonitorsDate = TO_DATE('$date', 'DD-MON-YYYY'))
						";
			}
	
			// Execute SQL query
			$result = executePlainSQL($sql);
	
			// Check if result is not null before printing
			if ($result) {
				// Call printResult function
				printResult($result);
			} else {
				// Handle case when result is null
				echo "No insights found based on the specified conditions.";
			}
		} else {
			// Handle case when form is not submitted
			echo "Filtering conditions not provided.";
		}
	}

	// ERROR HANDLED: IF NO USERS ADD ERROR
	function handleJoinRequest() 
	{
    	global $db_conn;


    	$tuple = array(
    	    ":Bedtime" => $_POST['bedtimeJoin']
    	);

		$bedtime = $_POST['bedtimeJoin'];

   		$alltuples = array(
    	$tuple
    	);

    	$result = "
		SELECT U.Email, U.Age, S.Bedtime
		FROM Sleep S, GenerateData GD, User_table U, NutritionInputs NI
		WHERE U.UserID = NI.UserID AND  NI.DeviceID = GD.DeviceID 
		AND GD.SleepID = S.SleepID AND S.Bedtime = $bedtime";

    	$resultResource = executePlainSQL($result);

		if ($resultResource) {
        	printResult($resultResource);
			
    	} else {
        	echo "Error: No results found.";
    	}

    	oci_commit($db_conn);

		echo "Completed Join Successfully<br>";
		}
	
	// ERROR HANDLED
	function handleDivisionRequest()
	{
		global $db_conn; // Assume this is your database connection variable


    	// The SQL query
    	$sql = "
        SELECT U.UserID
        FROM User_table U
        WHERE NOT EXISTS (
            (SELECT G.goalDescription FROM Goals G)
            MINUS
            (SELECT G.goalDescription 
             FROM NutritionInputs NI
             JOIN GenerateData GD ON NI.DeviceID = GD.DeviceID
             JOIN Goals G ON GD.GoalsID = G.GoalsID
             WHERE NI.UserID = U.UserID))
    	";

    	$result = executePlainSQL($sql);
		printResult($result);
		echo "Division Completed Successfully";
	} 

	function handlePOSTRequest()
	{
		// echo "handlePOSTRequest called<br>";
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('updateQueryRequest', $_POST)) {
				handleUpdateRequest();
			} else if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			} else if (array_key_exists('aggregateCaloriesRequest', $_POST)){
				handleAggregateCaloriesRequest();
			} else if (array_key_exists('multiDeviceUsersRequest', $_POST)){
				handleMultiDeviceUsersRequest();
			} else if (array_key_exists('displayQueryRequest', $_POST)) {
				handleDisplayRequest();
			} else if (array_key_exists('selectQueryRequest', $_POST)) {
				handleSelectRequest();
			} elseif (array_key_exists('deleteDeviceRequest', $_POST)) {
				handleDeleteDeviceRequest();
			} elseif (array_key_exists('joinQueryRequest', $_POST)) {
				handleJoinRequest();
			} elseif (array_key_exists('nestedAggRequest', $_POST)){
				handleNested();
			}
			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('countTuples', $_GET)) {
				handleCountRequest();
			 } elseif (array_key_exists('divisionQueryRequest', $_GET)) {
				handleDivisionRequest();
		}

			disconnectFromDB();
		}
	}

	// HELPERS

	// FOR PROJECTION HANDLER
	function doesTableExist($tableName) {
		global $db_conn;
		
		$upperTableName = strtoupper($tableName);
		$sql = "SELECT COUNT(*) FROM user_tables WHERE table_name = :tableName";
		$statement = oci_parse($db_conn, $sql);
		oci_bind_by_name($statement, ":tableName", $upperTableName);
	
		oci_execute($statement);
	
		
		$row = oci_fetch_array($statement);
		if ($row) {
			return $row[0] > 0;
		}
		return false; 
	}

	function doesForeignKeyExist($tableName, $columnName, $value) {
		global $db_conn;

		$sql = "SELECT COUNT(*) AS COUNT_RESULT FROM {$tableName} WHERE {$columnName} = {$value}";

		$statement = executePlainSQL($sql);
	
		$row = oci_fetch_array($statement);
		if ($row) {
			return $row[0] > 0;
		}
		return false;
	}

	function printResult($result) 
	{
		echo "<h2>Displaying Results</h2>";
		echo "<table border='1'>";
	
		// Fetch column names
		echo "<tr>";
		$numCols = oci_num_fields($result);
		for ($i = 1; $i <= $numCols; $i++) {
			$colName = oci_field_name($result, $i);
			echo "<th>{$colName}</th>";
		}
		echo "</tr>";
	
		// Fetch and print rows
		while ($row = oci_fetch_array($result, OCI_ASSOC)) {
			echo "<tr>";
			foreach ($row as $col) {
				echo "<td>{$col}</td>";
			}
			echo "</tr>";
		}
	
		echo "</table> <br>";
	}

	// Handler Fetcher
	if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['submitAggregate'])
	|| isset($_POST['submitMultiDeviceUsers']) || isset($_POST['displaySubmit']) || isset($_POST['selectSubmit']) || isset($_POST['deleteSubmit'])
	|| isset($_POST['joinSubmit']) || isset($_POST['submitNestedAgg'])) {
		handlePOSTRequest();
	} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest']) || isset($_GET['divisionSubmit'])) {
		handleGETRequest();
	} else if (isset($_GET['displayUsersRequest'])) {
		handleDisplayUsersRequest();
	}

	?>
</body>

</html>