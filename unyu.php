<?php
$mode = 'mention';
require_once './base.php';
header('Content-Type: application/json; charset=utf-8');
echo makeJson($mode);
