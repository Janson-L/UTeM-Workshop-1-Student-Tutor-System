<?php
SESSION_START();
if (preg_match("/\AADM/", @$_SESSION['loginUser'])) {
?>

<head>
    <title>Confirmation</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/outStyle.css">
    </head>
    <ul>
        <li><a href="admUI.php">Home</a></li>
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropbtn">Manage Users</a>
            <div class="dropdown-content">
            <a href="admAddUser.php">Add User</a>
            <a href="admManageUsers.php">Update and Delete Users</a>
        </div>
        </li>
        <li class="active"><a href="admManageTutorSession.php">Manage Tutor Session</a></li>
        <li><a href="admSystemUsageStatistics.php">System Usage Statistics</a></li>
        <li style="float:right"><a href="logOut.php">Log Out</a></li>
    </ul>


<?php 
    $dbc = mysqli_connect('localhost', 'root', '', 'utem_student_tutor_system') or die("Connection not established");
    $userClass="";

?>

    <?php if(!isset($_POST['deleteSessionConfirm'])){ ?>
        <h2>Confirmation </h2>
        <div class="container">
        <div class="prompt">Are you sure you want to delete <?php echo "{$_POST['topic']} ({$_POST['sessionID']})" ?> ?</div>
    <form method='POST'>
        <input type="text" name="sessionID" value="<?php echo $_POST['sessionID']; ?>" style="display:none">
        <div class="row">
        <div class="col-40"><input type="submit" name="deleteSessionConfirm" value="Confirm"></div>
    </form>

    <form method='POST' action='admManageTutorSession.php'>
    <div class="col-25" > <input type="submit" value="Cancel"></div>
    </form>
    </div>
</div>
    <?php } ?>
<?php
    if(isset($_POST['deleteSessionConfirm']))
    {
       $query="DELETE FROM tutoringSession WHERE sessionID='{$_POST['sessionID']}';";
       $result=mysqli_query($dbc,$query) or die("Query Failed $query");
       mysqli_close($dbc);
       echo"Update successful. You will now be redirected back to Manage User UI.";
       header("Refresh:5;URL=admManageTutorSession.php");
       die();
    }
        ?>

<?php
} else {
    echo "<h3>You don't have the privilege to view this page. You will be logged out and redirected to the login page in 5 seconds.<br> Please login with the correct account.</h3>";
    header("Refresh:5;URL=logOut.php");
    die();
}
?>