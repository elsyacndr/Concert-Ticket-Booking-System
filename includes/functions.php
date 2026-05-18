<?php
/**
 * Utility helper functions untuk aplikasi.
 */

/**
 * Bind parameter prepared statement dengan referensi.
 * @param mysqli_stmt $stmt
 * @param string $types
 * @param array $params
 */
function bindParams($stmt, string $types, array &$params) {
    if ($types === '' || empty($params)) {
        return;
    }
    $bindNames = array_merge([$types], $params);
    $refs = [];
    foreach ($bindNames as $key => $value) {
        $refs[$key] = &$bindNames[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);
}
