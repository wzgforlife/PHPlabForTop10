<?php
// SSRF漏洞演示
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['url'])) {
    // 未校验的URL请求
    $url = $_GET['url'];
    $content = file_get_contents($url);
    echo $content;
} else {
    echo "无效的请求";
}
?>