<?php

header('Content-Type: application/pdf');

$filename = pathinfo($_REQUEST['filename'], PATHINFO_FILENAME);
$filename = preg_replace('/[^a-zA-Z0-9_]+/', '', $filename) . ".pdf";

header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile("pdftmp/" . $filename);
