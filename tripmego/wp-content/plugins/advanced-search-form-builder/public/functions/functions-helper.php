<?php

function getClassByFileName($path) {
    $path = explode('/', $path);
    $path = end($path);
    $path = explode('.', $path);
    return $path[0];
}