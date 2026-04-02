<?php
// public/index.php
// Nạp file autoload của Composer
session_start();
define('PROJECT_ROOT', dirname(__DIR__));
// Nạp file autoload của Composer
require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Hzjan\Bai01QuanlySv\Controllers\SinhvienController;
use Hzjan\Bai01QuanlySv\Controllers\UserController;
use Hzjan\Bai01QuanlySv\Controllers\PageController;
// Simple Router
$action = $_GET['action'] ?? 'index';

// Danh sách các action KHÔNG yêu cầu đăng nhập
$public_actions = [
    'login',           // Hiển thị form đăng nhập
    'do_login',        // Xử lý logic đăng nhập
    'register',        // Hiển thị form đăng ký
    'do_register',     // Xử lý logic đăng ký
    'verify',          // Xử lý link xác nhận email
    'contact',         // Hiển thị form liên hệ
    'submit_contact'   // Xử lý logic gửi liên hệ
];

// --- TRẠM KIỂM SOÁT BẢO MẬT (GATEKEEPER) ---
// Nếu action KHÔNG nằm trong danh sách public VÀ người dùng CHƯA đăng nhập
if (!in_array($action, $public_actions) && !isset($_SESSION['user_id'])) {
    // Ghi lại lỗi (tùy chọn, dùng FlashMessage)
    // FlashMessage::set('login_form', 'Vui lòng đăng nhập để tiếp tục.', 'error');
    
    // Chuyển hướng về trang đăng nhập
    header('Location: index.php?action=login');
    exit(); // Dừng thực thi ngay lập tức
}
$controller = new SinhvienController();

if (in_array($action, ['contact', 'submit_contact'])) {
    $controller = new PageController();
} elseif (
    in_array($action, [
        'login',
        'register',
        'do_login',
        'do_register',
        'logout'
    ])
) {
    $controller = new UserController();
} else {
    $controller = new SinhvienController();
}
switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'add':
        $controller->add();
        break;
    // THÊM 2 CASE MỚI (bài 03)
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    // Các action của UserController
    case 'login':
        $controller->showLoginForm();
        break;
    case 'do_login':
        $controller->login();
        break;
    case 'register':
        $controller->showRegisterForm();
        break;
    case 'do_register':
        $controller->register();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'contact':
        $controller->showContactForm();
        break;
    case 'submit_contact':
        $controller->submitContact();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'index':
    default:
        $controller->index();
        break;
}
?>