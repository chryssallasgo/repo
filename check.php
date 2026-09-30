<?php
// Source - https://stackoverflow.com/a/670012
// Posted by karim79
// Retrieved 2026-09-29, License - CC BY-SA 2.5

if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
    echo 'We don\'t have mysqli!!!';
} else {
    echo 'Phew we have it!';
}
