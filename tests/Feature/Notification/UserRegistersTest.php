<?php

namespace Tests\Feature\Notification;

use App\Models\User;
use App\Notifications\UserRegisters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserRegistersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_registers_notification_is_sent()
    {
        Mail::fake();

        $user = User::factory()->create();

        $user->notify(new UserRegisters());

        Mail::assertSent(UserRegisters::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Welcome to FlowSchedule!';
        });
    }

    /** @test */
    public function user_registers_notification_contains_expected_content()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'John Doe'
        ]);

        $user->notify(new UserRegisters());

        Mail::assertSent(UserRegisters::class, function ($mail) use ($user) {
            $mail->build();

            return $mail->hasTo($user->email) &&
                   $mail->subject === 'Welcome to FlowSchedule!' &&
                   $mail->visibleIn(['Hello John Doe,']) &&
                   $mail->visibleIn(['Welcome to FlowSchedule!']) &&
                   $mail->visibleIn(['Verify Your Email']) &&
                   $mail->visibleIn(['Thank you for joining our community!']);
        });
    }
}