<?php
function formatDateRange(
    $startStr,
    $endStr = null,
    $separator = "–" // U+2013 "–" is used, and not the ASCII character U+002d "-"
) {
    $start = new DateTime($startStr);
    $end = $endStr ? new DateTime($endStr) : null;


    if (!$end) return $start->format('F j, Y') . " {$separator} ??";

    if ($start->format('Y') === $end->format('Y')) {
        if ($start->format('F') === $end->format('F')) {
            return $start->format('F j') . "{$separator}" . $end->format('j, Y');
        } else {
            return $start->format('F j') . " {$separator} " . $end->format('F j, Y');
        }
    }

    return $start->format('F j, Y') . " {$separator} " . $end->format('F j, Y');
}
