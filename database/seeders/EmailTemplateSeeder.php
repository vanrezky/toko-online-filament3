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
                'header_title' => 'Reset Password',
                'header_gradient' => '#4F46E5 0%, #7C3AED 100%',
                'body' => $this->getResetPasswordContent(),
                'placeholders' => ['customer_name', 'reset_url', 'expiry_minutes', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => false,
            ],
            [
                'code' => 'payment_request',
                'name' => 'Permintaan Pembayaran',
                'subject' => 'Permintaan Pembayaran Order #{{order_id}} - {{website_name}}',
                'header_title' => 'Permintaan Pembayaran',
                'header_gradient' => '#4F46E5 0%, #7C3AED 100%',
                'body' => $this->getPaymentRequestContent(),
                'placeholders' => ['customer_name', 'order_id', 'order_total', 'payment_url', 'expiry_time', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => true,
            ],
            [
                'code' => 'payment_success',
                'name' => 'Pembayaran Berhasil',
                'subject' => 'Pembayaran Berhasil untuk Order #{{order_id}} - {{website_name}}',
                'header_title' => 'Pembayaran Berhasil',
                'header_gradient' => '#059669 0%, #10B981 100%',
                'body' => $this->getPaymentSuccessContent(),
                'placeholders' => ['customer_name', 'order_id', 'order_total', 'payment_method', 'transaction_date', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => true,
            ],
            [
                'code' => 'order_expiry_reminder',
                'name' => 'Pengingat Order Akan Kadaluarsa',
                'subject' => 'Pengingat: Order #{{order_id}} Akan Kadaluarsa - {{website_name}}',
                'header_title' => 'Pengingat Order',
                'header_gradient' => '#F59E0B 0%, #D97706 100%',
                'body' => $this->getOrderExpiryReminderContent(),
                'placeholders' => ['customer_name', 'order_id', 'expiry_time', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => false,
            ],
            [
                'code' => 'order_expiry',
                'name' => 'Order Kadaluarsa',
                'subject' => 'Order #{{order_id}} Kadaluarsa - {{website_name}}',
                'header_title' => 'Order Kadaluarsa',
                'header_gradient' => '#DC2626 0%, #EF4444 100%',
                'body' => $this->getOrderExpiryContent(),
                'placeholders' => ['customer_name', 'order_id', 'expiry_time', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => true,
            ],
            [
                'code' => 'order_status_changed',
                'name' => 'Status Order Berubah',
                'subject' => 'Update Status Order #{{order_id}} - {{website_name}}',
                'header_title' => 'Update Status Order',
                'header_gradient' => '#4F46E5 0%, #7C3AED 100%',
                'body' => $this->getOrderStatusChangedContent(),
                'placeholders' => ['customer_name', 'order_id', 'old_status', 'new_status', 'tracking_number', 'courier_name', 'website_name'],
                'is_active' => true,
                'is_default' => true,
                'send_to_admin' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }

    private function getResetPasswordContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <p class="email-paragraph">Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah ini untuk melanjutkan:</p>
            <div class="text-center">
                <a href="{{reset_url}}" class="email-button">Reset Password</a>
            </div>
            <p class="text-center text-muted text-sm mt-4">Link ini akan kadaluarsa dalam <strong>{{expiry_minutes}} menit</strong></p>
            <p class="text-muted text-sm mt-6">Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini. Password Anda akan tetap aman.</p>';
    }

    private function getPaymentRequestContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <p class="email-paragraph">Terima kasih telah berbelanja di <strong>{{website_name}}</strong>! Silakan selesaikan pembayaran Anda.</p>
            <div class="email-box">
                <p><strong>Order ID:</strong> #{{order_id}}</p>
                <p><strong>Total Pembayaran:</strong> Rp {{order_total}}</p>
                <p><strong>Batas Waktu:</strong> {{expiry_time}}</p>
            </div>
            <div class="text-center">
                <a href="{{payment_url}}" class="email-button">Bayar Sekarang</a>
            </div>
            <p class="text-muted text-sm">Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum batas waktu yang ditentukan.</p>';
    }

    private function getPaymentSuccessContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <div class="email-success">
                <p class="text-center" style="font-size: 20px;">Pembayaran Anda telah berhasil!</p>
            </div>
            <p class="email-paragraph">Berikut adalah detail pembayaran Anda:</p>
            <div class="email-box">
                <p><strong>Order ID:</strong> #{{order_id}}</p>
                <p><strong>Total Pembayaran:</strong> Rp {{order_total}}</p>
                <p><strong>Metode Pembayaran:</strong> {{payment_method}}</p>
                <p><strong>Tanggal Pembayaran:</strong> {{transaction_date}}</p>
            </div>
            <p class="text-center mt-6" style="color: #059669;">Kami akan segera memproses pesanan Anda. Terima kasih telah berbelanja!</p>';
    }

    private function getOrderExpiryReminderContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <div class="email-warning">
                <p><strong>Perhatian!</strong></p>
                <p>Order Anda akan segera kadaluarsa.</p>
            </div>
            <p class="email-paragraph">Order Anda dengan detail berikut akan kadaluarsa dalam waktu dekat:</p>
            <div class="email-box">
                <p><strong>Order ID:</strong> #{{order_id}}</p>
                <p><strong>Batas Waktu:</strong> {{expiry_time}}</p>
            </div>
            <p style="color: #92400E;">Segera selesaikan pembayaran untuk menghindari pembatalan order.</p>';
    }

    private function getOrderExpiryContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <div class="email-error">
                <p><strong>Mohon Perhatian!</strong></p>
                <p>Order Anda telah kadaluarsa.</p>
            </div>
            <div class="email-box">
                <p><strong>Order ID:</strong> #{{order_id}}</p>
                <p><strong>Waktu Kadaluarsa:</strong> {{expiry_time}}</p>
            </div>
            <p style="color: #991B1B;">Mohon maaf, order Anda telah kadaluarsa karena melewati batas waktu pembayaran.</p>
            <p class="mt-4">Jika Anda masih ingin melakukan pembelian, silakan buat order baru.</p>';
    }

    private function getOrderStatusChangedContent(): string
    {
        return '<p class="email-greeting">Halo <strong>{{customer_name}}</strong>,</p>
            <p class="email-paragraph">Status order Anda telah diperbarui.</p>
            <div class="email-box">
                <p><strong>Order ID:</strong> #{{order_id}}</p>
                <p class="mt-4">
                    Status: 
                    <span class="status-badge status-old">{{old_status}}</span>
                    →
                    <span class="status-badge status-new">{{new_status}}</span>
                </p>
            </div>
            <p class="text-center mt-6">Terima kasih telah berbelanja di <strong>{{website_name}}</strong>!</p>';
    }
}
