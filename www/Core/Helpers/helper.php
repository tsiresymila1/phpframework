<?php

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function env($key){
    return getenv($key);
}
