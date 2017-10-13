<?php
/*LOCAL CHANGE*/

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

// Get GET parameters
$usersString = filter_input(INPUT_GET, "users", FILTER_SANITIZE_STRING);
$usersArr = explode("/", $usersString);
$usersToGet = Array();
foreach ($usersArr as $nro => $user) {
    $temp = explode(":", $user);
    $usersToGet[$temp[0]] = $temp[1];
}
//print_r ($usersToGet); exit("debug ends");

$usersTasks = Array();

foreach ($usersToGet as $userNumber => $userName) {
    $usersTasks[$userNumber] = getUserTasks($userNumber, $userName);
}

renderTemplate($usersTasks);

//-----------------------------------------------------------------------------

function renderTemplate($data) {
    require_once "template.php";
}

// Get user's tasks
function getUserTasks($userNumber, $userName) {
    global $apikey;
    global $redmineRoot;
    global $projectNumber;

    $url = $redmineRoot . "/issues.json?project_id=" . $projectNumber . "&assigned_to_id=" . $userNumber;
    $url .= "&key=" . $apikey;
    $dataArr = json_decode(file_get_contents($url), TRUE);
    
    //print_r ($dataArr); exit("Debug end");
    
    // Set the issues as html to array, grouped by issue status
    // The data does not include information about the order of items in the agile plugin
    $issueArr = Array();
    foreach ($dataArr['issues'] as $nro => $issue) {
        $issueHtml = "";
    
        $issueHtml .= "<h4>" . $issue['subject'] . "</h4>\n<p>";
        $issueHtml .= $issue['tracker']['name'] . " <a href='" . $redmineRoot . "/issues/" . $issue['id'] . "'>#" . $issue['id'] . "</a></p>";
    
        $issueArr[$issue['status']['name']][] = $issueHtml;
    
    }
    
    //print_r ($issueArr); exit();
    
    $html = "<h2>$userName <!--($userNumber)--></h2>";
    
    $html .= getTasksUnderStatus($issueArr, "In Progress");
    $html .= getTasksUnderStatus($issueArr, "Backlog");
    
    //echo "<pre>"; print_r ($dataArr); exit("\nDEBUG END");
    return $html;    
}

// Print issues under a single status
function getTasksUnderStatus($issueArr, $statusName) {
    $html = "<h3>$statusName</h3>";
    if (! empty($issueArr[$statusName])) {
//        print_r ($issueArr[$statusName]);
        foreach ($issueArr[$statusName] as $nro => $issueHtml) {
            $html .= $issueHtml;
        }
    }
    return $html;
}

