<html>
    <body>

        <h2>Delete Cafe</h2>
        <p>Please use numerical values</p>
        <p>If the CafeID entered is valid but not in the system there will be no change</p>
        <form method="POST" action="cafe.php">
            <input type="hidden" id="delCafeRequest" name="delCafeRequest">
            CafeID: <input type="number" name="ID"> <br /><br /> 
            <input type="submit" value="Delete" name="delete"></p>
        </form>

        <h2>Pricing Information</h2>
        <form method="GET" action="cafe.php"> <!--refresh page when submitted-->
            <input type="hidden" id="pricingRequest" name="pricingRequest">
            <label for="Pricing">Type:</label>
            <select id="Pricing" name="PricingType">
                <option value="AVG">Average Price</option>
                 <option value="MIN">Lowest Price</option>
                 <option value="MAX">Highest Price</option>
            </select> <br /><br />
            <input type="submit" name="pricing"></p>
        </form>

        <h2>Find Menu Items Sold at Every Cafe</h2>
        <form method="POST" action="cafe.php">
            <input type="hidden" id="divideRequest" name="divideRequest">
            <input type="submit" value="Find" name="divide"></p>
        </form>

        <h2>Check Which Users Bought A Menu Item at Any Cafe</h2>
        <form method="GET" action="cafe.php">
          <input type="hidden" id="joinRequest" name="joinRequest">
        <input type="submit" value="Check" name="check"></p>
        </form>
        <br>
        <h2>Navigation</h2>
        <button onclick="location.href='304project.php'">Home</button>
        <br><br>
        <button onclick="location.href='search.php'">Search</button>
        <br><br>
        <button onclick="location.href='loginpage.php'">Logout</button>    </body>
    <?php
        function handleDeleteRequest() {
            global $db_conn;
            $CafeID=$_POST['ID'];
            $result = executePlainSQL("SELECT * FROM Cafe");
            echo "<br>Retrieved data from table Cafe:<br>";
            echo "<table>";
            echo "<tr><th>CafeID</th></tr>";
            printResult($result, 1);
            echo "</table>";
            if(ctype_digit(strval($CafeID))){
                $result = executePlainSQL("DELETE FROM Cafe WHERE CafeID =$CafeID");
                OCICommit($db_conn);
                $result = executePlainSQL("SELECT * FROM Cafe");
                echo "<br>Successfully Deleted Cafe: ". $CafeID . "<br>";
                echo "<br>Updated Cafe table:<br>";
                echo "<table>";
                echo "<tr><th>CafeID</th></tr>";
                printResult($result, 1);
                echo "</table>";
            }
            else{
                echo "Please enter a integer for CafeID";
            }
        }

        function handleDivideRequest() {
            global $db_conn;
            $result = executePlainSQL("SELECT * FROM Cafe");
            echo "<br>Retrieved data from table Cafe:<br>";
            echo "<table>";
            echo "<tr><th>CafeID</th></tr>";
            printResult($result, 1);
            echo "</table>";
            $result = executePlainSQL("SELECT * FROM MenuItem");
            echo "<br>Retrieved data from table MenuItem:<br>";
            echo "<table>";
            echo "<tr><th>CafeID</th><th>Type</th><th>Price</th></tr>";
            printResult($result, 3);
            echo "</table>";
            $result = executePlainSQL("SELECT Types FROM MenuItem GROUP BY Types
                                        HAVING COUNT(DISTINCT cafeID) = (SELECT COUNT(DISTINCT cafeID) FROM cafe)");
            OCICommit($db_conn);
            echo "<br>Item Sold at each Cafe(Division Result):<br>";
            echo "<table>";
            echo "<tr><th>Type</th></tr>";
            printResult($result, 1);
            echo "</table>";
        }

        function handlePricingRequest() {
            global $db_conn;
            $Type = $_GET['PricingType'];
            if($Type == 'AVG'){
                $result = executePlainSQL("SELECT CafeID, AVG(Price) FROM MenuItem GROUP BY CafeID");
                echo "<br>Average Pricing for each Cafe<br>";
            }
            elseif ($Type == 'MIN') {
                $result = executePlainSQL("SELECT CafeID, MIN(Price) FROM MenuItem GROUP BY CafeID");
                echo "<br>Lowest price for a product for each Cafe<br>";
            }
            elseif ($Type == 'MAX') {
                $result = executePlainSQL("SELECT CafeID, MAX(Price) FROM MenuItem GROUP BY CafeID");
                echo "<br>Highest price for a product for each Cafe<br>";
            }
            OCICommit($db_conn);
            echo "<br>Retrieved data from table MenuItem:<br>";
            echo "<table>";
            echo "<tr><th>CafeID</th><th>Price</th></tr>";
            printResult($result, 2);
            echo "</table>";
        }

        function handlejoinRequest() {
            global $db_conn;
            $result = executePlainSQL("SELECT Users.Name FROM Users, Buy WHERE Buy.UserID = Users.MemberID");
            OCICommit($db_conn);
            printResultUsers($result); // change this
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }
			return $statement;
		}

        function printResultUsers($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr><th>User Names:</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }
        function printResult($result, $num_att) { //prints results from a select statement
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                for($i=0; $i < $num_att; $i++) {
                    echo "<td>" . $row[$i] . "</td>";
                }
                echo "</tr>";
            }
        }
        function connectToDB() {
            global $db_conn;

            $db_conn = OCILogon("ora_asamra02", "a55296388", "dbhost.students.cs.ubc.ca:1522/stu");
            if ($db_conn) {
                // debugAlertMessage("Database is Connected");
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
        function handleRequest() {
            if (connectToDB()) {
                if (array_key_exists('delCafeRequest', $_POST)) {
                    handleDeleteRequest();
                }
                if (array_key_exists('divideRequest', $_POST)) {
                    handleDivideRequest();
                }
                if (array_key_exists('pricingRequest', $_GET)) {
                    handlePricingRequest();
                }
                if (array_key_exists('joinRequest', $_GET)) {
                    handlejoinRequest();
                }
                disconnectFromDB();
            }
        }
        if (isset($_POST['delete']) || isset($_POST['divide']) || isset($_GET['pricing']) || isset($_GET['check'])) {
            handleRequest();
        }
    ?>
</html>