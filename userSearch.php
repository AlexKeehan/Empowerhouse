<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    if (!$loggedIn) {
        header('Location: login.php');
        die();
    }
    require_once('database/dbPersons.php');

        //function getonlythose_dbPersons_by_name($type, $status, $name) {
    $users = getall_users();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Find All Users</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Users</h1>
        <main class="search-form">
            <?php
                if (isset($users)) {
                    require_once('include/output.php');
                    if (count($users) > 0) {

                        echo "
                                <table class='user' width='500'>
                                    <thead>
                                        <tr>
                                            <td width = '20%'><b>ID </b></td>
                                            <td width = '20%'><b>First Name</b></td>
                                            <td width = '20%'><b>Last Name</b></td>
                                            <td width = '20%'><b>Phone</b></td>
                                            <td width = '20%'><b>Type</b></td>
                                        </tr>
                                    </thead>
                                </table>
                            ";



                        foreach ($users as $user) {
                            $count = count($users);
                            $id = $user->get_id();
                            $first_name = $user->get_first_name();
                            $last_name = $user->get_last_name();
                            $phone = $user->get_phone1();
                            $type = $user->get_type()[0];
                            echo "
                                <table class='user' width='500'>
                                    <tbody>
                                        <tr><td width = '20%'>" . $id . "</td>
                                        <td width = '20%'>" . $first_name . "</td>
                                        <td width = '20%'>" . $last_name . "</td>
                                        <td width = '20%'>" . $phone . "</td>
                                        <td width = '20%'>" . $type . "</td>
                                    </tbody>
                                </table>
                            ";
                        }
                    } else {
                        echo '<div class="error-toast">Your search returned no results.</div>';
                    }
                }
            ?>
            
            
            
            <!-- <form method="post">
                <label for="name">First/Last Name</label>
                <input type="text" name="name" id="name" placeholder="Enter first or last name" required>
                <input type="submit" name="submitName" id="submitName" value="Search">
            </form> -->
            <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
        </main>
    </body>
</html>