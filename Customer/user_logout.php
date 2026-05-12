<?php
session_start();
session_destroy();
echo "You logged out of the system. Please Login again if you want to.";
header("Location: user_homeIndex.php");
exit();
?>