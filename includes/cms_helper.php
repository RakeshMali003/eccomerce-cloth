<?php
function get_cms_content($page, $key, $default = '')
{
    global $pdo;
    static $cache = [];

    // Return cached if available
    if (isset($cache[$page][$key])) {
        return $cache[$page][$key];
    }

    try {
        // Pre-fetch all content for the page to reduce queries
        if (!isset($cache[$page])) {
            $stmt = $pdo->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = ?");
            $stmt->execute([$page]);
            $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $cache[$page] = $results;
        }

        if (isset($cache[$page][$key]) && $cache[$page][$key] !== '') {
            return $cache[$page][$key];
        }
    } catch (Exception $e) {
        // Fallback to default on error
    }

    return $default;
}
?>