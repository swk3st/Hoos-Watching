<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

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
