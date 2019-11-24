<?php
SESSION_START();
$_SESSION['loginUser'] = "STU1";
$_SESSION['userClass'] = "STU";
$out = "";
$out .= $_SESSION['loginUser'];
$out .= $_SESSION['userClass'];
echo "<h6> $out<h6>";
$dbc = mysqli_connect('localhost', 'root', '', 'utem_student_tutor_system') or die("Connection not established");
$studentID = $_SESSION['loginUser'];
?>

<?php
function format_time_output($t) // t = seconds, f = separator 
{
    return sprintf("%02d%s%02d", floor($t / 3600), ':', ($t / 60) % 60);
}

$searchType = "";
$searchQuery = "";
$searchTable = 0;

if (isset($_GET['searchType'])) {
    $searchType = $_GET['searchType'];
}

if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
}
?>

<form method='POST'>
    <label>Search Type</label>
    <select name='searchType' required>
        <option <?php if ($searchType == "sessionSearch") echo 'selected="selected"'; ?>value='sessionSearch'>Search by SessionID</option>
        <option <?php if ($searchType == "tutorIDSearch") echo 'selected="selected"'; ?>value='tutorIDSearch'>Search by TutorID</option>
        <option <?php if ($searchType == "subjectCodeSearch") echo 'selected="selected"'; ?>value='subjectCodeSearch'>Search by Subject Code </option>
        <option <?php if ($searchType == "topicSearch") echo 'selected="selected"'; ?>value='topicSearch'>Search by Topic</option>
    </select>

    <input type='text' name='searchQuery' value='<?php echo $searchQuery ?>' pattern="[A-Za-z0-9 ]{0,30}" placeholder="(Maximum 30 characters)" maxlength="30">
    <input type='submit' name='search' value='Search'>
</form>
<form method='POST' action='stuShowRegisteredSession.php'>
    <input type='submit' value='Refresh'><br>
</form>

<?php
if (isset($_POST['search'])) {
    if ($_POST['searchType'] == "sessionSearch") {
        $searchTable = 1;
        $searchQuery = $_POST['searchQuery'];
    } else if ($_POST['searchType'] == "tutorIDSearch") {
        $searchTable = 2;
        $searchQuery = $_POST['searchQuery'];
    } else if ($_POST['searchType'] == "subjectCodeSearch") {
        $searchTable = 3;
        $searchQuery = $_POST['searchQuery'];
    } else if ($_POST['searchType'] == "topicSearch") {
        $searchTable = 4;
        $searchQuery = $_POST['searchQuery'];
    }
}
?>

<?php if ($searchTable == 0) { ?>
    <h3>All Registered Sessions</h3>
<?php }
else if($searchTable==1){ ?>
    <h3>Search by SessionID</h3>
    <?php }
else if($searchTable==2){ ?>
    <h3>Search by TutorID</h3>
    <?php }
else if($searchTable==3){ ?>
    <h3>Search by Subject Code</h3>
    <?php }
else if($searchTable==4){ ?>
    <h3>Search by Topic</h3>
<?php } ?>
    <table border='1'>
        <tr>
            <th>Session ID</th>
            <th>Topic</th>
            <th>Subject Code</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Duration (Hour:Minute)</th>
            <th>TutorID</th>
            <th>Location</th>
            <th>Registration Status</th>
        </tr>
        <?php
            if($searchTable==0){    
                $query = "SELECT t.sessionID,t.topic,t.subjectCode,t.date,t.startTime,t.endTime,t.tutorID, t.location FROM tutoringsession t, student s, session_student a WHERE a.sessionID=t.sessionID AND a.studentID=s.studentID AND s.studentID='$studentID' ORDER BY t.sessionID DESC;";
            }
            else if ($searchTable==1){
                $query = "SELECT t.sessionID,t.topic,t.subjectCode,t.date,t.startTime,t.endTime,t.tutorID, t.location FROM tutoringsession t, student s, session_student a WHERE a.sessionID=t.sessionID AND a.studentID=s.studentID AND s.studentID='$studentID' AND t.sessionID='$searchQuery';";
            }
            else if ($searchTable==2){
                $query = "SELECT t.sessionID,t.topic,t.subjectCode,t.date,t.startTime,t.endTime,t.tutorID, t.location FROM tutoringsession t, student s, session_student a WHERE a.sessionID=t.sessionID AND a.studentID=s.studentID AND s.studentID='$studentID' AND t.tutorID='$searchQuery';";
            }
            else if ($searchTable==3){
                $query = "SELECT t.sessionID,t.topic,t.subjectCode,t.date,t.startTime,t.endTime,t.tutorID, t.location FROM tutoringsession t, student s, session_student a WHERE a.sessionID=t.sessionID AND a.studentID=s.studentID AND s.studentID='$studentID' AND t.subjectCode='$searchQuery';";
            }
            else{
                $query = "SELECT t.sessionID,t.topic,t.subjectCode,t.date,t.startTime,t.endTime,t.tutorID, t.location FROM tutoringsession t, student s, session_student a WHERE a.sessionID=t.sessionID AND a.studentID=s.studentID AND s.studentID='$studentID' AND t.topic LIKE '%$searchQuery%';";
            }
            $result = mysqli_query($dbc, $query) or die("Query Failed $query");

            $currentDate = date('Y-m-d', time());
            $currentTime = date('His', time());
            $currentTime += "070000";

            while ($row = mysqli_fetch_assoc($result)) {
                $duration = format_time_output(strtotime($row['endTime']) - strtotime($row['startTime']));
                $expiredSession = false;
                ?>
            <tr>
                <form method='POST' action='stuSessionSignUp.php'>
                    <td><?php echo $row['sessionID']; ?></td>
                    <td><?php echo $row['topic']; ?></td>
                    <td><?php echo $row['subjectCode']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['startTime']; ?></td>
                    <td><?php echo $row['endTime']; ?></td>
                    <td><?php echo $duration; ?></td>
                    <td><?php echo $row['tutorID']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>
                        <?php
                                $sessionRegistered = false;
                                $query2 = "SELECT studentID FROM session_student WHERE sessionID='{$row['sessionID']}';";
                                $result2 = mysqli_query($dbc, $query2) or die("Query Failed $query2");
                                $stuID = mysqli_fetch_assoc($result2);
                                if ($stuID['studentID'] === $_SESSION['loginUser']) {
                                    $sessionRegistered = true;
                                }
                                ?>
                        <input type="text" name="sessionID" value="<?php echo $row['sessionID']; ?>" style="display:none">
                        <input type="text" name="topic" value="<?php echo $row['topic']; ?>" style="display:none">
                        <input type="text" name="subjectCode" value="<?php echo $row['subjectCode']; ?>" style="display:none">
                        <input type="text" name="date" value="<?php echo $row['date']; ?>" style="display:none">
                        <input type="text" name="startTime" value="<?php echo $row['startTime']; ?>" style="display:none">
                        <input type="text" name="endTime" value="<?php echo $row['endTime']; ?>" style="display:none">
                        <input type="text" name="duration" value="<?php echo $duration; ?>" style="display:none">
                        <input type="text" name="tutorID" value="<?php echo $row['tutorID']; ?>" style="display:none">
                        <input type="text" name="location" value="<?php echo $row['location']; ?>" style="display:none">
                        <?php if (date('Y-m-d', strtotime($row['date'])) < $currentDate) {
                                    echo "Expired Session";
                                    $expiredSession = true;
                                } else {
                                    if ((date('Y-m-d', strtotime($row['date']))) == $currentDate) {
                                        if (date('His', strtotime($row['startTime'])) <= $currentTime) {
                                            echo "Expired Session";
                                            $expiredSession = true;
                                        } elseif (((date('His', strtotime($row['startTime']))) - $currentTime) <= "035900" && $sessionRegistered != true) {
                                            echo "Registration Closed";
                                        }
                                    }
                                }
                        if($expiredSession != true){
                            echo"Registered";
                        }
                                    
                        ?>
                    </td>
                </form>
            </tr>
        <?php } ?>
</table>

<?php
if (isset($_POST['deregister'])) {
    $query = "DELETE FROM session_student WHERE ('{$_POST['sessionID']}','{$_SESSION['loginUser']}');";
    $result = mysqli_query($dbc, $query) or die("Query Failed $query");
    echo '<meta http-equiv="refresh" content="0">';
    die();
}
?>