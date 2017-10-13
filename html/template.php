<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>★ Issues by user</title>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <link rel="stylesheet" href="style.css?_=<?php echo rand(0,100000); ?>">
    </head>
    <body>
        <h1>★ Backlog &amp; In Progress -issues by assigned user</h1>
        <p id="note">Note that: <br>
        - Order of tasks under each status is random, since Redmine doesn't inherently support ordering.<br>
        - Issues involving multiple people are under one person only, since Redmine dosn't support multiple assignees.
        </p>

        <?php

        foreach ($data as $userNumber => $html) {
            echo $html;
        }

        ?>
    </body>
</html>
