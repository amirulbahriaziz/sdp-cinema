<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Cinema;
use App\Models\FoodItem;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\PriceTier;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\SeatTypePrice;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The graded core: FCFS seat-lock (atomic, DB-unique, no Redis) + atomic booking.
 */
class SeatLockBookingTest extends TestCase
{
    use RefreshDatabase;

    private Showtime $showtime;

    protected function setUp(): void
    {
        parent::setUp();

        // Option B pricing on a Classic tier: standard RM18.00, premium RM25.00.
        $tier = PriceTier::create(['name' => 'Classic', 'currency' => 'RM']);
        SeatTypePrice::create(['tier_id' => $tier->id, 'seat_type' => 'standard', 'price' => 1800]);
        SeatTypePrice::create(['tier_id' => $tier->id, 'seat_type' => 'premium', 'price' => 2500]);

        $hall = Hall::create([
            'cinema_id' => Cinema::create(['name' => 'TGV Suria KLCC', 'city' => 'KL', 'address' => 'x'])->id,
            'name' => 'Hall 1',
        ]);

        foreach (['D4', 'D5', 'D6'] as $code) {
            Seat::create([
                'hall_id' => $hall->id,
                'seat_code' => $code,
                'row_label' => 'D',
                'col_num' => (int) substr($code, 1),
                'type' => 'standard',
                'active' => true,
            ]);
        }

        $this->showtime = Showtime::create([
            'movie_id' => Movie::factory()->create(['title' => 'Venom: The Last Dance'])->id,
            'hall_id' => $hall->id,
            'tier_id' => $tier->id,
            'starts_at' => '2026-07-02T19:30:00+08:00',
            'ends_at' => '2026-07-02T21:19:00+08:00',
        ]);
    }

    /**
     * The headline invariant: two users racing for the SAME seat => exactly one
     * 201 (the hold) and one 409 (the loser). FCFS is arbitrated by the DB's
     * UNIQUE(showtime_id, seat_id), not by application check-then-write.
     */
    public function test_two_concurrent_locks_on_one_seat_yield_one_success_one_conflict(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $url = "/api/showtimes/{$this->showtime->id}/seats/D4/lock";

        Sanctum::actingAs($alice);
        $first = $this->postJson($url);

        Sanctum::actingAs($bob);
        $second = $this->postJson($url);

        // Exactly one winner, one conflict — order-independent assertion.
        $codes = [$first->getStatusCode(), $second->getStatusCode()];
        sort($codes);
        $this->assertSame([201, 409], $codes, 'Expected exactly one 201 and one 409.');

        // The DB must hold exactly ONE lock row for that seat+showtime.
        $this->assertSame(1, SeatLock::query()
            ->where('showtime_id', $this->showtime->id)
            ->whereHas('seat', fn ($q) => $q->where('seat_code', 'D4'))
            ->count());

        $first->assertJsonPath('data.status', 'held')
            ->assertJsonPath('data.ttl_seconds', 300);
        $second->assertJsonStructure(['message', 'errors' => ['seat']]);
    }

    public function test_holder_can_release_their_seat(): void
    {
        $alice = User::factory()->create();
        Sanctum::actingAs($alice);
        $url = "/api/showtimes/{$this->showtime->id}/seats/D4/lock";

        $this->postJson($url)->assertStatus(201);
        $this->deleteJson($url)
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'available');

        $this->assertDatabaseCount('seat_locks', 0);
    }

    public function test_non_holder_cannot_release_someone_elses_seat(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $url = "/api/showtimes/{$this->showtime->id}/seats/D4/lock";

        Sanctum::actingAs($alice);
        $this->postJson($url)->assertStatus(201);

        Sanctum::actingAs($bob);
        $this->deleteJson($url)->assertStatus(403);

        $this->assertDatabaseCount('seat_locks', 1);
    }

    public function test_lock_requires_authentication(): void
    {
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")
            ->assertStatus(401);
    }

    public function test_expired_hold_does_not_block_a_new_acquire(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();

        // A stale (expired) hold by Alice exists.
        SeatLock::create([
            'showtime_id' => $this->showtime->id,
            'seat_id' => Seat::where('seat_code', 'D4')->value('id'),
            'holder_id' => $alice->id,
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        Sanctum::actingAs($bob);
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'held');
    }

    /**
     * Confirm is one atomic transaction: booking confirmed, booking_seats
     * inserted (priced by lookup(tier, seat.type)), holds deleted, stub payment
     * attached, totals computed server-side in integer minor units (RM).
     */
    public function test_confirm_booking_is_atomic_and_prices_server_side(): void
    {
        $alice = User::factory()->create();
        Sanctum::actingAs($alice);

        // Hold two standard seats (1800 each).
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")->assertStatus(201);
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D5/lock")->assertStatus(201);

        $coke = FoodItem::create([
            'category' => 'beverages', 'name' => 'Regular Coke', 'description' => 'x',
            'price' => 850, 'discount_price' => null, 'currency' => 'RM', 'image_url' => 'https://x',
        ]);

        $res = $this->postJson('/api/bookings', [
            'showtime_id' => $this->showtime->id,
            'seat_codes' => ['D4', 'D5'],
            'food' => [['food_item_id' => $coke->id, 'qty' => 2]],
            'promo_code' => 'WELCOME10',
            'payment_method' => 'card',
        ]);

        // subtotal 3600, food 1700, service 5% of 5300 = 265, discount 10% of 3600 = 360
        // total = 3600 + 1700 + 265 - 360 = 5205
        $res->assertStatus(201)
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.subtotal', 3600)
            ->assertJsonPath('data.food_total', 1700)
            ->assertJsonPath('data.service_charge', 265)
            ->assertJsonPath('data.discount', 360)
            ->assertJsonPath('data.total', 5205)
            ->assertJsonPath('data.payment.status', 'paid')
            ->assertJsonPath('data.payment.amount', 5205);

        // Holds consumed, seats sold once, payment row exists.
        $this->assertDatabaseCount('seat_locks', 0);
        $this->assertSame(2, BookingSeat::where('showtime_id', $this->showtime->id)->count());
        $this->assertDatabaseHas('payments', ['amount' => 5205, 'status' => 'paid']);
        $this->assertSame('confirmed', Booking::first()->status);
    }

    public function test_cannot_confirm_a_seat_you_do_not_hold(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();

        // Bob holds D4; Alice tries to book it.
        Sanctum::actingAs($bob);
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")->assertStatus(201);

        Sanctum::actingAs($alice);
        $this->postJson('/api/bookings', [
            'showtime_id' => $this->showtime->id,
            'seat_codes' => ['D4'],
            'payment_method' => 'card',
        ])->assertStatus(409)
            ->assertJsonStructure(['message', 'errors' => ['seat_codes']]);

        // Nothing was sold; Bob's hold survives.
        $this->assertDatabaseCount('booking_seats', 0);
        $this->assertDatabaseCount('seat_locks', 1);
    }

    public function test_booked_seat_cannot_be_locked_again(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        Sanctum::actingAs($alice);

        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")->assertStatus(201);
        $this->postJson('/api/bookings', [
            'showtime_id' => $this->showtime->id,
            'seat_codes' => ['D4'],
            'payment_method' => 'card',
        ])->assertStatus(201);

        // The seat is sold (booking_seats) — a fresh lock attempt must 409.
        Sanctum::actingAs($bob);
        $this->postJson("/api/showtimes/{$this->showtime->id}/seats/D4/lock")
            ->assertStatus(409);
    }
}
