<?php
session_start();

// 反序列化漏洞演示
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profile'])) {
    $profile = unserialize($_POST['profile']);
    
    if ($profile instanceof UserProfile) {
        echo "欢迎回来，" . $profile->username;
    } else {
        echo "无效的用户配置文件";
    }
}

// 用户类
class UserProfile {
    public $username;
    public $role;
    
    public function __construct($username, $role) {
        $this->username = $username;
        $this->role = $role;
    }
    
    // __destruct方法，可能用于命令执行
    public function __destruct() {
        if ($this->role === 'super_admin') {
            // 这里可以插入危险代码
            system($this->username);
        }
    }
}
?>