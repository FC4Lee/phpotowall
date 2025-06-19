<?php
// 确保无额外输出
ob_start();

// 设置 JSON 内容类型
header('Content-Type: application/json');

// 禁用错误显示，记录到日志
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// 兼容旧 PHP 版本
error_reporting(E_ALL & ~E_NOTICE);

// 定义 music 目录路径
$musicDir = realpath(dirname(__FILE__) . '/music');

// 检查目录是否存在
if (!is_dir($musicDir)) {
    http_response_code(500);
    echo json_encode(array('error' => 'Music directory does not exist: ' . $musicDir));
    ob_end_flush();
    exit;
}

// 扫描目录
try {
    $files = scandir($musicDir);
    if ($files === false) {
        throw new Exception('Unable to read directory');
    }
    // 过滤音乐文件
    $music = array_filter($files, function($file) {
        return preg_match('/\.(mp3|wav|ogg)$/i', $file);
    });
    // 移除 . 和 .. 目录
    $music = array_values(array_diff($music, array('.', '..')));
    // 输出 JSON
    echo json_encode($music);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Unable to read directory: ' . $e->getMessage()));
}

// 清空输出缓冲
ob_end_flush();
?>