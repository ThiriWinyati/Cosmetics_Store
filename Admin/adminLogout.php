<?php
session_start();
session_unset();
session_destroy();

header("Location: /Admin/adminLogin.php?logout=success");
exit();
?>