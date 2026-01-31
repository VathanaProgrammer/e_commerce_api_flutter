<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $notificationTypes = [
            [
                'type' => 'promotion',
                'title' => 'Flash Sale Alert! 🎉',
                'message' => 'Get 15% off on all electronics. Limited time offer!',
                'action_url' => '/products?category=electronics'
            ],
            [
                'type' => 'system',
                'title' => 'Welcome to Our Store! 👋',
                'message' => 'Thank you for joining us. Explore our latest products and exclusive deals.',
                'action_url' => '/home'
            ],
            [
                'type' => 'promotion',
                'title' => 'New Arrivals 🆕',
                'message' => 'Check out our newest products just added to the store.',
                'action_url' => '/products?sort_by=created_at'
            ]
        ];

        foreach ($users->take(5) as $user) {
            foreach ($notificationTypes as $notification) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'action_url' => $notification['action_url'],
                    'is_read' => false
                ]);
            }
        }
    }
}
