<?php
require_once 'check_admin.php';
require_once '../config/sys_config.php';
require_once '../config/database.php';



if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}
?>
<?php
require_once '../config/sys_config.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// --- Xử lý xóa món ăn và nạp file kết nối DB ---
require_once '../config/database.php'; 

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_delete = (int)$_GET['id'];
    try {
        // Lấy tên ảnh cũ để xóa file trên server nhằm tối ưu không gian lưu trữ
        $stmt_img = $conn->prepare("SELECT image FROM products WHERE id = :id");
        $stmt_img->execute(['id' => $id_delete]);
        $product = $stmt_img->fetch(PDO::FETCH_ASSOC);
        if ($product && !empty($product['image']) && file_exists('../uploads/' . $product['image'])) {
            unlink('../uploads/' . $product['image']);
        }

        // Xóa món ăn trong Database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id_delete]);
        $_SESSION['success_msg'] = "Xóa món ăn thành công!";
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Lỗi xóa món ăn: " . $e->getMessage();
    }
    header('Location: index.php');
    exit();
}

// Lấy danh sách món ăn từ database
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>QFood Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-sm">
          <div class="admin-header">
    <div>
        <h1>🍔 QFood Dashboard</h1>
        <p>Xin chào,
            <strong><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong>
        </p>
    </div>

    <div>
        <span class="badge bg-success fs-6 px-3 py-2">
            Administrator
        </span>
    </div>
</div>

<hr>
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✨ <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ❌ <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

           <div class="admin-toolbar">
   <h4>📋 Danh sách sản phẩm</h4>

<div>

<a href="../index.php"
class="btn btn-dark rounded-pill me-2">
🏠 Trang chủ
</a>

<a href="orders.php"
class="btn btn-info text-white rounded-pill me-2">
📦 Đơn hàng
</a>

<a href="add.php"
class="btn btn-success rounded-pill">
➕ Thêm sản phẩm
</a>

</div>
</div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center small">
                   <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên Món</th>
                            <th>Giá</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $pro): ?>
                                <tr>
                                    <td><?= $pro['id'] ?></td>
                                    <td>
                                        <?php if (!empty($pro['image'])): ?>
                                            <img src="../uploads/<?= htmlspecialchars($pro['image']) ?>" class="rounded border admin-product-thumb">
                                        <?php else: ?>
                                            <span class="text-muted">Không ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-start fw-bold"><?= htmlspecialchars($pro['name']) ?></td>
                                    <td class="text-danger fw-bold"><?= number_format($pro['price'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <a href="edit_product.php?id=<?= $pro['id'] ?>" class="class="btn btn-outline-primary btn-sm rounded-pill">Sửa</a>
                                        <a href="index.php?action=delete&id=<?= $pro['id'] ?>" class="class="btn btn-outline-primary btn-sm rounded-pill" onclick="return confirm('Bạn có chắc chắn muốn xóa món ăn này không?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-muted">Chưa có món ăn nào!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="../logout.php" class="class="btn btn-danger rounded-pill mt-4 px-4">Đăng xuất hệ thống</a>
        </div>
    </div>
</body>
</html>
