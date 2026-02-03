<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create general notifications
        $generalNotification1 = Notification::create([
            'action' => 'general_announcement',
            'is_general' => true,
            'icon' => 'announcement',
        ]);

        $generalNotification1->translations()->createMany([
            [
                'language' => 'en',
                'title' => 'Welcome to Our Platform',
                'description' => 'We are excited to have you join our community!'
            ],
            [
                'language' => 'ar',
                'title' => 'مرحبا بكم في منصتنا',
                'description' => 'نحن متحمسون لانضمامك إلى مجتمعنا!'
            ],
        ]);

        $generalNotification2 = Notification::create([
            'action' => 'new_feature',
            'is_general' => true,
            'icon' => 'star',
        ]);

        $generalNotification2->translations()->createMany([
            [
                'language' => 'en',
                'title' => 'New Feature Available',
                'description' => 'Check out our latest feature that helps you connect better!'
            ],
            [
                'language' => 'ar',
                'title' => 'ميزة جديدة متاحة',
                'description' => 'تحقق من أحدث ميزة لدينا التي تساعدك على التواصل بشكل أفضل!'
            ],
        ]);

        // Create user-specific notifications
        $userSpecificNotification = Notification::create([
            'action' => 'new_message',
            'is_general' => false,
            'icon' => 'message',
        ]);

        $userSpecificNotification->translations()->createMany([
            [
                'language' => 'en',
                'title' => 'New Message Received',
                'description' => 'You have received a new message from our support team.'
            ],
            [
                'language' => 'ar',
                'title' => 'تم استلام رسالة جديدة',
                'description' => 'لقد تلقيت رسالة جديدة من فريق الدعم لدينا.'
            ],
        ]);

        // Assign user-specific notifications to some users
        $users = User::take(2)->get();
        foreach ($users as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $userSpecificNotification->id,
                'is_read' => false,
            ]);
        }

        // Create another user-specific notification
        $serviceNotification = Notification::create([
            'action' => 'service_update',
            'action_id' => 1, // Assuming service ID 1 exists
            'is_general' => false,
            'icon' => 'update',
        ]);

        $serviceNotification->translations()->createMany([
            [
                'language' => 'en',
                'title' => 'Service Updated',
                'description' => 'One of your followed services has been updated.'
            ],
            [
                'language' => 'ar',
                'title' => 'تم تحديث الخدمة',
                'description' => 'تم تحديث إحدى الخدمات التي تتابعها.'
            ],
        ]);

        // Assign to specific users
        foreach ($users->take(2) as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $serviceNotification->id,
                'is_read' => false,
            ]);
        }
    }
}
