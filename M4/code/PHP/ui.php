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
	<title>CPSC 304 PHP/Oracle Demonstration</title>
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
		New Age: <input type="text" name="updateAge"> <br /><br />
		New Gender: <input type="text" name="updateGender"> <br /><br />

		<input type="submit" value="Update" name="updateSubmit"></p>
	</form>

	<hr />

	<h2>Count the Tuples in User table</h2>
	<form method="GET" action="ui.php">
		<input type="hidden" id="countTupleRequest" name="countTupleRequest">
		<input type="submit" name="countTuples"></p>
	</form>

	<hr />

	<h2>Display Tuples in User table</h2>
	<form method="GET" action="ui.php">
		<input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
		<input type="submit" name="displayTuples"></p>
	</form>


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

	function handleUpdateRequest()
	{
		global $db_conn;

            $tuple = array (
                ":UserID" => $_POST['updateUserID'],
                ":Age" => $_POST['updateAge'],
                ":Gender" => $_POST['updateGender'],
            );

            $alltuples = array (
                $tuple
            );

			// echo "<br>RESULT BEFORE UPDATE:</br>";
			// printUpdateRequestResult();

			executeBoundSQL("
				UPDATE User_table U
				SET U.Age = :Age, U.Gender = :Gender
				WHERE U.UserID = :UserID
				
			", $alltuples);

			// echo "<br>RESULT AFTER UPDATE:</br>";
			// printUpdateRequestResult();

            oci_commit($db_conn);
	}

	
	

	function handleResetRequest()
	{
		global $db_conn;
		// Drop old table
		executePlainSQL("DROP TABLE demoTable");

		// Create new table
		echo "<br> creating new table <br>";
		executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
		oci_commit($db_conn);
	}

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

	function handleAggregateCaloriesRequest() {
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

	function handleMultiDeviceUsersRequest() {
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

	function doesForeignKeyExist($tableName, $columnName, $value) {
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
		$result = executePlainSQL("SELECT * FROM User_table");
		printUsersTable($result);
	}


	// HANDLERS

	function handleDisplayUsersRequest()
	{
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM User_table");
    printUsersTable($result);
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
			}

			disconnectFromDB();
		}
	}


	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('countTuples', $_GET)) {
				handleCountRequest();
			} elseif (array_key_exists('displayTuples', $_GET)) {
				handleDisplayRequest();
			}

			disconnectFromDB();
		}
	}

	// PRINTERS

	function printInsertRequestResult() {
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

	function printUsersTable($result)
	{
		echo "<h2>Displaying Users</h2>";
		echo "<table border='1'>";
		echo "<tr><th>User ID</th><th>Age</th><th>Gender</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["USERID"] . "</td><td>" . $row["AGE"] . "</td><td>" . $row["GENDER"] . "</td></tr>"; 
		}

		echo "</table>";
	}


	// Handler Fetcher
	if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['submitAggregate']) || isset($_POST['submitMultiDeviceUsers'])) {
		handlePOSTRequest();
	} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest'])) {
		handleGETRequest();
	} else if (isset($_GET['displayUsersRequest'])) {
		handleDisplayUsersRequest();
	}

	?>
</body>

</html>