<?php
$times = [
    '12:00 AM', '1:00 AM', '2:00 AM', '3:00 AM', '4:00 AM', '5:00 AM',
    '6:00 AM', '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
    '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
    '6:00 PM', '7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM',
    '11:59 PM'
];
$values = [
    "00:00", "01:00", "02:00", "03:00", "04:00", "05:00", 
    "06:00", "07:00", "08:00", "09:00", "10:00", "11:00", 
    "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", 
    "18:00", "19:00", "20:00", "21:00", "22:00", "23:00",
    "23:59"
];

function buildSelect($name, $disabled=false, $selected=null) {
    global $times;
    global $values;
    if ($disabled) {
        $select = '
            <select id="' . $name . '" name="' . $name . '" disabled>';
    } else {
        $select = '
            <select id="' . $name . '" name="' . $name . '">';
    }
    if (!$selected) {
        $select .= '<option disabled selected value>Select a time</option>';
    }
    $n = count($times);
    for ($i = 0; $i < $n; $i++) {
        $value = $values[$i];
        if ($selected == $value) {
            $select .= '
                <option value="' . $values[$i] . '" selected>' . $times[$i] . '</option>';
        } else {
            $select .= '
                <option value="' . $values[$i] . '">' . $times[$i] . '</option>';
        }
    }
    $select .= '</select>';
    return $select;
}
?>

<h1>New Volunteer Registration</h1>
<main class="signup-form">
    <form class="signup-form" method="post">
        <h2>Registration Form</h2>
        <p>Please fill out each section of the following form if you would like to volunteer for the organization.</p>
        <p>An asterisk (<label><em>*</em></label>) indicates a required field.</p>
        <fieldset>
            <legend>Personal Information</legend>
            <p>The following information will help us identify you within our system.</p>
            <label for="first-name"><em>* </em>First Name</label>
            <input type="text" id="first-name" name="first-name" required placeholder="Enter your first name">

            <label for="last-name"><em>* </em>Last Name</label>
            <input type="text" id="last-name" name="last-name" required placeholder="Enter your last name">

            <label for="gender"><em>* </em>Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Choose an option</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="birthdate"><em>* </em>Date of Birth</label>
            <input type="date" id="birthdate" name="birthdate" required placeholder="Choose your birthday" max="<?php echo date('Y-m-d'); ?>">


            <label for="address"><em>* </em>Street Address</label>
            <input type="text" id="address" name="address" required placeholder="Enter your street address">

            <label for="city"><em>* </em>City</label>
            <input type="text" id="city" name="city" required placeholder="Enter your city">

            <label for="state"><em>* </em>State</label>
            
            <select id="state" name="state" required>
                <option value="AL">Alabama</option>
                <option value="AK">Alaska</option>
                <option value="AZ">Arizona</option>
                <option value="AR">Arkansas</option>
                <option value="CA">California</option>
                <option value="CO">Colorado</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="DC">District Of Columbia</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="HI">Hawaii</option>
                <option value="ID">Idaho</option>
                <option value="IL">Illinois</option>
                <option value="IN">Indiana</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NV">Nevada</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NM">New Mexico</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="ND">North Dakota</option>
                <option value="OH">Ohio</option>
                <option value="OK">Oklahoma</option>
                <option value="OR">Oregon</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="SD">South Dakota</option>
                <option value="TN">Tennessee</option>
                <option value="TX">Texas</option>
                <option value="UT">Utah</option>
                <option value="VT">Vermont</option>
                <option value="VA" selected>Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>
            </select>

            <label for="zip"><em>* </em>Zip Code</label>
            <input type="text" id="zip" name="zip" pattern="[0-9]{5}" title="5-digit zip code" required placeholder="Enter your 5-digit zip code">
            
            <!-- Wade - I added this field -->

            <label><em>* </em>User Role</label>
            <div class="radio-group">
                <input type="radio" id="Volunteer" name="role-type" value="Volunteer" required><label for="Volunteer">Volunteer</label>
                <input type="radio" id="Admin" name="role-type" value="Admin" required><label for="Admin">Admin</label>
                <input type="radio" id="Trainer" name="role-type" value="Trainer" required><label for="Trainer">Trainer</label>
            </div>
          
            <!-- Nahom I added this section response from the orginal form we were given-->
            <label for="How did you hear of our volunteer program?"><em>*</em>How did you hear of our volunteer program?</label>
            <input type="text" id="How did you hear of our volunteer program?" name="How did you hear of our volunteer program?" required placeholder="Enter how you hear of us">
            
            <label for="convictions"><em>*</em>Have you ever been convicted of a law violation(s), including moving traffic violations but excluding violations before your 18th birthday?</label>
            <br>
            <div>
                <label for="convictions-yes">
                    <input type="checkbox" id="convictions-yes" name="convictions" value="Yes">
                    Yes
                </label>
                <label for="convictions=no">
                    <input type="checkbox" id="convictions-no"name="convictions"value="No">
                    No
                </label>
            </div>
            <br>
            <label for="convictionList">If yes, list all and explain:</label>
            <br>
            <textarea id="convictionList" name="convictionList" rows="4" cols="50" required disabled></textarea>
            <br>
            </div> <!-- not sure if this div element does anything-->
            <!-- script tests that the field is only required if they hit yes-->
            <script>
                document.getElementById("convictions-yes").addEventListener("click", function () {
                document.getElementById("convictionList").required = true;
                document.getElementById("convictionList").removeAttribute("disabled");
                });
                document.getElementById("convictions-no").addEventListener("click", function () {
                document.getElementById("convictionList").required = false;
                document.getElementById("convictionList").setAttribute("disabled", "disabled");
                });
            </script>
            <!--change ends here for the personal information portion-->
      
        </fieldset>
        <fieldset>
            <legend>Contact Information</legend>
            <p>The following information will help us determine the best way to contact you regarding event coordination.</p>
            <label for="email"><em>* </em>E-mail</label>
            <p>This will also serve as your username when logging in.</p>
            <input type="email" id="email" name="email" required placeholder="Enter your e-mail address">

            <label for="phone"><em>* </em>Phone Number</label>
            <input type="tel" id="phone" name="phone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Ex. (555) 555-5555">

            <label><em>* </em>Phone Type</label>
            <div class="radio-group">
                <input type="radio" id="phone-type-cellphone" name="phone-type" value="cellphone" required><label for="phone-type-cellphone">Cell</label>
                <input type="radio" id="phone-type-home" name="phone-type" value="home" required><label for="phone-type-home">Home</label>
                <input type="radio" id="phone-type-work" name="phone-type" value="work" required><label for="phone-type-work">Work</label>
            </div>

            <label for="contact-when" required><em>* </em>Best Time to Reach You</label>
            <input type="text" id="contact-when" name="contact-when" required placeholder="Ex. Evenings, Days">

            <label><em>* </em>Preferred Contact Method</label>
            <div class="radio-group">
                <input type="radio" id="method-phone" name="contact-method" value="phone" required><label for="method-phone">Phone call</label>
                <input type="radio" id="method-text" name="contact-method" value="text" required><label for="method-text">Text</label>
                <input type="radio" id="method-email" name="contact-method" value="email" required><label for="method-email">E-mail</label>
            </div>
        </fieldset>
        <fieldset>
            <legend>Emergency Contact</legend>
            <p>Please provide us with someone to contact on your behalf in case of an emergency.</p>
            <label for="econtact-name" required><em>* </em>Contact Name</label>
            <input type="text" id="econtact-name" name="econtact-name" required placeholder="Enter emergency contact name">

            <label for="econtact-phone"><em>* </em>Contact Phone Number</label>
            <input type="tel" id="econtact-phone" name="econtact-phone" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" required placeholder="Enter emergency contact phone number. Ex. (555) 555-5555">

            <label for="econtact-name"><em>* </em>Contact Relation to You</label>
            <input type="text" id="econtact-relation" name="econtact-relation" required placeholder="Ex. Spouse, Mother, Father, Sister, Brother, Friend">
        </fieldset>
        <!-- start of Nahom code I added new legend for education section and necessary elements-->
        <fieldset>
            <legend>Education Level</legend>
            <p>Please provide clarification on your educational background.</p>
            <label for="level"required><em>*</em>Level</label>
            <input type="text" id="level" name="level" required placeholder="Enter highest educational level">
            <label for="degree"required><em>*</em>Degree(s)</label>
            <input type="text" id="degree" name="degree" required placeholder="Enter all degrees you have recieved">
            <label for="hobby"required><em>*</em>Hobbies</label>
            <input type="text" id="hobby" name="hobby"required placeholder="Enter any hobbies">
        </fieldset>
        <fieldset>
            <legend>Volunteer Information</legend>
            <p>The following information will be used to help us determine your availability and skillset.</p>
            <label for="start-date"><em>* </em>I will be available to volunteer starting</label>
            <input type="date" id="start-date" name="start-date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
            <label><em>* </em>Availability</label>
            <p>Enter the days and times you will be available to volunteer each week, starting from the date above.</p>
            <div class="availability-container">
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-sundays" name="available-sundays" type="checkbox" required>
                        <label for="available-sundays">Sundays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <!-- <input type="text" id="sundays-start" name="sundays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <?php echo buildSelect('sundays-start', true) ?>
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('sundays-end', true) ?>
                    <!-- <input type="text" id="sundays-end" name="sundays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="sundays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-mondays" name="available-mondays" type="checkbox" required>
                        <label for="available-mondays">Mondays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <?php echo buildSelect('mondays-start', true) ?>
                    <!-- <input type="text" id="mondays-start" name="mondays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('mondays-end', true) ?>
                    <!-- <input type="text" id="mondays-end" name="mondays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="mondays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-tuesdays" name="available-tuesdays" type="checkbox" required>
                        <label for="available-tuesdays">Tuesdays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <?php echo buildSelect('tuesdays-start', true) ?>
                    <!-- <input type="text" id="tuesdays-start" name="tuesdays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('tuesdays-end', true) ?>
                    <!-- <input type="text" id="tuesdays-end" name="tuesdays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="tuesdays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-wednesdays" name="available-wednesdays" type="checkbox" required>
                        <label for="available-wednesdays">Wednesdays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <?php echo buildSelect('wednesdays-start', true) ?>
                    <!-- <input type="text" id="wednesdays-start" name="wednesdays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('wednesdays-end', true) ?>
                    <!-- <input type="text" id="wednesdays-end" name="wednesdays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="wednesdays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-thursdays" name="available-thursdays" type="checkbox" required>
                        <label for="available-thursdays">Thursdays</label>
                    </p>
                    <p>From</p>
                    <?php echo buildSelect('thursdays-start', true) ?>
                    <!-- <input type="text" id="thursdays-start" name="thursdays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p>to</p>
                    <?php echo buildSelect('thursdays-end', true) ?>
                    <!-- <input type="text" id="thursdays-end" name="thursdays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="thursdays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-fridays" name="available-fridays" type="checkbox" required>
                        <label for="available-fridays">Fridays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <?php echo buildSelect('fridays-start', true) ?>
                    <!-- <input type="text" id="fridays-start" name="fridays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('fridays-end', true) ?>
                    <!-- <input type="text" id="fridays-end" name="fridays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="fridays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
                <div class="availability-day">
                    <p class="availability-day-header">
                        <input id="available-saturdays" name="available-saturdays" type="checkbox" required>
                        <label for="available-saturdays">Saturdays</label>
                    </p>
                    <p><em class="hidden">* </em>From</p>
                    <?php echo buildSelect('saturdays-start', true) ?>
                    <!-- <input type="text" id="saturdays-start" name="saturdays-start" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 8:00AM" disabled> -->
                    <p><em class="hidden">* </em>to</p>
                    <?php echo buildSelect('saturdays-end', true) ?>
                    <!-- <input type="text" id="saturdays-end" name="saturdays-end" pattern="([1-9]|10|11|12):[0-5][0-9]([aApP][mM])" placeholder="Ex. 4:00PM" disabled> -->
                    <p id="saturdays-range-error" class="hidden error">Start time must come before end time.</p>
                </div>
            </div>

            <label for="skills">Skills</label>
            <textarea id="skills" name="skills" placeholder="Please let us know about any special skills you may have, including proficiencies in non-English languages."></textarea>

            <label>Additional Information</label>
            <div class="checkbox-grouping">
                <div class="checkbox-pair">
                    <input id="has-computer" name="has-computer" type="checkbox">
                    <label for="has-computer">I own a computer</label>
                </div>
                <div class="checkbox-pair">
                    <input id="has-camera" name="has-camera" type="checkbox">
                    <label for="has-camera">I own a camera</label>
                </div>
                <div class="checkbox-pair">
                    <input id="has-transportation" name="has-transportation" type="checkbox">
                    <label for="has-transportation">I have access to reliable transportation</label>
                </div>
            </div>

            <label for="shirt-size"><em>* </em>T-shirt Size</label>
            <select id="shirt-size" name="shirt-size" required>
                <option value="S">Small</option>
                <option value="M">Medium</option>
                <option value="L">Large</option>
                <option value="XL">Extra Large</option>
                <option value="XXL">2X Large</option>
            </select>
        </fieldset>
        <fieldset>
            <!--stuff is separated into multiple divs because I wanted a line break at certain points-->
            <legend>Volunteer opportunities/area of interest</legend>
            <label for="opportunties"><em>*</em>Potential interests</label>
            <br>
            <div>
                <label for="Hotline">
                    <input type="checkbox" id="Hotline" name="Hotline" value="Yes">
                    Hotline
                </label>
                <label for="shelter">
                    <input type="checkbox" id="shelter" name="Hotline" value="Yes">
                    Shelter
                </label>
                <label for="court ad">
                    <input type="checkbox" id="court ad" name="court ad" value="Yes">
                    Court Advocacy 
                </label>
            </div>
            <div>
                <label for="Support groups a">
                    <input type="checkbox" id="Support groups a" name="Support groups a" value="Yes">
                    Support Groups for Adults
                </label>
                <label for="Support groups b">
                    <input type="checkbox" id="Support groups b" name="Support groups b" value="Yes">
                    Support Groups for Children
                </label>
            </div>
            <div>
                <label for="Batterer's Intervention A">
                    <input type="checkbox" id="Batterer's Intervention A" name="Batterer's Intervention A" value="Yes">
                    Batterer's Intervention for Women
                </label>
                <label for="Batterer's Intervention B">
                    <input type="checkbox" id="Batterer's Intervention A" name="Batterer's Intervention A" value="Yes">
                    Batterer's Intervention for Men
                </label>
            </div>
            <div>
                <label for="clerical">
                    <input type="checkbox" id="clerical" name="clerical" value="Yes">
                    Group Maintenance 
                </label>
                <label for="mail">
                    <input type="checkbox" id="mail" name="mail" value="Yes">
                    Major Mailings	
                </label>
                <label for="funding">
                    <input type="checkbox" id="clerical" name="funding" value="Yes">
                    Fund Raising/Special Events
            </div>
        </fieldset>
        <fieldset>
            <!--Nahom code added cerfication legend and required elements-->
            <legend>Certification</legend>
            <p>I certify that the above statements are true to the best of my knowledge.</p>
            <label for="signature"><em>* </em>Signature</label>
            <canvas id="signatureCanvas" width="300" height="100"></canvas>
            <br>
            <button id="clearSignature">Clear Signature</button>
        </fieldset>
        <!-- this contains the logic that allows for writing the signature-->
        <script>
            // Get a reference to the canvas element and its 2D drawing context
            const canvas = document.getElementById("signatureCanvas");
            const context = canvas.getContext("2d");
            // A flag to indicate whether the user is currently drawing
            let isDrawing = false;
            // Event listener for when the user presses the mouse button or touches the canvas    
            canvas.addEventListener("mousedown", () => {
            // Set the drawing flag to true    
            isDrawing = true;
            // Begin a new drawing path
            context.beginPath();
            // Move the drawing point to the current cursor position
            context.moveTo(event.clientX - canvas.getBoundingClientRect().left, event.clientY - canvas.getBoundingClientRect().top);
            });
            // Event listener for when the user moves the mouse or finger on the canvas
            canvas.addEventListener("mousemove", () => {
            // Check if the user is actively drawing
            if (isDrawing) {
                // Draw a line from the previous point to the current cursor position
                context.lineTo(event.clientX - canvas.getBoundingClientRect().left, event.clientY - canvas.getBoundingClientRect().top);
                context.stroke();//display the line
            }
            });
            //Event listener for when the user releases the mouse
            canvas.addEventListener("mouseup", () => {
            //set drawing flag to false to indicate that signature is done
            isDrawing = false;
            });
            // Event listener for when the user releases the mouse
            canvas.addEventListener("mouseup", () => {
            isDrawing = false;

            // Get the data URL representation of the canvas content
            const signatureDataURL = canvas.toDataURL();

            // Log the dataURL to the console (for demonstration purposes)
            console.log(signatureDataURL);

            // Here you can send the dataURL to your server or store it in your database
            // For demonstration, the code to send or store the dataURL is not included.
            });
            //Event listener for clear signature button
            document.getElementById("clearSignature").addEventListener("click", () => {
            //clears the canvas so user can redo signature
            context.clearRect(0, 0, canvas.width, canvas.height);
            });
        </script>
        <fieldset>
            <legend>Login Credentials</legend>
            <p>You will use the following information to log in to the VMS.</p>

            <label for="email-relisting">E-mail Address</label>
            <span id="email-dupe" class="pseudo-input">Enter your e-mail address above</span>

            <label for="password"><em>* </em>Password</label>
            <input type="password" id="password" name="password" placeholder="Enter a strong password" required>

            <label for="password-reenter"><em>* </em>Re-enter Password</label>
            <input type="password" id="password-reenter" name="password-reenter" placeholder="Re-enter password" required>
            <p id="password-match-error" class="error hidden">Passwords do not match!</p>
        </fieldset>
        <p>By pressing Submit below, you are agreeing to volunteer for the organization.</p>
        <input type="submit" name="registration-form" value="Submit">
    </form>
    <?php if ($loggedIn): ?>
        <a class="button cancel" href="index.php" style="margin-top: .5rem">Cancel</a>
    <?php endif ?>
</main>
