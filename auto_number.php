<?php
// Fungsi untuk membuat nomor otomatis
function getAutoNumber($index, $page, $perPage) {
    return ($page - 1) * $perPage + ($index + 1);
}
?>
