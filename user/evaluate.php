<?php
session_start();

// 检查用户是否为管理员（逻辑漏洞，未正确验证用户角色）
$userRole = 'user';
if (isset($_COOKIE['user']) && $_COOKIE['user'] == 'admin') {
    $userRole = 'admin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userRole === 'admin') {
    $code = $_POST['code'] ?? '';
    
    // RCE漏洞：使用eval执行用户输入的代码
    eval($code);
} else {
    header('Location: /index.html');
    exit();
}