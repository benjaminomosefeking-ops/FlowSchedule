<?php

namespace Tests\Feature\Notification;

use App\Models\User;
use App\Notifications\CustomResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_reset_password_notification_is_sent()
    {
        Mail::fake();

        $user = User::factory()->create();

        $notification = new CustomResetPassword();
        $notification->token = 'test-token';

        $user->notify($notification);

        Mail::assertSent(CustomResetPassword::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Reset Your Password';
        });
    }

    /** @test */
    public function custom_reset_password_notification_contains_expected_content()
    {
        Mail::fake();

        $user = User::factory()->create();

        $notification = new CustomResetPassword();
        $notification->token = 'test-token';

        $user->notify($notification);

        Mail::assertSent(CustomResetPassword::class, function ($mail) use ($user) {
            $mail->build();

            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Reset Your Password' &&
                   $mail->visibleIn(['Hello!']) &&
                   $mail->visibleIn(['Reset Your Password']) &&
                   $mail->visibleIn(['expire in 60 minutes']) &&
                   $mail->visibleIn(['If you did not request a password reset']);
        });
    }
}