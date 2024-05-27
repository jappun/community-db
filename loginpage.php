<html>
    <body>
        <h2>Login</h2>
            <form method="POST" action="304project.php">
                <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
                <p><input type="submit" value="Login" name="login"></p>
            </form>
            <form method="POST" action="loginpage.php">
                <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
                <p><input type="submit" value="Set Tables" name="login"></p>
            </form>
        <!-- <h2>Print</h2>
            <form method="POST" action="loginpage.php">
                <input type="hidden" id="printQueryRequest" name="printQueryRequest">
                <input type="submit" value="Print" name="print"></p>
            </form> -->
    </body>
    <?php
        function handleLoginRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE EventHall2");
            executePlainSQL("DROP TABLE Gym");
            executePlainSQL("DROP TABLE Buy");
            executePlainSQL("DROP TABLE Issue");
            executePlainSQL("DROP TABLE Maintain");
            executePlainSQL("DROP TABLE HeldIn");
            executePlainSQL("DROP TABLE MenuItem");
            executePlainSQL("DROP TABLE Cafe");
            executePlainSQL("DROP TABLE EmployeesWorkingIn");
            executePlainSQL("DROP TABLE RentalEquipment");
            executePlainSQL("DROP TABLE ClassHeldIn1");
            executePlainSQL("DROP TABLE ClassHeldIn2");
            executePlainSQL("DROP TABLE Events");
            executePlainSQL("DROP TABLE EventHall1");
            executePlainSQL("DROP TABLE Facilities");
            executePlainSQL("DROP TABLE Users");
            executePlainSQL("DROP TABLE RecPass");
            // Create new tables
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE Cafe (cafeID integer PRIMARY KEY)");
            executePlainSQL("CREATE TABLE MenuItem
                (cafeID int, 
                Types CHAR(20),
                Price REAL,
                PRIMARY KEY (cafeID, Types),
                FOREIGN KEY (cafeID) REFERENCES Cafe ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE Facilities (RoomNum INTEGER PRIMARY KEY)");
            executePlainSQL("CREATE TABLE RecPass
                (Type CHAR(20),
                Length INTEGER,
                Price FLOAT,
                PRIMARY KEY (Type, Length))");
            executePlainSQL("CREATE TABLE Users
                (MemberID INTEGER PRIMARY KEY,
                Address CHAR(20),
                Name CHAR(20),
                RecPassType CHAR(20) NOT NULL,
                RecPassLength INTEGER NOT NULL,
                FOREIGN KEY(RecPassType, RecPassLength) REFERENCES RecPass(Type, Length)ON DELETE CASCADE)");
            executePlainSQL("CREATE TABLE EmployeesWorkingIn
                (SIN INTEGER PRIMARY KEY,
                Name CHAR(20),
                Address CHAR(20),
                RoomNum INTEGER NOT NULL,
                FOREIGN KEY (RoomNum) REFERENCES Facilities)");
            executePlainSQL("CREATE TABLE RentalEquipment
                (EquipmentID INTEGER PRIMARY KEY, 
                Status CHAR(20), 
                RentalLength CHAR(20), 
                MemberID INTEGER UNIQUE,
                FOREIGN KEY (MemberID) REFERENCES Users)");
            executePlainSQL("CREATE TABLE ClassHeldIn1
                (Day DATE, 
                ClassName CHAR(20), 
                RoomNum INTEGER, 
                PRIMARY KEY (RoomNum,Day),
                FOREIGN KEY (RoomNum) REFERENCES Facilities)");
            executePlainSQL("CREATE TABLE ClassHeldIn2
                (ClassName CHAR(20), 
                ClassType CHAR(20), 
                PRIMARY KEY (ClassName))");
            executePlainSQL("CREATE TABLE Events (EventID INTEGER PRIMARY KEY)");

            executePlainSQL("CREATE TABLE EventHall1
                (SizeInSqFt INTEGER,
                RoomNum INTEGER PRIMARY KEY,
                FOREIGN KEY (RoomNum) REFERENCES Facilities)");
            
            executePlainSQL("CREATE TABLE EventHall2
                (SizeInSqFt INTEGER PRIMARY KEY,
                MaxOccupancy INTEGER NOT NULL)");   

            executePlainSQL("CREATE TABLE Gym
                (RoomNum INTEGER PRIMARY KEY,
                FOREIGN KEY (RoomNum) REFERENCES Facilities)");
            
            executePlainSQL("CREATE TABLE Buy 
                (MenuItemType CHAR(20), 
                UserID INTEGER, 
                CafeID INTEGER,
                PRIMARY KEY (MenuItemType, UserID, CafeID),
                FOREIGN KEY (CafeID, MenuItemType) REFERENCES MenuItem ON DELETE CASCADE,
                FOREIGN KEY (UserID) REFERENCES Users)");
                
            executePlainSQL("CREATE TABLE Issue
                (EmployeeSIN INTEGER,
                RecPassType CHAR(20),
                RecPassLength INTEGER,
                IssueDate DATE,
                PRIMARY KEY(EmployeeSIN, RecPassType, RecPassLength),
                FOREIGN KEY (EmployeeSIN) REFERENCES EmployeesWorkingIn(SIN),
                FOREIGN KEY (RecPassType, RecPassLength) REFERENCES RecPass(Type, Length))");
            
            executePlainSQL("CREATE TABLE Maintain
                (EquipmentID INTEGER,
                EmployeeSIN INTEGER,
                FOREIGN KEY (EmployeeSIN) REFERENCES EmployeesWorkingIn(SIN),
                FOREIGN KEY (EquipmentID) REFERENCES RentalEquipment,
                PRIMARY KEY (EquipmentID, EmployeeSIN))");

            executePlainSQL("CREATE TABLE HeldIn
                (EventID INTEGER,
                RoomNum INTEGER,
                Day DATE,
                FOREIGN KEY (EventID) REFERENCES Events,
                FOREIGN KEY (RoomNum) REFERENCES EventHall1,
                PRIMARY KEY (EventID, RoomNum))");  
                
            // load pre-existing data
            executePlainSQL(
                "INSERT ALL
                INTO Cafe(cafeID) VALUES (123)
                INTO Cafe(cafeID) VALUES (324)
                INTO Cafe(cafeID) VALUES (411)
                INTO Cafe(cafeID) VALUES (456)
                INTO Cafe(cafeID) VALUES (789)
                SELECT * FROM dual"
            );
            executePlainSQL(
                "INSERT ALL
                INTO MenuItem(cafeID, Types, Price) VALUES (123, 'Drink', 2.00)
                INTO MenuItem(cafeID, Types, Price) VALUES (123, 'Food', 5.00)
                INTO MenuItem(cafeID, Types, Price) VALUES (324, 'Drink', 3.50)
                INTO MenuItem(cafeID, Types, Price) VALUES (411, 'Drink', 6.00)
                INTO MenuItem(cafeID, Types, Price) VALUES (456, 'Food', 4.75)
                INTO MenuItem(cafeID, Types, Price) VALUES (456, 'Drink', 2.00)
                INTO MenuItem(cafeID, Types, Price) VALUES (789, 'Drink', 2.00)
                SELECT * FROM dual");
             executePlainSQL(
                "INSERT ALL
                INTO RecPass(Type, Length, Price) VALUES ('Student', 7, 10.00)
                INTO RecPass(Type, Length, Price) VALUES ('Student', 30, 30.00)
                INTO RecPass(Type, Length, Price) VALUES ('Student', 365, 300.00)
                INTO RecPass(Type, Length, Price) VALUES ('Senior', 7, 3.00)
                INTO RecPass(Type, Length, Price) VALUES ('Senior', 30, 15.00)
                INTO RecPass(Type, Length, Price) VALUES ('Senior', 365, 100.00)
                INTO RecPass(Type, Length, Price) VALUES ('Adult', 7, 12.00)
                INTO RecPass(Type, Length, Price) VALUES ('Adult', 30, 50.00)
                INTO RecPass(Type, Length, Price) VALUES ('Adult', 365, 400.00)
                INTO RecPass(Type, Length, Price) VALUES ('Youth', 7, 4.00)
                INTO RecPass(Type, Length, Price) VALUES ('Youth', 30, 20.00)
                INTO RecPass(Type, Length, Price) VALUES ('Youth', 365, 200.00)
                SELECT * FROM dual");

            executePlainSQL(
                "INSERT ALL
                INTO Users(MemberID, Address, Name, RecPassType, RecPassLength) VALUES (1, '10 Crescent Road', 'Maya', 'Student', 30)
                INTO Users(MemberID, Address, Name, RecPassType, RecPassLength) VALUES (2, '14 Bow St', 'Mary', 'Senior', 365)
                INTO Users(MemberID, Address, Name, RecPassType, RecPassLength) VALUES (3, '123 Sesame St', 'Bailey', 'Adult', 7)
                INTO Users(MemberID, Address, Name, RecPassType, RecPassLength) VALUES (4, '2 Rose Road', 'Jeff', 'Youth', 30)
                INTO Users(MemberID, Address, Name, RecPassType, RecPassLength) VALUES (5, '13 Thorn St', 'Kayley', 'Adult', 365)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO RentalEquipment (EquipmentID, Status, RentalLength, MemberID) VALUES (0, 'Available', NULL, NULL)
                INTO RentalEquipment (EquipmentID, Status, RentalLength, MemberID) VALUES (1, 'Rented', '3Days', 3)
                INTO RentalEquipment (EquipmentID, Status, RentalLength, MemberID) VALUES (2, 'Rented', '1Month', 2)
                INTO RentalEquipment (EquipmentID, Status, RentalLength, MemberID) VALUES (3, 'Available', NULL, NULL)
                INTO RentalEquipment (EquipmentID, Status, RentalLength, MemberID) VALUES (4, 'Available', '7Days', 1)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Events (EventID) VALUES (0)
                INTO Events (EventID) VALUES (1)
                INTO Events (EventID) VALUES (2)
                INTO Events (EventID) VALUES (3)
                INTO Events (EventID) VALUES (4)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Facilities (RoomNum) VALUES (100)
                INTO Facilities (RoomNum) VALUES (101)
                INTO Facilities (RoomNum) VALUES (102)
                INTO Facilities (RoomNum) VALUES (103)
                INTO Facilities (RoomNum) VALUES (104)
                INTO Facilities (RoomNum) VALUES (105)
                INTO Facilities (RoomNum) VALUES (106)
                INTO Facilities (RoomNum) VALUES (107)
                INTO Facilities (RoomNum) VALUES (108)
                INTO Facilities (RoomNum) VALUES (109)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO EventHall1 (SizeInSqFt, RoomNum) VALUES (350, 100)
                INTO EventHall1 (SizeInSqFt, RoomNum) VALUES (450, 101)
                INTO EventHall1 (SizeInSqFt, RoomNum) VALUES (2500, 107)
                INTO EventHall1 (SizeInSqFt, RoomNum) VALUES (3000, 108)
                INTO EventHall1 (SizeInSqFt, RoomNum) VALUES (1500, 109)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO EventHall2(SizeInSqFt, MaxOccupancy) VALUES (350, 65)
                INTO EventHall2(SizeInSqFt, MaxOccupancy) VALUES (450, 60)
                INTO EventHall2(SizeInSqFt, MaxOccupancy) VALUES (2500, 150)
                INTO EventHall2(SizeInSqFt, MaxOccupancy) VALUES (3000, 200)
                INTO EventHall2(SizeInSqFt, MaxOccupancy) VALUES (1500, 85)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Gym(RoomNum) VALUES (102)
                INTO Gym(RoomNum) VALUES (103)
                INTO Gym(RoomNum) VALUES (104)
                INTO Gym(RoomNum) VALUES (105)
                INTO Gym(RoomNum) VALUES (106)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO ClassHeldIn1(Day, RoomNum, ClassName) VALUES ('15-JUN-2023', 100, 'Pilates')
                INTO ClassHeldIn1(Day, RoomNum, ClassName) VALUES ('20-JUN-2023', 101, 'Kickboxing')
                INTO ClassHeldIn1(Day, RoomNum, ClassName) VALUES ('04-JUL-2023', 107, 'Woodwork')
                INTO ClassHeldIn1(Day, RoomNum, ClassName) VALUES ('14-AUG-2023', 108, 'Sewing')
                INTO ClassHeldIn1(Day, RoomNum, ClassName) VALUES ('19-AUG-2023', 109, 'French')
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO ClassHeldIn2(ClassName, ClassType) VALUES ('Pilates', 'Exercise')
                INTO ClassHeldIn2(ClassName, ClassType) VALUES ('Kickboxing', 'Exercise')
                INTO ClassHeldIn2(ClassName, ClassType) VALUES ('Woodwork', 'Crafts')
                INTO ClassHeldIn2(ClassName, ClassType) VALUES ('Sewing', 'Crafts')
                INTO ClassHeldIn2(ClassName, ClassType) VALUES ('French', 'Languages')
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Buy(MenuItemType, UserID, CafeID) VALUES ('Drink', 1, 123)
                INTO Buy(MenuItemType, UserID, CafeID) VALUES ('Drink', 3, 324)
                INTO Buy(MenuItemType, UserID, CafeID) VALUES ('Food', 2, 123)
                INTO Buy(MenuItemType, UserID, CafeID) VALUES ('Drink', 4, 411)
                INTO Buy(MenuItemType, UserID, CafeID) VALUES ('Food', 5, 123)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (123456789, 'John', '45 Rose Road', 101)
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (987654321, 'Jess', '23 Thorn St', 102)
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (132465798, 'Said', '2 Bow St', 107)
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (978645312, 'Rose', '222 Crescent Road', 108)
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (912834756, 'Emma', '32 Thorn St', 109)
                INTO EmployeesWorkingIn(SIN, Name, Address, RoomNum) VALUES (123890371, 'Kelly', '1 Bow St', 109)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Issue(EmployeeSIN, RecPassType, RecPassLength, IssueDate) VALUES (123456789, 'Student', 7, '03-MAR-2023')
                INTO Issue(EmployeeSIN, RecPassType, RecPassLength, IssueDate) VALUES (987654321, 'Student', 30, '15-JAN-2023')
                INTO Issue(EmployeeSIN, RecPassType, RecPassLength, IssueDate) VALUES (132465798, 'Senior', 7, '02-MAR-2023')
                INTO Issue(EmployeeSIN, RecPassType, RecPassLength, IssueDate) VALUES (978645312, 'Adult', 7, '01-MAY-2023')
                INTO Issue(EmployeeSIN, RecPassType, RecPassLength, IssueDate) VALUES (912834756, 'Youth', 30, '03-MAY-2023')
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO Maintain(EquipmentID, EmployeeSIN) VALUES (0, 123456789)
                INTO Maintain(EquipmentID, EmployeeSIN) VALUES (1, 987654321)
                INTO Maintain(EquipmentID, EmployeeSIN) VALUES (2, 132465798)
                INTO Maintain(EquipmentID, EmployeeSIN) VALUES (3, 978645312)
                INTO Maintain(EquipmentID, EmployeeSIN) VALUES (4, 912834756)
                SELECT * FROM dual");
            executePlainSQL(
                "INSERT ALL
                INTO HeldIn(EventID, RoomNum, Day) VALUES (0, 100, '01-JAN-2023')
                INTO HeldIn(EventID, RoomNum, Day) VALUES (1, 101, '14-FEB-2023')
                INTO HeldIn(EventID, RoomNum, Day) VALUES (2, 107, '20-MAR-2023')
                INTO HeldIn(EventID, RoomNum, Day) VALUES (3, 108, '21-APR-2023')
                INTO HeldIn(EventID, RoomNum, Day) VALUES (4, 109, '29-MAY-2023')
                SELECT * FROM dual");
            OCICommit($db_conn);
        }


        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);

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
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleLoginRequest();
                }
                else if (array_key_exists('printQueryRequest', $_POST)) {
                    handlePrintRequest();
                }
                disconnectFromDB();
            }
        }

        function handlePrintRequest() {
            $result = executePlainSQL("SELECT * FROM Cafe");
            printResult($result);
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table Cafe:<br>";
            echo "<table>";
            echo "<tr><th>CafeID</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            $db_conn = OCILogon("ora_asamra02", "a55296388", "dbhost.students.cs.ubc.ca:1522/stu");
            echo "Successfully connected to Oracle.\n";
            if ($db_conn) {
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
        if (isset($_POST['login']) || isset($_POST['print'])) {
            handlePOSTRequest();
        }
    ?>
</html>
