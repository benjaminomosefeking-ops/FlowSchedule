<?php

namespace Tests\Feature\Notification;

use App\Models\User;
use App\Notifications\UserVerified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserVerifiedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_verified_notification_is_sent()
    {
        Mail::fake();

        $user = User::factory()->create();

        $user->notify(new UserVerified());

        Mail::assertSent(UserVerified::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Email Verified Successfully';
        });
    }

    /** @test */
    public function user_verified_notification_contains_expected_content()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Jane Smith'
        ]);

        $user->notify(new UserVerified());

        Mail::assertSent(UserVerified::class, function ($mail) use ($user) {
            $mail->build();

            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Email Verified Successfully' &&
                   $mail->visibleIn(['Hello Jane Smith,']) &&
                   $mail->visibleIn(['Your email address has been successfully verified!']) &&
                   $mail->visibleIn(['Get Started']) &&
                   $mail->visibleIn(['Welcome to the FlowSchedule team!']);
        });
    }
}