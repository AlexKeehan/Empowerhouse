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

    <!-- Adding code to line below for adding jquery library for better event handling like submit action -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Inclusion of DocuSign JavaScript library below -->
    <script src="https://js-d.docusign.com/bundle.js"></script>


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
                                    
                                                                        
                                    <!-- document links added below -->
                                    <li class="column">
                                    <a href="#" class="document-link" data-document-id="1" data-document-name="Criminal Background Check">Criminal Background Check</a>
                                    </li>

                                    <li class="column">
                                    <a href="#" class="document-link" data-document-id="2" data-document-name="Acknowledgement of Receipt">Acknowledgement of Receipt</a>
                                    </li>


                                    <li class="column">
                                    <a href="#" class="document-link" data-document-id="3" data-document-name="Volunteer Application">Volunteer Application</a>
                                    </li>



                                    <!-- Container for signature forms -->
                                    <div id="signature-forms-container"></div> 

                                    <!-- my edits done -->


                                    <div>
                                        <li id="DocumentsListFilesTab"
                                            style="position: relative;display: block;list-style-type: none;background: 0 0;float: left;margin: 0 4px 0 0;padding: 0;font-weight: 700;height: 35px;font-size: 14px;">
                                            <a href="documents.php">
                                                <span>Paperwork &amp; Forms</span>
                                            </a>
                                        </li>
                                    </div>
                                    <div>
                                        <li id="VolunteerRecords"
                                            style="position: relative;display: block;list-style-type: none;background: 0 0;float: left;margin: 0 4px 0 0;padding: 0;font-weight: 700;height: 35px;font-size: 14px;">
                                            <a href="documentRecords.php">Volunteer
                                                Records</a>
                                            <div id="UnderlineBox"
                                                style="height:2px;width: 100%;position: relative;border-radius: 1px;bottom: 3px;background-color: #396A92;">
                                            </div>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                            <div id="DocumentsListPage_DocumentsListContainer">
                                <div style="color:#FFFFFF;">Quick Spacer</div>
                                <div>
                                    <div>
                                        <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Volunteer Reference Form 5.17.18.doc" download>Reference Document 5.17.18<target="_self"/a>                                           
                                        </div>
                                        </div>
                                    <div>
                                    <div>
                                        <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Volunteer Application _ Confidentiality Committment.doc" download>Confidentiality Commitment<target="_self"/a>                                           
                                        </div>
                                        </div>
                                    <div>
                                    <div>
                                        <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/CB Check Info.docx" download>Criminal Background Check<target="_self"/a>                                           
                                        </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Acknowledgement of Receipt--Personnel Policies.docx" download>Acknowledgement of Receipt<target="_self"/a>                                           
                                        </div>
                                        </div> 
                                    </div>
                                    <div>
                                        <div id="AlwaysAvaibleDocumentListItem_null" class="AlwaysAvailableDocuments" style="margin-bottom: 12px;text-decoration: none;border-radius: 4px;background-color: #eee;padding: 12px;">
                                        <a href="Documents/Volunteer Application _ Confidentiality Committment.doc" download>Volunteer Application<target="_self"/a>                                       
                                        </div>
                                        </div>
                                    </div>
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

<!-- JavaScript to handle the signature submission aspect-->
<script>
    $(document).ready(function() {
        // Add signature forms dynamically based on document links
        $('.document-link').click(function (e) {
            e.preventDefault(); // Prevents default link behavior
            var documentId = $(this).data('document-id');
            var documentName = $(this).data('document-name');
            var formHtml = '<form class="document-sign-form" data-document-id="' + documentId + '">';
            formHtml += '<label for="signature-' + documentId + '">Your E-Signature for ' + documentName + ':</label>';
            formHtml += '<input type="text" id="signature-' + documentId + '" name="signature" required>';
            formHtml += '<button type="button" class="sign-button">Sign</button>';
            formHtml += '</form>';
            $('#signature-forms-container').html(formHtml); // Replace existing form with new form
        });


        //Handles the signature submission
        $(document).on('click', '.sign-button', function () {
            var form = $(this).closest('.document-sign-form');
            var documentId = form.data('document-id');
            var documentName = $('.document-link[data-document-id="' + documentId + '"]').data('document-name');
            var signature = form.find('input[name="signature"]').val();

            console.log('Signing document:', documentName);


            //Sending signature to DocuSign
            console.log('Signature sent to DocuSign:', signature);

            // Adds the verificatiom, the check mark that the document was signed
            var documentLink = $('.document-link[data-document-id="' + documentId + '"]');
            // Following line is making sure the message does not already exist
            if (documentLink.siblings('.sign-success-message').length === 0) { 
                documentLink.after('<span class="sign-success-message" style="color:green; margin-left:5px;">Signed ✔</span>');
                setTimeout(function() {
                    documentLink.siblings('.sign-success-message').fadeOut('slow', function() {
                        $(this).remove(); // Remove the message after fading out
                    });
                }, 3000); // Message lasts 3 seconds, then disappears
            }

            // To clear the signature input after it has been signed and sent
            form.find('input[name="signature"]').val('');
            
            
            // Send signature data to DocuSign
            DocuSign.signDocument({
                apiKey: 'e0edfd40-a150-431d-9784-fc37e913314f',
                accountId: '4d012b83-b8d9-4f4d-905f-c9c59721aa50',
                username: 'tnoor049@gmail.com',
                password: '*VdH,2!@ct.sgAt',
                documentId: documentId,
                documentName: documentName,
                signature: signature,
                callback: function (response) {
                    // Handle DocuSign response here (shows the success message)
                    alert('Document signed successfully!');
                    // Clear the signature input after submission
                    form.find('input[name="signature"]').val('');
                }
            });
        });
    });
    
    
    </script>
</body>

</html>