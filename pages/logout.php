<?php
session_start();
require_once('../backend/page_controller.php');

PageController::setCanAccess(true, 'login.php');
PageController::setCanAccess(true, 'signup.php');

PageController::setCanAccess(false, 'welcome.php');
PageController::setCanAccess(false, 'rankings.php');
PageController::setCanAccess(false, 'forum.php');
PageController::setCanAccess(false, 'adminWelcome.php');
PageController::setCanAccess(false, 'adminRankings.php');
PageController::setCanAccess(false, 'toAdminWelcome.php');
PageController::setCanAccess(false, 'toAdminRankings.php');

header('Location: login.php');
exit();
