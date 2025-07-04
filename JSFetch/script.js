// 加密算法 - 需要逆向才能破解
function encryptData(username, password) {
    // 将用户名和密码转换为Base64
    const base64User = btoa(username);
    const base64Pass = btoa(password);
    
    // 创建一个简单的混淆字符串
    let encrypted = '';
    const secretKey = 's3cr3tK3y42023'; // 加密密钥
    
    // 混淆算法
    for (let i = 0; i < base64User.length; i++) {
        encrypted += String.fromCharCode(
            base64User.charCodeAt(i) ^ secretKey.charCodeAt(i % secretKey.length)
        );
    }
    
    encrypted += String.fromCharCode(0); // 添加分隔符
    
    for (let i = 0; i < base64Pass.length; i++) {
        encrypted += String.fromCharCode(
            base64Pass.charCodeAt(i) ^ secretKey.charCodeAt(i % secretKey.length)
        );
    }
    
    // 最终加密结果
    return btoa(encrypted);
}