<?php
// src/Core/Mailer.php
namespace Hzjan\Bai01QuanlySv\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    /**
     * Gửi email
     * @param string $toEmail - Email người nhận
     * @param string $toName - Tên người nhận
     * @param string $subject - Tiêu đề email
     * @param string $body - Nội dung email (hỗ trợ HTML)
     * @return bool
     */
    public static function send($toEmail, $toName, $subject, $body) {
        // Nạp file cấu hình (vì nó không được autoload)
        $configPath = dirname(__DIR__, 2) . '/config.php';
        if (!file_exists($configPath)) {
            throw new Exception("Config file not found at: $configPath");
        }
        
        // Sử dụng include để đảm bảo file được load
        $result = include $configPath;
        if ($result === false) {
            throw new Exception("Failed to include config file: $configPath");
        }
        
        // Kiểm tra xem hằng số có được định nghĩa không
        if (!defined('MAIL_HOST')) {
            throw new Exception("MAIL_HOST constant not defined. Please check config.php file.");
        }

        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = \MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = \MAIL_USERNAME;
            $mail->Password = \MAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Hoặc ENCRYPTION_SMTPS
            $mail->Port = \MAIL_PORT;

            // Người gửi và người nhận
            $mail->setFrom(\MAIL_FROM_ADDRESS, \MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName); // Thêm người nhận

            // Nội dung
            $mail->isHTML(true); // Gửi email dạng HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body); // Nội dung dạng text cho các trình duyệt mail không hỗ trợ HTML

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Có thể ghi log lỗi ở đây
            $errorMsg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            error_log($errorMsg . "\n", 3, PROJECT_ROOT . '/logs/email_errors.log');
            // Vẫn trả về false nhưng không throw exception để code có thể tiếp tục
            return false;
        }
    }
}
