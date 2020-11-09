<?php

function minutes_to_human_time($minutes)
{
    $hours = (int) ($minutes / 60);
    $minutes = (int) ($minutes % 60);
    if ($hours > 0) {
        return $hours . "h" . $minutes . "m";
    } else {
        return $minutes . "m";
    }
}
