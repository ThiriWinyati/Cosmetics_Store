<?php
session_start();
session_unset();
session_destroy();

header("Location: /Customer/user_login.php?logout=success");
exit();
?>