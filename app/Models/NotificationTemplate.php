<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = ['event_key', 'title', 'body', 'channel', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * All supported push-notification events with human-readable labels and icons.
     */
    public static function eventList(): array
    {
        return [
            // ── Bookings ──────────────────────────────────────────
            'booking_created'          => ['label' => 'Booking Created',           'icon' => 'bi-calendar-plus',         'category' => 'Bookings'],
            'booking_confirmed'        => ['label' => 'Booking Confirmed',         'icon' => 'bi-calendar-check',        'category' => 'Bookings'],
            'booking_cancelled'        => ['label' => 'Booking Cancelled',         'icon' => 'bi-calendar-x',            'category' => 'Bookings'],
            'booking_rescheduled'      => ['label' => 'Booking Rescheduled',       'icon' => 'bi-calendar-event',        'category' => 'Bookings'],
            'booking_reminder'         => ['label' => 'Booking Reminder',          'icon' => 'bi-alarm',                 'category' => 'Bookings'],
            'booking_completed'        => ['label' => 'Booking Completed',         'icon' => 'bi-check-circle',          'category' => 'Bookings'],
            'booking_no_show'          => ['label' => 'Booking No-Show',           'icon' => 'bi-person-dash',           'category' => 'Bookings'],

            // ── Job Cards ─────────────────────────────────────────
            'job_card_opened'          => ['label' => 'Job Card Opened',           'icon' => 'bi-tools',                 'category' => 'Job Cards'],
            'job_card_diagnosis_done'  => ['label' => 'Diagnosis Completed',       'icon' => 'bi-clipboard2-pulse',      'category' => 'Job Cards'],
            'job_card_approval_needed' => ['label' => 'Customer Approval Needed',  'icon' => 'bi-hand-thumbs-up',        'category' => 'Job Cards'],
            'job_card_in_progress'     => ['label' => 'Work In Progress',          'icon' => 'bi-wrench-adjustable',     'category' => 'Job Cards'],
            'job_card_ready'           => ['label' => 'Vehicle Ready for Pickup',  'icon' => 'bi-car-front',             'category' => 'Job Cards'],
            'job_card_delivered'       => ['label' => 'Vehicle Delivered',         'icon' => 'bi-check2-circle',         'category' => 'Job Cards'],

            // ── Payments ──────────────────────────────────────────
            'payment_received'         => ['label' => 'Payment Received',          'icon' => 'bi-cash-stack',            'category' => 'Payments'],
            'payment_due'              => ['label' => 'Payment Due',               'icon' => 'bi-exclamation-circle',    'category' => 'Payments'],
            'payment_overdue'          => ['label' => 'Payment Overdue',           'icon' => 'bi-clock-history',         'category' => 'Payments'],
            'invoice_generated'        => ['label' => 'Invoice Generated',         'icon' => 'bi-receipt',               'category' => 'Payments'],

            // ── Expenses ──────────────────────────────────────────
            'expense_submitted'        => ['label' => 'Expense Submitted',         'icon' => 'bi-cash-coin',             'category' => 'Expenses'],
            'expense_approved'         => ['label' => 'Expense Approved',          'icon' => 'bi-patch-check',           'category' => 'Expenses'],
            'expense_rejected'         => ['label' => 'Expense Rejected',          'icon' => 'bi-x-circle',              'category' => 'Expenses'],

            // ── Car Sales ─────────────────────────────────────────
            'car_sale_created'         => ['label' => 'Car Sale Created',          'icon' => 'bi-car-front-fill',        'category' => 'Car Sales'],
            'car_sale_approved'        => ['label' => 'Car Sale Approved',         'icon' => 'bi-bag-check',             'category' => 'Car Sales'],
            'car_purchase_created'     => ['label' => 'Car Purchase Created',      'icon' => 'bi-cart-plus',             'category' => 'Car Sales'],

            // ── Users & Account ───────────────────────────────────
            'user_welcome'             => ['label' => 'Welcome New User',          'icon' => 'bi-person-check',          'category' => 'Account'],
            'password_reset'           => ['label' => 'Password Reset Request',    'icon' => 'bi-key',                   'category' => 'Account'],
            'account_suspended'        => ['label' => 'Account Suspended',         'icon' => 'bi-person-x',              'category' => 'Account'],
            'account_activated'        => ['label' => 'Account Activated',         'icon' => 'bi-person-check-fill',     'category' => 'Account'],

            // ── System ────────────────────────────────────────────
            'system_maintenance'       => ['label' => 'System Maintenance',        'icon' => 'bi-gear-wide-connected',   'category' => 'System'],
            'low_stock_alert'          => ['label' => 'Low Stock Alert',           'icon' => 'bi-box-seam',              'category' => 'System'],
            'report_ready'             => ['label' => 'Report Ready',              'icon' => 'bi-file-earmark-bar-graph','category' => 'System'],
        ];
    }

    /**
     * Available channel options.
     */
    public static function channels(): array
    {
        return [
            'push'  => ['label' => 'Push Notification', 'icon' => 'bi-bell-fill',         'color' => '#2563eb'],
            'sms'   => ['label' => 'SMS',                'icon' => 'bi-chat-dots-fill',    'color' => '#16a34a'],
            'email' => ['label' => 'Email',              'icon' => 'bi-envelope-fill',     'color' => '#ea580c'],
            'all'   => ['label' => 'All Channels',       'icon' => 'bi-broadcast-pin',     'color' => '#7c3aed'],
        ];
    }

    /**
     * Supported template variables with descriptions.
     */
    public static function variables(): array
    {
        return [
            '{{name}}'           => 'Customer / recipient name',
            '{{booking_no}}'     => 'Booking reference number',
            '{{date}}'           => 'Date of the event',
            '{{time}}'           => 'Time of the event',
            '{{vehicle}}'        => 'Vehicle make and model',
            '{{registration}}'   => 'Vehicle registration number',
            '{{amount}}'         => 'Amount (payment or invoice)',
            '{{service_type}}'   => 'Type of service',
            '{{technician}}'     => 'Assigned technician name',
            '{{advisor}}'        => 'Service advisor name',
            '{{company_name}}'   => 'Your business name',
            '{{company_phone}}'  => 'Your business phone number',
            '{{job_card_no}}'    => 'Job card reference number',
            '{{invoice_no}}'     => 'Invoice number',
            '{{status}}'         => 'Current status of the record',
        ];
    }

    /**
     * Default templates seeded on first install.
     */
    public static function defaults(): array
    {
        return [
            ['event_key' => 'booking_created',
             'title'     => 'Booking Received – {{booking_no}}',
             'body'      => 'Hi {{name}}, your booking {{booking_no}} for {{service_type}} on {{date}} at {{time}} has been received. We will confirm shortly. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-calendar-plus', 'is_active' => true],

            ['event_key' => 'booking_confirmed',
             'title'     => 'Booking Confirmed – {{booking_no}}',
             'body'      => 'Great news, {{name}}! Your booking {{booking_no}} for {{service_type}} on {{date}} at {{time}} is confirmed. See you soon! – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-calendar-check', 'is_active' => true],

            ['event_key' => 'booking_cancelled',
             'title'     => 'Booking Cancelled – {{booking_no}}',
             'body'      => 'Hi {{name}}, your booking {{booking_no}} scheduled for {{date}} at {{time}} has been cancelled. Contact us at {{company_phone}} for assistance.',
             'channel'   => 'push', 'icon' => 'bi-calendar-x', 'is_active' => true],

            ['event_key' => 'booking_reminder',
             'title'     => 'Reminder: Appointment Tomorrow',
             'body'      => 'Hi {{name}}, this is a reminder for your {{service_type}} appointment tomorrow at {{time}}. Vehicle: {{vehicle}} ({{registration}}). – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-alarm', 'is_active' => true],

            ['event_key' => 'booking_completed',
             'title'     => 'Service Completed – {{booking_no}}',
             'body'      => 'Hi {{name}}, the service for your {{vehicle}} has been completed. Thank you for choosing {{company_name}}. We hope to see you again!',
             'channel'   => 'push', 'icon' => 'bi-check-circle', 'is_active' => true],

            ['event_key' => 'job_card_approval_needed',
             'title'     => 'Approval Required – {{job_card_no}}',
             'body'      => 'Hi {{name}}, our technician has completed the diagnosis for your {{vehicle}}. Your approval is needed to proceed. Please call {{company_phone}} or reply to this message.',
             'channel'   => 'push', 'icon' => 'bi-hand-thumbs-up', 'is_active' => true],

            ['event_key' => 'job_card_ready',
             'title'     => 'Vehicle Ready for Pickup – {{job_card_no}}',
             'body'      => 'Hi {{name}}, your {{vehicle}} ({{registration}}) is ready for pickup at {{company_name}}. Please bring your job card reference {{job_card_no}}.',
             'channel'   => 'push', 'icon' => 'bi-car-front', 'is_active' => true],

            ['event_key' => 'job_card_delivered',
             'title'     => 'Vehicle Delivered – {{job_card_no}}',
             'body'      => 'Thank you {{name}}! Your {{vehicle}} has been delivered. We hope the service met your expectations. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-check2-circle', 'is_active' => true],

            ['event_key' => 'payment_received',
             'title'     => 'Payment Received – {{invoice_no}}',
             'body'      => 'Hi {{name}}, we have received your payment of {{amount}} against invoice {{invoice_no}}. Thank you! – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-cash-stack', 'is_active' => true],

            ['event_key' => 'payment_due',
             'title'     => 'Payment Due Reminder',
             'body'      => 'Hi {{name}}, your payment of {{amount}} for {{invoice_no}} is due on {{date}}. Please arrange payment to avoid delays. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-exclamation-circle', 'is_active' => true],

            ['event_key' => 'payment_overdue',
             'title'     => 'Payment Overdue – Action Required',
             'body'      => 'Hi {{name}}, your payment of {{amount}} for {{invoice_no}} is overdue. Please contact us at {{company_phone}} immediately. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-clock-history', 'is_active' => true],

            ['event_key' => 'expense_approved',
             'title'     => 'Expense Approved',
             'body'      => 'Your expense request of {{amount}} has been approved. It will be processed on {{date}}. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-patch-check', 'is_active' => true],

            ['event_key' => 'expense_rejected',
             'title'     => 'Expense Request Rejected',
             'body'      => 'Your expense request of {{amount}} submitted on {{date}} has been rejected. Please contact your manager for details.',
             'channel'   => 'push', 'icon' => 'bi-x-circle', 'is_active' => true],

            ['event_key' => 'car_sale_approved',
             'title'     => 'Car Sale Approved',
             'body'      => 'Hi {{name}}, your car sale has been approved. Amount: {{amount}}. Please complete the payment and documentation. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-bag-check', 'is_active' => true],

            ['event_key' => 'user_welcome',
             'title'     => 'Welcome to {{company_name}}!',
             'body'      => 'Hi {{name}}, welcome to {{company_name}}! Your account has been created. If you have any questions, call us at {{company_phone}}.',
             'channel'   => 'push', 'icon' => 'bi-person-check', 'is_active' => true],

            ['event_key' => 'low_stock_alert',
             'title'     => 'Low Stock Alert',
             'body'      => 'Stock alert: one or more products have fallen below the reorder level. Please review and reorder to avoid shortages. – {{company_name}}',
             'channel'   => 'push', 'icon' => 'bi-box-seam', 'is_active' => true],
        ];
    }
}
