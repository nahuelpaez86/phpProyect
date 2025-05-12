<?php
function renderAlert(string $type, string $message): void {
    if (!empty($message)) {
        $label = $type === 'success' ? 'Éxito' : 'Error';
        echo "
        <div class='alert alert-$type alert-dismissible fade show' role='alert'>
            <strong>$label:</strong> " . htmlspecialchars($message) . "
        </div>";
    }
}