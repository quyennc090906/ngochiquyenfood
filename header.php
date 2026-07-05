<?php
require_once 'config/sys_config.php';
require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QFood - Fresh Food</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

        <a class="navbar-brand fw-bold logo-text" href="index.php">
            <i class="fa-solid fa-burger"></i>
            QFood
        </a>

        <button class="navbar-toggler"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Trang chủ
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="menu.php">
                        Thực đơn
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">
                        Liên hệ
                    </a>
                </li>

            </ul>

            <?php
            $total_items = 0;

            if(isset($_SESSION['user'])){
                $stmt = $conn->prepare("SELECT SUM(quantity) total FROM cart WHERE user_id=?");
                $stmt->execute([$_SESSION['user']['id']]);
                $total_items = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            ?>

            <div class="d-flex align-items-center gap-2">

                <a href="cart.php" class="btn btn-success position-relative rounded-pill">

                    <i class="fa-solid fa-cart-shopping"></i>

                    <?php if($total_items>0): ?>

                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                        <?= $total_items ?>

                    </span>

                    <?php endif; ?>

                </a>

                <?php if(isset($_SESSION['user'])): ?>

                    <span class="user-name">

                        Xin chào,

                        <b><?= htmlspecialchars($_SESSION['user']['fullname']) ?></b>

                    </span>

                    <?php if($_SESSION['user']['role']=="admin"): ?>

                        <a href="admin/index.php"
                           class="btn btn-dark rounded-pill">

                            Dashboard

                        </a>

                    <?php endif; ?>

                    <a href="logout.php"
                       class="btn btn-outline-danger rounded-pill">

                        Đăng xuất

                    </a>

                <?php else: ?>

                    <a href="login.php"
                       class="btn btn-success rounded-pill">

                        Đăng nhập

                    </a>

                    <a href="register.php"
                       class="btn btn-outline-success rounded-pill">

                        Đăng ký

                    </a>

                <?php endif; ?>

            </div>

        </div>
    </div>
</nav>

<?php if(isset($_SESSION['message']) && basename($_SERVER['PHP_SELF'])=="cart.php"): ?>

<div class="container mt-3">

<div class="alert alert-success">

<?= htmlspecialchars($_SESSION['message']) ?>

</div>

</div>

<?php unset($_SESSION['message']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
