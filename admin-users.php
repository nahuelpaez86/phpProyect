<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'services/usersServices.php';

$users = handleUserGet();
$isLogged = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RentaVeloz | Usuarios Admin</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .badge-confirmada {
      background-color: #22c55e;
    }

    .badge-cancelada {
      background-color: #ef4444;
    }

    .badge-completada {
      background-color: #6b7280;
    }
  </style>
</head>

<body>

  <!-- ***** Preloader Start ***** -->

  <!-- ***** Preloader End ***** -->

  <!-- ***** Admin Header Start ***** -->
  <?php renderHeader('users', $isLogged); ?>
  <!-- ***** Admin Header End ***** -->
  <div class="py-5"></div>
  <section class="section">
    <div class="container-fluid px-300">
      <div class="table-responsive">
        <a href="admin-users-add.php" class="btn btn-sm btn-primary">Crear usuario administrador</a>
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <!-- ***** tablas ***** -->
          <tbody>
            <?php if (!empty($users)): ?>
              <?php $editingId = $_GET['edit'] ?? null;
              foreach ($users  as $user): ?>
                <!-- ***** tabla de edicion ***** -->
                <?php if ($editingId == $user['id']): ?>
                  <form method="POST" action="services/usersServices.php?action=update&id=<?= $user['id'] ?>&path=admin-users.php">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <tr>
                      <td><input type="text" name="name" class="form-control" value="<?= $user['name'] ?>"></td>
                      <td><input type="email" name="email" class="form-control" value="<?= $user['email'] ?>"></td>
                      <td>
                        <select name="role" class="form-select">
                          <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Cliente</option>
                          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                      </td>
                      <td>
                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                        <a href="admin-users.php" class="btn btn-sm btn-secondary">Cancelar</a>
                      </td>
                    </tr>
                  </form>
                <?php else: ?>
                  <!-- ***** tabla visual ***** -->
                  <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                      <?= htmlspecialchars($user['role'] == 'admin' ? 'Administrador' : 'Cliente') ?>
                    </td>
                    <td>
                      <a href="admin-users.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                      <a href="services/usersServices.php?action=delete&id=<?= $user['id'] ?>&path=admin-users.php" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('¿Estás seguro de que querés eliminar este usuario?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center">No tenés usuarios registrados.</p>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ***** alerts popup ***** -->
      <?php if (isset($_GET['post_success']) || isset($_GET['post_error'])): ?>
        <?php if (isset($_GET['post_success'])): ?>
          <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
              <?= htmlspecialchars($_GET['post_success']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
          </div>
        <?php elseif (isset($_GET['post_error'])): ?>
          <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
              <?= htmlspecialchars($_GET['post_error']) ?>
              <a href="car-details.php?id=<?= $car['id'] ?>" class="btn-close" role="button"></a>
            </div>
          </div>
        <?php endif; ?>
        <script>
          setTimeout(() => {
            window.location.href = "admin-users.php";
          }, 3000);
        </script>
      <?php endif; ?>
    </div>
  </section>

  <!-- ***** Footer Start ***** -->
  <?php echo renderFooter(); ?>

  <script src="assets/js/jquery-2.1.0.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <!-- Bootstrap -->
  <script src="assets/js/popper.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <!-- Plugins -->
  <script src="assets/js/scrollreveal.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/imgfix.min.js"></script>
  <script src="assets/js/mixitup.js"></script>
  <script src="assets/js/accordions.js"></script>
  <script>
    document.querySelectorAll('.days-input').forEach((input) => {
      input.addEventListener('input', function() {
        const days = parseInt(this.value) || 0;
        const price = parseFloat(this.dataset.price) || 0;
        const total = days * price;

        const totalInput = this.closest('tr').querySelector('.total-amount-input');
        if (totalInput) {
          totalInput.value = total;
        }
      });
    });
  </script>
</body>

</html>