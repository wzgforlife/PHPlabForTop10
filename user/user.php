<?php
session_start();

// 检查用户是否登录（逻辑漏洞，只检查session中是否存在user_id）
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.html');
    exit();
}

// 检查用户权限（逻辑漏洞，未正确验证用户角色）
$userRole = 'user';
if (isset($_COOKIE['user']) && $_COOKIE['user'] == 'admin') {
    $userRole = 'admin'; // 易受越权攻击
}

// 处理退出功能
if (isset($_POST['logout'])) {
    session_destroy();
    setcookie('user', '', time() - 3600);
    setcookie('token', '', time() - 3600);
    header('Location: /index.html');
    exit();
}

// XSS漏洞演示
$message = '';
if (isset($_GET['message'])) {
    // 未对用户输入进行过滤
    $message = $_GET['message'];
}

// 文件上传漏洞演示
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $uploadFile = $_FILES['upload'];
    $uploadDir = realpath(dirname(__FILE__) . '/../upload/') . '/';
    
    // 检查文件类型（存在绕过漏洞）
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($uploadFile['type'], $allowedTypes)) {
        // 存在路径遍历漏洞
        $fileName = basename($uploadFile['name']);
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($uploadFile['tmp_name'], $uploadPath)) {
            echo "<p>文件上传成功: <a href='/upload/$fileName'>$fileName</a></p>";
        } else {
            echo "<p>文件上传失败</p>";
        }
    } else {
        echo "<p>不允许的文件类型</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户面板</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logout-form {
            display: inline-block;
        }
        .message {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .upload-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .profile-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>欢迎，<?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo $userRole; ?>)</h2>
            <form class="logout-form" method="post">
                <input type="hidden" name="logout" value="1">
                <button type="submit">退出登录</button>
            </form>
        </div>
        
        <!-- XSS漏洞演示 -->
        <?php if (!empty($message)): ?>
        <div class="message">
            <p>系统消息: <?php echo $message; ?></p>
        </div>
        <?php endif; ?>
        
        <!-- 逻辑漏洞：管理员可以看到额外信息 -->
        <?php if ($userRole === 'admin'): ?>
        <div>
            <h3>管理员控制面板</h3>
            <p>您有权限访问敏感数据。</p>
            <!-- RCE漏洞：通过eval执行用户输入 -->
            <form method="post" action="/evaluate/evaluate.php">
                <label for="code">执行代码:</label>
                <textarea name="code" id="code" rows="5" cols="50"></textarea>
                <button type="submit">执行</button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- 文件上传漏洞演示 -->
        <div class="upload-section">
            <h3>文件上传</h3>
            <form method="post" enctype="multipart/form-data">
                <label for="upload">选择文件:</label>
                <input type="file" name="upload" id="upload">
                <button type="submit">上传</button>
            </form>
        </div>
        
        <!-- 反序列化漏洞演示 -->
        <div class="profile-section">
            <h3>用户配置文件</h3>
            <form method="post" action="/profile/profile.php">
                <label for="profile">配置文件:</label>
                <textarea name="profile" id="profile" rows="5" cols="50"></textarea>
                <button type="submit">保存</button>
            </form>
        </div>
    </div>
</body>
</html>