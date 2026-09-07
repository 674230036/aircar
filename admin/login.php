<?php
session_start();
require_once __DIR__ . '/../classes/Admin.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (Admin::verifyCredentials($username, $password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_name'] = ($username === 'admin') ? 'ผู้จัดการระบบ (Admin)' : 'เจ้าหน้าที่เทคนิค';
        header('Location: index.php');
        exit;
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (ทดลองใช้: admin / admin123)';
    }
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เข้าสู่ระบบผู้ดูแล • AirCare Pro</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    body {
      background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0b1526 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .login-container {
      width: 100%;
      max-width: 440px;
      background: #ffffff;
      border-radius: 24px;
      padding: 40px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
    }
    .login-brand {
      text-align: center;
      margin-bottom: 28px;
    }
    .login-logo {
      width: 58px;
      height: 58px;
      border-radius: 16px;
      background: linear-gradient(135deg, #0284c7, #2563eb);
      color: #fff;
      font-size: 26px;
      font-weight: 700;
      display: grid;
      place-items: center;
      margin: 0 auto 14px;
      box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
    }
    .login-brand h1 {
      font-size: 22px;
      color: #0f172a;
      margin-bottom: 4px;
    }
    .login-brand p {
      color: #64748b;
      font-size: 13.5px;
    }
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13.5px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .login-field {
      margin-bottom: 18px;
    }
    .login-field label {
      display: block;
      font-size: 13.5px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 6px;
    }
    .login-field input {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      font-family: inherit;
      font-size: 14.5px;
      transition: all 0.2s;
    }
    .login-field input:focus {
      outline: none;
      border-color: #0284c7;
      box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
    .demo-hint {
      background: #f0f9ff;
      border: 1px dashed #bae6fd;
      border-radius: 10px;
      padding: 12px;
      margin: 20px 0;
      font-size: 12.5px;
      color: #0369a1;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .demo-hint code {
      font-weight: bold;
      background: #e0f2fe;
      padding: 2px 6px;
      border-radius: 4px;
    }
    .btn-quick-fill {
      background: #0284c7;
      color: #fff;
      border: none;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 11.5px;
      cursor: pointer;
    }
    .back-home {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #64748b;
      font-size: 13.5px;
      text-decoration: none;
    }
    .back-home:hover {
      color: #0284c7;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-brand">
    <div class="login-logo">AC</div>
    <h1>AirCare Pro Management</h1>
    <p>ระบบจัดการร้านและวิเคราะห์ข้อมูลแดชบอร์ด</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-danger">
      <span>⚠️</span>
      <div><?=htmlspecialchars($error)?></div>
    </div>
  <?php endif; ?>

  <form method="post" id="loginForm">
    <div class="login-field">
      <label>ชื่อผู้ใช้งาน (Username)</label>
      <input type="text" name="username" id="username" placeholder="เช่น admin" required autofocus>
    </div>

    <div class="login-field">
      <label>รหัสผ่าน (Password)</label>
      <input type="password" name="password" id="password" placeholder="••••••••" required>
    </div>

    <div class="demo-hint">
      <div>Demo: <code>admin</code> / <code>admin123</code></div>
      <button type="button" class="btn-quick-fill" onclick="fillDemo()">ใส่ให้อัตโนมัติ</button>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15px; font-weight:600;">
      เข้าสู่ระบบผู้ดูแลระบบ →
    </button>
  </form>

  <a href="../index.php" class="back-home">← กลับสู่หน้าหลักลูกค้า (Customer Portal)</a>
</div>

<script>
function fillDemo() {
  document.getElementById('username').value = 'admin';
  document.getElementById('password').value = 'admin123';
}
</script>

</body>
</html>
