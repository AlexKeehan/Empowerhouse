<?php
    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");

    // Ensure user is logged in
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        die();
    }

    $filter = -1;
    if(isset($_GET["event-filter"])){
        $filter = $_GET["event-filter"];
    }
    // Redirect to month or first month that event is in or current month if
    // the other two aren't set
    require_once('database/dbCourses.php');
    if (isset($_GET['month'])) {
        $month = $_GET['month'];
    } else if(isset($_GET['event-filter'])){
        $month = find_first_course_month($filter); 
    } else {
        $month = date("Y-m");
    }
    $year = substr($month, 0, 4);
    $month2digit = substr($month, 5, 2);

    $today = strtotime(date("Y-m-d"));

    $first = $month . '-01';
    $cleanMonth=$month;
    // Convert to date
    $month = strtotime($month);
    // Find first day of the month
    $first = strtotime($first);
    // Find previous and next month
    $previousMonth = strtotime(date('Y-m', $month) . ' -1 month');
    $nextMonth = strtotime(date('Y-m', $month) . ' +1 month');
    // Validate; redirect if bad arg given
    if (!$month) {
        header('Location: calendar.php?month=' . date("Y-m"));
        die();
    }
    $calendarStart = $first;
    // Back up until we find the first Sunday that should appear on the calendar
    while (date('w', $calendarStart) > 0) {
        $calendarStart = strtotime(date('Y-m-d', $calendarStart) . ' -1 day');
    }
    $calendarEnd = date('Y-m-d', strtotime(date('Y-m-d', $calendarStart) . ' +34 day'));
    $calendarEndEpoch = strtotime($calendarEnd);
    $weeks = 5;
    // Add another row if it's needed to display all days in the month
    if (date('m', strtotime($calendarEnd . ' +1 day')) == date('m', $first)) {
        $calendarEnd = date('Y-m-d', strtotime($calendarEnd . ' +7 day'));
        $calendarEndEpoch = strtotime($calendarEnd);
        $weeks = 6;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require('universal.inc'); ?>
        <script src="js/calendar.js"></script>
        <title>Empowerhouse VMS | Events Calendar</title>
        <style>.happy-toast { margin: 0 1rem 1rem 1rem; }</style>
    </head>
    <body>
        <div id="month-jumper-wrapper" class="hidden">
            <form id="month-jumper">
                <p>Choose a month to jump to</p>
                <div>
                    <select id="jumper-month">
                        <?php
                            $months = [
                                'January', 'February', 'March', 'April',
                                'May', 'June', 'July', 'August',
                                'September', 'October', 'November', 'December'
                            ];
                            $digit = 1;
                            foreach ($months as $m) {
                                $month_digits = str_pad($digit, 2, '0', STR_PAD_LEFT);
                                if ($month_digits == $month2digit) {
                                    echo "<option value='$month_digits' selected>$m</option>";
                                } else {
                                    echo "<option value='$month_digits'>$m</option>";
                                }
                                $digit++;
                            }
                        ?>
                    </select>
                    <input id="jumper-year" type="number" value="<?php echo $year ?>" required min="2023">
                </div>
                <input type="hidden" id="jumper-value" name="month" value="<?php echo 'test' ?>">
                <input type="hidden" id="jumper-filter" name="event-filter" value= <?php echo $filter ?> >
                <input type="submit" value="View">
                <button id="jumper-cancel" class="cancel" type="button">Cancel</button>
            </form>
        </div>
        <?php require('header.php'); ?>
        <main class="calendar-view">
            <h1 class='calendar-header'>
            <img id="previous-month-button" src="images/arrow-back.png" data-month="<?php echo date("Y-m", $previousMonth); ?>" data-event-filter="<?php echo $filter; ?>">
                <span id="calendar-heading-month">Events - <?php echo date('F Y', $month); ?></span>
                <img id="next-month-button" src="images/arrow-forward.png" data-month="<?php echo date("Y-m", $nextMonth); ?>" data-event-filter="<?php echo $filter; ?>">
            </h1>
            <!-- <input type="date" id="month-jumper" value="<?php echo date('Y-m-d', $month); ?>" min="2023-01-01"> -->
            <?php if (isset($_GET['deleteSuccess'])) : ?>
                <div class="happy-toast">Event deleted successfully.</div>
            <?php endif ?>
            <?php if (isset($_GET['createSuccess'])): ?>
                <div class="happy-toast">Event created successfully!</div>
            <?php endif ?>
            <?php if (isset($_GET['editSuccess'])): ?>
                <div class="happy-toast">Event details updated successfully!</div>
            <?php endif ?>
            <form id="event-filter" method="get">
                <label for="event-list">Filter by Event </label>
                    <select id=event-filter name=event-filter onchange="submitForm()">
                        <option value= "-1">All Events</option> 
                        <?php
                            require_once('database/dbEvents.php');
                            $events_list = get_all_events();
                            foreach ($events_list as $e) {
                              if($e['id'] == $filter){
                                echo "<option value='$e[id]', selected>$e[eventname]</option>";
                              }else{
                                echo "<option value='$e[id]'>$e[eventname]</option>";
                              }
                            }
                        ?>
                    </select>
            </form>
            <script>
                function submitForm() {
                // Get the form element by its ID
                var myForm = document.getElementById("event-filter");
                // Use the submit() method to automatically submit the form
                myForm.submit();
            }
            </script>
        </div>
            <div class="table-wrapper">
                <table id="calendar">
                    <thead>
                        <tr>
                            <th>Sunday</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $date = $calendarStart;
                        $start = date('Y-m-d', $calendarStart);
                        $end = date('Y-m-d', $calendarEndEpoch);
                        $events = fetch_courses_in_date_range($start, $end, $filter);
                        for ($week = 0; $week < $weeks; $week++) {
                            echo '
                                <tr class="calendar-week">
                            ';
                            for ($day = 0; $day < 7; $day++) {
                                $extraAttributes = '';
                                $extraClasses = '';
                                if ($date == $today) {
                                    $extraClasses = ' today';
                                }
                                if (date('m', $date) != date('m', $month)) {
                                    $extraClasses .= ' other-month';
                                    $extraAttributes .= ' data-month="' . date('Y-m', $date) . '"';
                                }
                                $eventsStr = '';
                                $e = date('Y-m-d', $date);
                                if (isset($events[$e])) {
                                    $dayEvents = $events[$e];
                                    foreach ($dayEvents as $info) {
                                        $eventsStr .= '<a class="calendar-event" href="event.php?id=' . $info['id'] . '">' . $info['abbrevName'] .  '</a>';
                                    }
                                }
                                echo '<td class="calendar-day' . $extraClasses . '" ' . $extraAttributes . ' data-date="' . date('Y-m-d', $date) . '">
                                    <div class="calendar-day-wrapper">
                                        <p class="calendar-day-number">' . date('j', $date) . '</p>
                                        ' . $eventsStr . '
                                    </div>
                                </td>';
                                $date = strtotime(date('Y-m-d', $date) . ' +1 day');
                            }
                            echo '
                                </tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <div id="calendar-footer">
                <a class="button cancel" href="index.php">Return to Dashboard</a>
            </div>
        </main>
    </body>
</html>
