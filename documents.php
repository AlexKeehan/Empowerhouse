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
?>
<!DOCTYPE html>
<html>

<head>
    <?php require_once('universal.inc') ?>
    <title>Empowerhouse VMS | Documents</title>
</head>

<body>
    <?php require_once('header.php') ?>
    <main>
        <!-- Your code goes here. Be sure to wrap any form elements in a <form> tag -->
        <div id="DisplayElem">
            <div>
                <h1 class="DocumentsListTitle">Documents</h1>
                <div>
                    <div class="DocumentsListTabsContainer">
                        <div>
                            <div class="row">
                                <ul id="DocumentsListTabsList"
                                    style="margin: 0 0 20px;padding: 0;float: left;border-bottom: 1px solid #e5e5e5;width: 100%;">
                                    <div class="column">
                                        <li id="DocumentsListFilesTab"
                                            style="position: relative;display: block;list-style-type: none;background: 0 0;float: left;margin: 0 4px 0 0;padding: 0;font-weight: 700;height: 35px;font-size: 14px;">
                                            <a href="http://localhost/Codebase/documentRecords.php">
                                                <span>Paperwork &amp; Forms</span>
                                            </a>
                                            <div id="UnderlineBox"
                                                style="height:2px;width: 100%;position: relative;border-radius: 1px;bottom: 3px;background-color: #396A92;"></div>
                                        </li>
                                    </div>
                                    <div class="column">
                                        <li id="VolunteerRecords"
                                            style="position: relative;display: block;list-style-type: none;background: 0 0;float: left;margin: 0 4px 0 0;padding: 0;font-weight: 700;height: 35px;font-size: 14px;">
                                            <a href="http://localhost/Codebase/documentRecords.php">Volunteer Records</a>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                            <div id="DocumentsListPage_DocumentsListContainer">
                                <div id="AlwaysAvailableDocuments_Instructions"
                                    style="font-size: 14px;color: #727272;margin-bottom: 16px;">These are available for
                                    use as needed or
                                    as directed.</div>
                                <div>
                                    <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Volunteer Letter.doc" download>Employee and Volunteer Policies<target="_self"/></a>                                           
                                    </div>
                                </div>
                                <div>
                                    <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/General Information.docx" download>General Information<target="_self"/></a>                                           
                                    </div>
                                </div>
                                <div>
                                    <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Volunteer Letter.doc" download>Volunteer Letter<target="_self"/></a>                                           
                                    </div>
                                </div>
                                <div>
                                    <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Training_Evaluation_of_Trainer-3.25.21.doc" download> Evaluation of Trainer<target="_self"/></a>                                           
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>