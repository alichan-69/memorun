<?php
require("function.php");

// セッションの破棄
session_destroy();

header("Location: index.php");
?>