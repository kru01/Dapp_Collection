<?php

/**
 * Make a link with button appearance with a js onclick function
 *  to navigate the page.
 *
 * @param string $label Text content of the link.
 * @param string $js_loadpage_func Name of the js function to handle the load page event.
 *  E.g., handleForm.loadPage = (page) => { ... }.
 * @param int $page_value Value of the page to navigate to.
 *
 * @return string Html of link with button appearance.
 */
function link_button($label, $onclick, $page_value)
{
    $onclick .= '(' . $page_value . ')';
    $onclick .= '; return false;';

    return '<li class="page-item">'
        . '<a class="page-link" href="#"'
        . ' onclick ="' . $onclick . '"'
        . '}); return false;">' . $label . '</a></li>';
}

function disabled_button($label)
{
    return '<li class="page-item disabled"><span class="page-link">' . $label . '</span></li>';
}

/**
 * Make a link with button appearance with a js onclick function
 *  to navigate the page.
 *
 * @param int $edge Number of pages at edges before getting ellipsed.
 * @param int $radius Number of pages on either side of the current page.
 * @param int $max_length Largest number of pages to display without ellipses.
 *
 * @return array Array with the all the page displays.
 *  E.g., [1, 2, '...', 7, 8, 9, *10, 11, 12, 13, '...', 20, 21]
 *      $current=10, $total=21, $radius=3, $edge=2, $max_length = 10
 */
function get_pagination_range($current, $total, $edge = 2, $radius = 2)
{
    if ($total <= 1) return [1];
    if ($total <= $edge * 2) return range(1, $total);

    /* Ensure consistent total range size (e.g., edge*2 + radius*2 + 1)
     */
    // Always show first $edge pages
    $pages = range(1, $edge);

    // Determine middle block
    $min_middle = $edge + 1;
    $max_middle = $total - $edge;

    $start = max($min_middle, $current - $radius);
    $end = min($max_middle, $current + $radius);

    // If we're near the start, expand right to keep fixed width
    if ($current <= $edge + $radius) {
        $start = $edge + 1;
        $end = $start + $radius * 2;
    }

    // If we're near the end, expand left
    if ($current >= $total - $edge - $radius + 1) {
        $end = $total - $edge;
        $start = $end - $radius * 2;
    }

    if ($start > $edge + 1) {
        $pages[] = '...';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i > $edge && $i < $total - $edge + 1)
            $pages[] = $i;
    }

    if ($end < $total - $edge) {
        $pages[] = '...';
    }

    // Make last $edge pages
    for ($i = $total - $edge + 1; $i <= $total; $i++) {
        $pages[] = $i;
    }

    return $pages;
}

/**
 * Create a pagination bar.
 * Format: | < Previous | 1 | 2 | ... | 6 | [ 7 ] | 8 | 9 | ... | 56854 | 56855 | Next > |
 *  The current page ([ 7 ]) should be an input allowing jumping to any page.
 *
 * @param string $js_loadpage_func Name of the js function to handle the load page event.
 *  E.g., handleForm.loadPage = (page) => { ... }.
 *
 * @return string Html of whole pagination bar.
 */
function render_pagination_bar($current_page, $total_pages, $js_loadpage_func)
{
    if ($total_pages <= 1) return;

    $current_page = max(1, min($current_page, $total_pages));

    // Start pagination markup
    $html = '<nav class="d-flex justify-content-center mt-3"><ul class="pagination">';

    // Prev button
    $html .= $current_page > 1
        ? link_button("« Prev", $js_loadpage_func, $current_page - 1)
        : disabled_button("« Prev");

    // Page number range
    $range = get_pagination_range($current_page, $total_pages);

    foreach ($range as $item) {
        if ($item === '...') {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        } elseif ($item == $current_page) {
            $html .= '<li class="page-item active">'
                . '<input type="number" min="1" max="' . $total_pages . '"'
                . ' value="' . $current_page . '" class="form-control page-link text-center rounded-0"'
                . ' onchange="' . $js_loadpage_func . '(this.value); return false;">'
                . '</li>';
        } else {
            $html .= link_button($item, $js_loadpage_func, $item);
        }
    }

    // Next button
    $html .= $current_page < $total_pages
        ? link_button("Next »", $js_loadpage_func, $current_page + 1)
        : disabled_button("Next »");

    // End pagination markup
    $html .= '</ul></nav>';

    echo $html;
}
