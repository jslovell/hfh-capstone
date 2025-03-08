<?php

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

debug_to_console('File Begining.');

//$target_file = "../uploads/layouts/" . "img-1834-heic-trowel-monkeys-img_ef41fcf90f9058f2_4-2677-1-39fc952.heic";
$target_file = '../uploads/layouts/' . '24633-1-414.jpg';
echo "Is heic? ";
try {
    debug_to_console('Breakpoint 1.');
    //If the exception is thrown, this text will not be shown
    if (Maestroerror\HeicToJpg::isHeic($target_file))
    {
        debug_to_console('Breakpoint 2.');
        echo "True";
        debug_to_console('Image is HEIC.');
    }
    else {
        debug_to_console('Breakpoint 3.');
        echo "False";
        debug_to_console('Image not HEIC.');
    }
    debug_to_console('Breakpoint 4.');
}

//catch exception
catch(Exception $e) {
    echo 'Message: '.$e->getMessage();
    debug_to_console("Message: ".$e->getMessage());
}

debug_to_console('File End.');

?>
