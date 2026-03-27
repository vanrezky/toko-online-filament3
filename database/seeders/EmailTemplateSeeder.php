<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'reset_password',
                'name' => 'Reset Password',
                'subject' => 'Reset Password - {{website_name}}',
                'body' => $this->getResetPasswordTemplate(),
                'placeholders' => ['customer_name', 'reset_url', 'expiry_minutes', 'website_name'],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'payment_request',
                'name' => 'Permintaan Pembayaran',
                'subject' => 'Permintaan Pembayaran Order #{{order_id}} - {{website_name}}',
                'body' => $this->getPaymentRequestTemplate(),
                'placeholders' => ['customer_name', 'order_id', 'order_total', 'payment_url', 'expiry_time', 'website_name'],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'payment_success',
                'name' => 'Pembayaran Berhasil',
                'subject' => 'Pembayaran Berhasil untuk Order #{{order_id}} - {{website_name}}',
                'body' => $this->getPaymentSuccessTemplate(),
                'placeholders' => ['customer_name', 'order_id', 'order_total', 'payment_method', 'transaction_date', 'website_name'],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'order_expiry',
                'name' => 'Order Kadaluarsa',
                'subject' => 'Order #{{order_id}} Kadaluarsa - {{website_name}}',
                'body' => $this->getOrderExpiryTemplate(),
                'placeholders' => ['customer_name', 'order_id', 'expiry_time', 'payment_url', 'website_name'],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'code' => 'order_status_changed',
                'name' => 'Status Order Berubah',
                'subject' => 'Update Status Order #{{order_id}} - {{website_name}}',
                'body' => $this->getOrderStatusChangedTemplate(),
                'placeholders' => ['customer_name', 'order_id', 'old_status', 'new_status', 'tracking_number', 'courier_name', 'website_name'],
                'is_active' => true,
                'is_default' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }

    private function getResetPasswordTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #4F46E5; color: white !important; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reset Password</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{customer_name}}</strong>,</p>
        <p>Kami menerima permintaan untuk reset password akun Anda. Silakan klik tombol di bawah ini untuk reset password:</p>
        <p style="text-align: center;">
            <a href="{{reset_url}}" class="button">Reset Password</a>
        </p>
        <p>Link reset password ini akan kadaluarsa dalam <strong>{{expiry_minutes}} menit</strong>.</p>
        <p>Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini.</p>
    </div>
    <div class="footer">
        <p>{{website_name}}</p>
    </div>
</body>
</html>
HTML;
    }

    private function getPaymentRequestTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .order-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
        .button { display: inline-block; background: #059669; color: white !important; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Permintaan Pembayaran</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{customer_name}}</strong>,</p>
        <p>Terima kasih telah berbelanja di {{website_name}}! Silakan selesaikan pembayaran untuk order Anda:</p>
        
        <div class="order-details">
            <p><strong>Order ID:</strong> #{{order_id}}</p>
            <p><strong>Total:</strong> Rp {{order_total}}</p>
            <p><strong>Batas Pembayaran:</strong> {{expiry_time}}</p>
        </div>
        
        <p style="text-align: center;">
            <a href="{{payment_url}}" class="button">Bayar Sekarang</a>
        </p>
        
        <p>Silakan klik tombol di atas untuk melanjutkan ke halaman pembayaran.</p>
    </div>
    <div class="footer">
        <p>{{website_name}}</p>
    </div>
</body>
</html>
HTML;
    }

    private function getPaymentSuccessTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Berhasil</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .success-icon { font-size: 48px; text-align: center; margin: 20px 0; }
        .order-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pembayaran Berhasil!</h1>
    </div>
    <div class="content">
        <div class="success-icon">🎉</div>
        <p>Halo <strong>{{customer_name}}</strong>,</p>
        <p>Pembayaran untuk order Anda telah kami terima. Berikut detailnya:</p>
        
        <div class="order-details">
            <p><strong>Order ID:</strong> #{{order_id}}</p>
            <p><strong>Jumlah:</strong> Rp {{order_total}}</p>
            <p><strong>Metode Pembayaran:</strong> {{payment_method}}</p>
            <p><strong>Tanggal:</strong> {{transaction_date}}</p>
        </div>
        
        <p>Kami akan segera memproses order Anda setelah pembayaran terkonfirmasi.</p>
    </div>
    <div class="footer">
        <p>{{website_name}}</p>
    </div>
</body>
</html>
HTML;
    }

    private function getOrderExpiryTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Kadaluarsa</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #DC2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .warning { background: #FEF2F2; border: 1px solid #FECACA; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .button { display: inline-block; background: #DC2626; color: white !important; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Kadaluarsa</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{customer_name}}</strong>,</p>
        
        <div class="warning">
            <p><strong>Mohon Perhatian:</strong></p>
            <p>Order <strong>#{{order_id}}</strong> telah kadaluarsa pada <strong>{{expiry_time}}</strong>.</p>
        </div>
        
        <p>Jika Anda masih interested dengan produk ini, Anda dapat membuat order baru dan menyelesaikan pembayaran sebelum kadaluarsa.</p>
        
        <p style="text-align: center;">
            <a href="{{payment_url}}" class="button">Buat Order Baru</a>
        </p>
    </div>
    <div class="footer">
        <p>{{website_name}}</p>
    </div>
</body>
</html>
HTML;
    }

    private function getOrderStatusChangedTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Status Order</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .status-change { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; text-align: center; }
        .status-old { color: #6B7280; text-decoration: line-through; }
        .status-new { color: #059669; font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Update Status Order</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{customer_name}}</strong>,</p>
        <p>Status order Anda telah diperbarui:</p>
        
        <div class="status-change">
            <p><strong>Order ID:</strong> #{{order_id}}</p>
            <p>
                Status: 
                <span class="status-old">{{old_status}}</span>
                →
                <span class="status-new">{{new_status}}</span>
            </p>
            @if {{tracking_number}}
            <p><strong>No. Tracking:</strong> {{tracking_number}}</p>
            <p><strong>Kurir:</strong> {{courier_name}}</p>
            @endif
        </div>
        
        <p>Terima kasih telah berbelanja di {{website_name}}!</p>
    </div>
    <div class="footer">
        <p>{{website_name}}</p>
    </div>
</body>
</html>
HTML;
    }
}
