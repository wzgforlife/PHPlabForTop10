<?php
session_start();

// 数据库配置（硬编码密码）
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'test_db');

// 连接数据库
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("数据库连接失败: " . mysqli_connect_error());
}

// 解密函数
function decryptData($encryptedData) {
    $data = base64_decode($encryptedData);
    $decrypted = '';
    $secretKey = 's3cr3tK3y42023'; // 与前端相同的密钥
    
    // 解密算法
    for ($i = 0; $i < strlen($data); $i++) {
        $decrypted .= chr(ord($data[$i]) ^ ord($secretKey[$i % strlen($secretKey)]));
    }
    
    // 分割用户名和密码
    $splitPoint = strpos($decrypted, chr(0));
    $username = base64_decode(substr($decrypted, 0, $splitPoint));
    $password = base64_decode(substr($decrypted, $splitPoint + 1));
    
    return ['username' => $username, 'password' => $password];
}

// 获取POST数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    // CSRF漏洞 - 使用相同的固定token
    if ($jsonData['csrf'] !== 'insecure_token_1234') {
        echo json_encode(['success' => false, 'message' => 'CSRF验证失败']);
        exit();
    }
    
    // 解密数据
    $decrypted = decryptData($jsonData['data']);
    
    // SQL注入漏洞 - 未使用预处理语句
    $username = $decrypted['username'];
    $password = $decrypted['password'];
    
    // 直接构造SQL查询
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // 登录成功，创建会话
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // 返回包含越权漏洞的token
        $token = 'user_token_1234';
        if ($user['role'] == 'admin') { // 使用role列判断角色
            $token = 'admin_token_5678'; // 管理员token
        }
        
        echo json_encode(['success' => true, 'message' => '登录成功', 'token' => $token]);
    } else {
        // 登录失败，返回SQL错误信息（信息泄露漏洞）
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    
    mysqli_close($conn);
}