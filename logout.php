<?php

// Logout
session_start();
session_unset();
session_destroy();

?>

<script>
localStorage.removeItem('username');
window.location.href = "index.php";
</script>
