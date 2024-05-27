<html>
    <body>
        <h2>Administer New Rec Pass</h2>
        <form method="POST" action="304project.php"> 
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            MemberID: <input type="number" name="insID" required> <br /><br />
            Name: <input type="text" name="insName"> <br /><br />
            Address: <input type="text" name="insAddr"> <br /><br />
            <label for="pass">Type:</label>
            <select id="pass" name="PassType">
                <option value="Adult">Adult</option>
                 <option value="Senior">Senior</option>
                 <option value="Youth">Youth</option>
                <option value="Student">Student</option>
            </select> <br /><br />
            <label for="length">Length:</label>
            <select id="length" name="Length">
                <option value="7">7 days</option>
                 <option value="30">30 days</option>
                 <option value="365">365 days</option>
            </select> <br /><br />

            <input type="submit" value="Insert" name="insertSubmit"></p>

        </form>

        <hr />

        <h2>Update Rental Equipment Information</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="304project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old EquipmentID: <input type="number" name="oldEquipmentID"> <br /><br />
            New EquipmentID: <input type="number" name="newEquipmentID"> <br /><br />
            Old RentalLength: <input type="text" name="oldRentalLength"> <br /><br />
            New RentalLength: <input type="text" name="newRentalLength"> <br /><br />
            Old MemberID: <input type="number" name="oldMemberID"> <br /><br />
            New MemberID: <input type="number" name="newMemberID"> <br /><br />
	        Old Status: <input type="text" name="oldStatus"> <br /><br />
            New Status: <input type="text" name="newStatus"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>User Fees</h2>

        <form method="GET" action="304project.php">
        <input type="hidden" id="nestedRequest" name="nestedRequest">

        <label for="pass">Calculate the average cost users from the following age group pay:</label>
            <select id="pass" name="PassType">
                <option value="Adult">Adult</option>
                 <option value="Senior">Senior</option>
                 <option value="Youth">Youth</option>
                <option value="Student">Student</option>
            </select> <br /><br />

            <input type="submit" value="Calculate" name="nestedReq"></p>
        </form>
    
        <hr />

       
        <!-- <h2>Print</h2> testing table entries
            <form method="POST" action="304project.php">
                <input type="hidden" id="printQueryRequest" name="printQueryRequest">
                <input type="submit" value="Print" name="print"></p>
            </form>

        <hr /> -->

        <h2>Group Users by Rec Pass Length (for passes with length >2 weeks) </h2>

        <form method="GET" action="304project.php">
            <input type="hidden" id="aggregationHavingQueryRequest" name="aggregationHavingQueryRequest">
            <input type="submit" value="Show" name="aggregationHaving">
        </form>

        <hr />

        <h2>Navigation</h2>
        <button onclick="location.href='search.php'">Search</button>
        <br><br>
        <button onclick="location.href='cafe.php'">Cafe</button>
        <br><br>
        <button onclick="location.href='loginpage.php'">Logout</button>
        <br>


    </body>
    <?php
        //Source:https://www.students.cs.ubc.ca/~cs-304/resources/php-oracle-resources/oracle-test.txt

        function handleNestedRequest() {
            global $db_conn;
            $passType = $_GET['PassType'];
            $result = executePlainSQL("SELECT AVG(Price) FROM (SELECT * FROM Users, RecPass
                                        WHERE Users.RecPassType = '$passType'
                                        AND Users.RecPassType = RecPass.Type
                                        AND Users.RecPassLength = RecPass.Length) GROUP BY Type");
            genericValuePrint($result, 0, "The average cost that the user of type '$passType' pays here is:");
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insID'],
                ":bind2" => $_POST['insAddr'],
                ":bind3" => $_POST['insName'],
                ":bind4" => $_POST['PassType'],
                ":bind5" => $_POST['Length']
            );

            $memberid = $_POST['insID'];
            $validID = validMemberID($memberid, executePlainSQL("SELECT MemberID FROM Users"));

            $alltuples = array (
                $tuple
            );

            if ($validID) {
                executeBoundSQL("
                insert into Users values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
                // uncomment for debugging
                // $example = executePlainSQL("SELECT * FROM Users");
                // printResult($example, 5);
                if ($result) {
                    OCICommit($db_conn);
                    echo "<br>Succesfully inserted data into Users.<br>";
                } 
            }        

        }

        function validMemberID($id) {
            $r = "SELECT MemberID FROM Users";
            $result = executePlainSQL($r);
        
            foreach ($result as $row) {
                if ($row['MEMBERID'] == $memberID) {
                    return false;
                }
            }
        
            return true;
        }



        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                // echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                // echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                // echo htmlentities($e['message']);
                $success = False;
            }

            return $statement;
        }

      	function handleUpdateRequest() {
            global $db_conn;

	    //Updating rental equipment attributes
            $old_EquipmentID = $_POST['oldEquipmentID'];
            $new_EquipmentID = $_POST['newEquipmentID'];
            $old_Status = $_POST['oldStatus'];
            $new_Status = $_POST['newStatus'];
            $old_RentalLength = $_POST['oldRentalLength'];
            $new_RentalLength = $_POST['newRentalLength'];
            $old_MemberID = $_POST['oldMemberID'];
            $new_MemberID = $_POST['newMemberID'];
	    

            // you need the wrap the old name and new name values with single quotations
            $r1 = executePlainSQL("UPDATE RentalEquipment SET EquipmentID='" . $new_EquipmentID . "' WHERE EquipmentID='" . $old_EquipmentID . "'");
            $r2 = executePlainSQL("UPDATE RentalEquipment SET Status='" . $new_Status . "' WHERE Status='" . $old_Status . "'");
            $r3 = executePlainSQL("UPDATE RentalEquipment SET RentalLength='" . $new_RentalLength . "' WHERE RentalLength='" . $old_RentalLength . "'");
            $r4 = executePlainSQL("UPDATE RentalEquipment SET MemberID='" . $new_MemberID . "' WHERE MemberID='" . $old_MemberID . "'");
            OCICommit($db_conn);

	    $change = false;
            if(($r1 or $r2 or $r3 or $r4) and
	       (($old_EquipmentID != $new_EquipmentID) or
		($old_Status != $new_Status) or
		($old_RentalLength != $new_RentalLength) or
		($old_MemberID != $new_MemberID))){
	        $change = true;
		echo "<br>Succesfully updated data in Rental Equipment:<br>";

             if($change){
                 $result = executePlainSQL("SELECT * FROM RentalEquipment");
                 echo "<br>Retrieved data from table RentalEquipment:<br>";
                 echo "<table>";
                 echo "<tr><th>EquipmentID</th><th>Status</th><th>RentalLength</th><th>MemberID</th></tr>";
                 printUpdate($result, 4);
                 echo "</table>";
             }
	    }
        }

	function printUpdate($result, $num_att) { //prints results from a select statement
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                for($i=0; $i < $num_att; $i++) {
                    echo "<td>" . $row[$i] . "</td>";
                }
                echo "</tr>";
            }
        }

    //         executeBoundSQL("
	// insert into Users values (:bind1, :bind2, :bind3, :bind4, :bind5)
	// ", $alltuples);
    //         OCICommit($db_conn);
    //     }

    function executeBoundSQL($cmdstr, $list) {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
    In this case you don't need to create the statement several times. Bound variables cause a statement to only be
    parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
    See the sample code below for how this function is used */

        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);
        $success = true;

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
                OCIBindByName($statement, $bind, $val);
                unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                // !!! remember to remove these on final submission
                // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                // echo htmlentities($e['message']);
                // echo "<br>";
                
                $success = False;
            }

            if ($success) {
                echo "<br>Succesfully added new user.<br>";
            }

            if (!$success) {
                echo "<br>Failed to insert new user. Please try again.<br>";
            }
        }
    }


        function handleAggregationHavingRequest() {
            global $db_conn;
            //group users by recpasslength having recpasslength>14
            $result = executePlainSQL("SELECT RecPassLength, COUNT(*) FROM Users GROUP BY RecPassLength HAVING RecPassLength>14");
            echo "<br>The number of users of each rec pass length >2 weeks is:<br>";
            OCICommit($db_conn);
            printResult($result, 2);

        }

	//note - update this
        function handleRequest() {
            if (connectToDB()) {
                if (array_key_exists('printQueryRequest', $_POST)) {
                    handlePrintRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('aggregationHavingQueryRequest', $_GET)) {
                    handleAggregationHavingRequest();
                } else if (array_key_exists('nestedRequest', $_GET)) {
                    handleNestedRequest();
                }
                disconnectFromDB();
            }
        }
        function handlePrintRequest() {
            $result = executePlainSQL("SELECT * FROM Users");
            printResult($result, 5);
        }
        function printResult($result, $num_att) { //prints results from a select statement
            echo "<br>Retrieved data from table Users:<br>";
            echo "<table>";
            echo "<tr><th>Length of Pass</th><th>Number of Users</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                for($i=0; $i < $num_att; $i++) {
                    echo "<td>" . $row[$i] . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            $db_conn = OCILogon("ora_asamra02", "a55296388", "dbhost.students.cs.ubc.ca:1522/stu");
            // echo "Successfully connected to Oracle.\n";//
            if ($db_conn) {
                //debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }
        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }
        if (isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['print']) || isset($_GET['nestedReq']) || isset($_GET['aggregationHaving'])) {
            handleRequest();
        }

        function genericTablePrint($result, $num_att, $description) { //prints results from a select statement
            echo $description;
            echo "<table>";


            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                for($i=0; $i < $num_att; $i++) {
                    echo "<td>" . $row[$i] . "|</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }


        function genericValuePrint($result, $num_att, $description) { //prints results from a select statement
            // echo "\n";
            $row = OCI_Fetch_Array($result, OCI_BOTH);
        
        if ($row !== false) {
            $value = $row[$num_att];
            
            echo $description . " $" . $value;
        } else {
            echo "<p>No results found.</p>";
        }
    }

    ?>
</html>
