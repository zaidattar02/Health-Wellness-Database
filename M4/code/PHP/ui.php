<!-- Test Oracle file for UBC CPSC304
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  Modified by Jason Hall (23-09-20)
  This file shows the very basics of how to execute PHP commands on Oracle.
  Specifically, it will drop a table, create a table, insert values update
  values, and then query for values
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up All OCI commands are
  commands to the Oracle libraries. To get the file to work, you must place it
  somewhere where your Apache server can run it, and you must rename it to have
  a ".php" extension. You must also change the username and password on the
  oci_connect below to be your ORACLE username and password
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
$config["dbuser"] = "ora_omardawd";			// change "cwl" to your own CWL
$config["dbpassword"] = "a81766800";	// change to 'a' + your student number
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
</head>

<body>
	<!-- <h2>Reset</h2>
	<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

	<form method="POST" action="ui.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr /> -->

	<h2>Insert Nutrition Data</h2>
    <form method="POST" action="ui.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            DeviceID: <input type="text" name="insertDeviceID"> <br /><br />
			User ID: <input type="text" name="insertUserID"> <br /><br />
            Calories: <input type="text" name="insertCalories"> <br /><br />
			Date: <input type = "text" name="insertDate"> <br /><br />
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

	<h2>Count the Tuples in User table</h2>
	<form method="GET" action="ui.php">
		<input type="hidden" id="countTupleRequest" name="countTupleRequest">
		<input type="submit" name="countTuples"></p>
	</form>

	<hr />


	<h2>Display Tuples</h2>
<form method="POST" action="ui.php">
    <input type="hidden" id="displayQueryRequest" name="displayQueryRequest">
    Table Name: <input type="text" name="tableName"> <br /><br />
    Attributes(Leave blank to display full table): <input type="text" name="attributes"> <br /><br />
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

<h2>Join: Find the emails and ages of users who sleep at</h2>
<form method="POST" action="ui.php">
    <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
	Bedtime: <input type="text" name="bedtimeJoin"> <br /><br />
	<input type="submit" value="Join" name="joinSubmit"></p>
</form>

<hr />


	<?php
	// The following code will be parsed as PHP

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
		echo "Running SQL: $cmdstr<br>";
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
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
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

	function handleDeleteDeviceRequest() 
	{
		global $db_conn;
	
		// Retrieve DeviceID from the form submission
		$deviceID = $_POST['deleteDeviceID'];

		$tuple = array (
			":DeviceID" => $deviceID
		);

		$alltuples = array (
			$tuple
		);
		
		// Prepare the SQL statement for execution
		// $statement = oci_parse($db_conn, $sql);
		// oci_bind_by_name($statement, ":DeviceID", $deviceID); // Bind the DeviceID to ensure the correct device is targeted
	
		// Execute the deletion query
		executePlainSQL("
			DELETE FROM Device 
			WHERE DeviceID = $deviceID", 
			$alltuples);
		// if ($r) {
			// Notify the user of successful deletion
			echo "<br> Device with ID " . $deviceID . " has been deleted successfully. <br>";
		// } // else {
			// Error handling: retrieve the error message from OCI and display it
			// $e = oci_error($statement);
			// echo "<script type='text/javascript'>alert('Error deleting device: " . htmlentities($e['message']) . "');</script>";
		// }

		oci_commit($db_conn);
	}

	function handleUpdateRequest()
	{
		global $db_conn;

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

	// function handleResetRequest()
	// {
	// 	global $db_conn;
	// 	// Drop old table
	// 	executePlainSQL("DROP TABLE demoTable");

	// 	// Create new table
	// 	echo "<br> creating new table <br>";
	// 	executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
	// 	oci_commit($db_conn);
	// }

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

		if (!doesForeignKeyExist('User_table', 'UserID', $tuple[":UserID"])) {
			echo "Error: No such User ID found.";
			return;
		}

		if (!doesForeignKeyExist('Device', 'DeviceID', $tuple[":DeviceID"])) {
			echo "Error: No such Device ID found.";
			return;
		}

		echo "<br>User_table BEFORE INSERT:</br>";
        printInsertRequestResult();

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
        printInsertRequestResult();


		oci_commit($db_conn);
	}

	function handleAggregateCaloriesRequest() 
	{
		echo "handleAggregateCaloriesRequest called<br>";
		global $db_conn;

		$aggregationType = $_POST['aggregationType'];
		$query = "SELECT UserID, {$aggregationType}(Calories) AS AggregatedCalories FROM NutritionInputs GROUP BY UserID ORDER BY AggregatedCalories DESC";

		$result = executePlainSQL($query);

		echo "<br> Calories Data: <br>";
		echo "<table>";
		echo "<tr><th>User ID</th><th>{$aggregationType} Calories</th></tr>";

		while ($row = oci_fetch_array($result, OCI_BOTH)) {
			echo "UserID: " . $row["USERID"] . " - Calories: " . $row["AGGREGATEDCALORIES"] . "<br>";
			echo "<tr><td>" . $row["USERID"] . "</td><td>" . $row["AGGREGATEDCALORIES"] . "</td></tr>";
		}

		echo "</table>";
	}

	function handleMultiDeviceUsersRequest() 
	{
		global $db_conn;
		
		echo "handleMultiDeviceUsersRequest called<br>";
	
		$query = "SELECT UserID, COUNT(DISTINCT DeviceID) AS DeviceCount
				  FROM NutritionInputs
				  GROUP BY UserID
				  HAVING COUNT(DISTINCT DeviceID) > 1";
	
		$result = executePlainSQL($query);
	
		echo "<br>Users with multiple devices:<br>";
		echo "<table>";
		echo "<tr><th>User ID</th><th>Device Count</th></tr>";
	
		while ($row = oci_fetch_array($result, OCI_BOTH)) {
			echo "<tr><td>" . $row["USERID"] . "</td><td>" . $row["DEVICECOUNT"] . "</td></tr>";
		}
	
		echo "</table>";
	}

	function doesForeignKeyExist($tableName, $columnName, $value) 
	{
		global $db_conn;

		// Prepare the SQL query to check the existence of the key
		$sql = "SELECT COUNT(*) FROM " . $tableName . " WHERE " . $columnName . " = :value";

		$statement = oci_parse($db_conn, $sql);
		oci_bind_by_name($statement, ":value", $value);

		// Execute the query
		oci_execute($statement, OCI_DEFAULT);

		// Fetch the result
		if ($row = oci_fetch_array($statement)) {
			// If count is more than 0, the foreign key exists
			return $row[0] > 0;
		}
		return false; // In case the query fails or count is 0
	}

	function handleCountRequest()
	{
		global $db_conn;

		$result = executePlainSQL("SELECT Count(*) FROM User_table");

		if (($row = oci_fetch_row($result)) != false) {
			echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
		}
	}

	function handleDisplayRequest()
	{
		global $db_conn;
	
		// Check if tableName is set in POST
		if (isset($_POST['tableName'])) {
			$tableName = $_POST['tableName'];
			$attributes = $_POST['attributes'];
	
			// Formulate SQL query
			$sql = "";
	
			if (!empty($attributes)) {
				// If attributes are provided, include them in the query
				$sql = "SELECT {$attributes} FROM {$tableName}";
			} else {
				// If attributes are not provided, select all attributes
				$sql = "SELECT * FROM {$tableName}";
			}
	
			// Execute SQL query
			$result = executePlainSQL($sql);
	
			// Check if result is not null before printing
			if ($result) {
				// Call printResult function
				printResult($result);
			} else {
				// Handle case when result is null
				echo "No data found for table: " . $tableName;
			}
		} else {
			// Handle case when tableName is not set in POST
			echo "Table Name not provided.";
		}
	}

	function handleSelectRequest() {
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
        echo "No results found.";
    }

    oci_commit($db_conn);
}

	
	
	function handlePOSTRequest()
	{
		echo "handlePOSTRequest called<br>";
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
			}

			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('countTuples', $_GET)) {
				handleCountRequest();
			 } // elseif (array_key_exists('displayTuples', $_GET)) {
			// 	handleDisplayRequest();
			// }

			disconnectFromDB();
		}
	}
	
	// PRINTERS

	function printResult($result) {
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
	
		echo "</table>";
	}

	function printInsertRequestResult() 
	{
		$result = executePlainSQL("SELECT * FROM NutritionInputs ORDER BY NutritionID");
		echo "<br>Retrieved data from Nutrition table:<br>";
		echo "<table>";
		echo "
			<tr>
				<th>NutritionID</th>
				<th>DeviceID</th>
				<th>UserID</th>
				<th>Calories</th>
				<th>Date</th>
			</tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>" .
					"<td>" . $row[0] . "</td>" .
					"<td>" . $row[1] . "</td>" .
					"<td>" . $row[2] . "</td>" .
					"<td>" . $row[3] . "</td>" .
					"<td>" . $row[4] . "</td>" .
				"<tr>";
		}
		echo "</table>";
	}

	// Handler Fetcher
	if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['submitAggregate']) 
	|| isset($_POST['submitMultiDeviceUsers']) || isset($_POST['displaySubmit']) || isset($_POST['selectSubmit']) || isset($_POST['deleteSubmit']) 
	|| isset($_POST['joinSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest'])) {
		handleGETRequest();
	} else if (isset($_GET['displayUsersRequest'])) {
		handleDisplayUsersRequest();
	}

	?>
</body>

</html>