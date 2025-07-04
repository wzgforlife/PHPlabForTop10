<?php
// 创建数据库连接
$conn = mysqli_connect('localhost', 'root', '123456', 'root');

if (!$conn) {
    die("数据库连接失败: " . mysqli_connect_error());
}

// 创建用户表
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    role VARCHAR(10) DEFAULT 'user'
)";

if (mysqli_query($conn, $sql)) {
    // 检查是否存在phpweb用户，如果不存在则插入测试用户
    $checkSql = "SELECT * FROM users WHERE username = 'phpweb'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($checkResult) === 0) {
        $insertSql = "INSERT INTO users (username, password, role) VALUES 
        ('phpweb', 'phpweb', 'admin'), 
        ('user1', 'password1', 'user'),
        ('user2', 'password2', 'user')";
        
        if (mysqli_query($conn, $insertSql)) {
            echo "数据库初始化成功！";
        } else {
            echo "插入测试用户失败: " . mysqli_error($conn);
        }
    } else {
        echo "数据库已初始化！";
    }
} else {
    echo "创建表失败: " . mysqli_error($conn);
}

mysqli_close($conn);
