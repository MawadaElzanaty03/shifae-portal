<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    // تعريف المتغيرات اللي هنحتاجها لتمرير بيانات الموظف وكلمة المرور
    public  $employee;
    public $tempPassword;

    // الكونستراكتور: بيستقبل بيانات الموظف وكلمة المرور المؤقتة وقت إنشاء الكلاس
    public function __construct(User $employee, $tempPassword)
    {
        $this->employee = $employee;
        $this->tempPassword = $tempPassword;
    }

    // تحديد عنوان البريد الإلكتروني (Subject) اللي هيوصل للموظف
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'مرحباً بك في فريق العمل - مركز شفائي',
        );
    }

    // تحديد مسار ملف العرض (View) اللي فيه تصميم محتوى الإيميل
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_employee', 
                );
    }

    public function attachments(): array
    {
        return [];
    }
}
