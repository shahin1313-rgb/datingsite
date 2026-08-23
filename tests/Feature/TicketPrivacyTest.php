<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TicketPrivacyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_only_sees_their_own_tickets(): void
    {
        $currentUser = User::factory()->create([
            'gender' => 'other',
            'city' => 'Test City',
        ]);

        $otherUser = User::factory()->create([
            'gender' => 'other',
            'city' => 'Test City',
        ]);

        $ownTicket = Ticket::create([
            'user_id' => $currentUser->id,
            'subject' => 'OWN-TICKET-SUBJECT',
            'message' => 'OWN-TICKET-MESSAGE',
        ]);

        $otherTicket = Ticket::create([
            'user_id' => $otherUser->id,
            'subject' => 'OTHER-TICKET-SUBJECT',
            'message' => 'OTHER-TICKET-MESSAGE',
        ]);

        $response = $this
            ->actingAs($currentUser)
            ->get(route('user.tickets.index'));

        $response
            ->assertOk()
            ->assertSee($ownTicket->subject)
            ->assertSee($ownTicket->message)
            ->assertDontSee($otherTicket->subject)
            ->assertDontSee($otherTicket->message)
            ->assertViewHas(
                'tickets',
                function ($tickets) use (
                    $currentUser,
                    $ownTicket,
                    $otherTicket
                ) {
                    return $tickets->contains('id', $ownTicket->id)
                        && ! $tickets->contains('id', $otherTicket->id)
                        && $tickets->every(
                            fn (Ticket $ticket) =>
                                (int) $ticket->user_id ===
                                (int) $currentUser->id
                        );
                }
            );
    }

    public function test_guest_cannot_open_the_ticket_list(): void
    {
        $this->get(route('user.tickets.index'))
            ->assertRedirect(route('login'));
    }
}