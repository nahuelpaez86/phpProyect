<?php
function renderFlashAlert( $delayMs = 3000) {
 $redirectUrl = $_SERVER['HTTP_REFERER'];
  if (isset($_GET['post_success']) || isset($_GET['post_error'])) {
    $type = isset($_GET['post_success']) ? 'success' : 'danger';
    $message = htmlspecialchars($_GET['post_success'] ?? $_GET['post_error']);
    echo <<<HTML
      <div class="container mt-4">
        <div class="alert alert-$type alert-dismissible fade show text-center" role="alert">
          $message
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      </div>
HTML;
    if ($redirectUrl) {
      echo <<<SCRIPT
        <script>
          setTimeout(() => {
            window.location.href = "$redirectUrl";
          }, $delayMs);
        </script>
SCRIPT;
    }
  }
}
?>