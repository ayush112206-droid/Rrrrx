<?php
require_once 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['userid'])||empty($_SESSION['userid'])){
    header('Location: login.php');
} else {
    header('Location: batches.php');
}
exit;
