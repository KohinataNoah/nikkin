<?php

$siteTtl = 'ログアウト';
require('parts/function.php');

session_destroy();

header("Location:index.php");
