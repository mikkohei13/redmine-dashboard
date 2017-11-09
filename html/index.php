<?php

// Get secrets from environment variables
$apikey = getenv("redmine-dashboard_api_key");
$redmineRoot = getenv("redmine-dashboard_redmine_root");

/*
$key = getenv("myissues_key");
if ($_GET['key'] != $key) {
    header("HTTP/1.0 401 Unauthorized");
    exit("401 Unauthorized");
}
*/

// Check that setup is ok and parameters have been set
if (empty($apikey) || empty($redmineRoot)) {
    echo "<h3>It works, sort of</h3>";
    echo "<p>Please set up your Redmine API key and root URL (e.g. https://myredmine.example.com) to the docker-compose.yml environment variables, and then restart docker-compose.";
    exit();
}
elseif (! isset($_GET['projectnumber']) || ! isset($_GET['users'])) {
    echo "<h3>It works!</h3>";
    echo "<p>Now add some get parameters to connect to a Redmine API:";
    echo "<pre>/?projectnumber=PROJECTNUMBER&users=USERNUMBER:NAME/USERNUMBER:NAME";
    exit();
}
$projectNumber = intval($_GET['projectnumber']);
$openTaskCount = 0;
$testableTaskCount = 0;


// Get GET parameters
$usersString = filter_input(INPUT_GET, "users", FILTER_SANITIZE_STRING);
$usersArr = explode("/", $usersString);
$usersToGet = Array();
foreach ($usersArr as $nro => $user) {
    $temp = explode(":", $user);
    $usersToGet[$temp[0]] = $temp[1];
}

$usersTasks = Array();

// Generate html for each user's tasks
foreach ($usersToGet as $userNumber => $userName) {
    $usersTasks[$userNumber] = getUserTasks($userNumber, $userName);
}

// Generate html for issues resolved for testing
if (isset($_GET['showtestable'])) {
    $usersTasks['testable'] = getTestableTasks();
}

renderTemplate($usersTasks, $openTaskCount, $testableTaskCount);

//-----------------------------------------------------------------------------

function renderTemplate($data, $openTaskCount, $testableTaskCount) {
    $undoneTaskCount = $openTaskCount - $testableTaskCount;
    require_once "template.php";
}

function getTestableTasks() {
    global $apikey;
    global $redmineRoot;
    global $projectNumber;
    global $testableTaskCount;
    
    $url = $redmineRoot . "/issues.json?project_id=" . $projectNumber . "&status_id=" . "7"; // todo: fix hardcoded status id
    $url .= "&key=" . $apikey;
    $dataArr = json_decode(file_get_contents($url), TRUE);
    
    $issueArr = Array();
    foreach ($dataArr['issues'] as $nro => $issue) {
            $issueHtml = "";
        
            $issueHtml .= "<h4>" . $issue['subject'] . "</h4>\n<p>";
            $issueHtml .= $issue['tracker']['name'] . " <a href='" . $redmineRoot . "/issues/" . $issue['id'] . "'>#" . $issue['id'] . "</a></p>";
        
            $issueArr[$issue['status']['name']][] = $issueHtml;
            $testableTaskCount++;
    }

    $html = "<div class='user' id='userNumberTestable'>";
    
    $html .= "<h2>Resolved for testing</h2>";
    $html .= getTasksUnderStatus($issueArr, "Resolved for testing"); // todo: parametrize name?
    $html .= "</div><!-- userWrapper ends -->";

    return $html;
}

// Get tasks of a single user
function getUserTasks($userNumber, $userName) {
    global $apikey;
    global $redmineRoot;
    global $projectNumber;

    // Get issues assigned to non-one: first get all issues, then filter out those that have been assigned to someone.
    if ("none" == $userNumber) {
        $url = $redmineRoot . "/issues.json?project_id=" . $projectNumber;
        $url .= "&key=" . $apikey;
        $dataArr = json_decode(file_get_contents($url), TRUE);

        // Filter out tasks that have been assigned to someone 
        foreach ($dataArr['issues'] as $nro => $issue) {
            if ($issue['assigned_to']) {
                unset($dataArr['issues'][$nro]);
            }
        }
    }
    // Get issues assigned to a certain person
    else {
        $url = $redmineRoot . "/issues.json?project_id=" . $projectNumber . "&assigned_to_id=" . $userNumber;
        $url .= "&key=" . $apikey;
        $dataArr = json_decode(file_get_contents($url), TRUE);
    }
    
//    echo "WHOLE ARRAY: \n\n ";print_r ($dataArr); exit("Debug end");
    
    // Set the issues as html to array, grouped by issue status
    // The data does not include information about the order of items in the agile plugin
    $issueArr = Array();
    foreach ($dataArr['issues'] as $nro => $issue) {
        // Skip issues with Tracker "detail"
        if (8 == $issue['tracker']['id']) {
            continue;
        }

//        print_r ($issue); exit(); // debug: show what Redmine returns from each issue
        $issueHtml = "";
    
        $issueHtml .= "<h4>" . $issue['subject'] . "</h4>\n<p>";
        $issueHtml .= $issue['tracker']['name'] . " <a href='" . $redmineRoot . "/issues/" . $issue['id'] . "'>#" . $issue['id'] . "</a></p>";
    
        $issueArr[$issue['status']['name']][] = $issueHtml;
    }
    
    $html = "<div class='user' id='userNumber$userNumber'>";    
    $html .= "<h2>$userName <!--($userNumber)--></h2>";    
//    $html .= getTasksUnderStatus($issueArr, "Resolved for testing");
    $html .= getTasksUnderStatus($issueArr, "In Progress");
    $html .= getTasksUnderStatus($issueArr, "Sprint Backlog");
    $html .= "</div><!-- userWrapper ends -->";
    
    return $html;    
}

// Print issues under a single status
function getTasksUnderStatus($issueArr, $statusName) {
    global $openTaskCount;    
    $html = "<h3>$statusName</h3>";
    if (! empty($issueArr[$statusName])) {
        foreach ($issueArr[$statusName] as $nro => $issueHtml) {
            $html .= $issueHtml;
            $openTaskCount++;
        }
    }
    return $html;
}

