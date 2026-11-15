<?php

use App\Enums\SerialStatus;
use App\Models\Item;
use App\Models\SerialNumber;
use App\Models\StockTransaction;
use App\Models\User;

test('guests cannot access the incoming page', function () {
    $this->get(route('inventory.incoming'))->assertRedirect(route('login'));
});

test('all roles can view the incoming page', function (string $state) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)
        ->get(route('inventory.incoming'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('inventory/Incoming')
            ->has('transactions')
            ->has('customers')
            ->has('items')
            ->has('activities'));
})->with(['spv', 'admin', 'teknisi']);

test('an FPB transaction stores serial and non-serial lines without a customer', function () {
    $user = User::factory()->admin()->create();
    $serialItem = Item::factory()->serial()->create();
    $nonSerialItem = Item::factory()->create();

    $response = $this->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'customer_id' => null,
        'transaction_date' => '2026-11-01',
        'note' => 'Pengadaan rutin',
        'items' => [
            ['item_id' => $serialItem->id, 'quantity' => 2, 'serials' => ['SN-100', 'SN-101']],
            ['item_id' => $nonSerialItem->id, 'quantity' => 10, 'serials' => []],
        ],
    ]);

    $response->assertRedirect(route('inventory.incoming'));
    $response->assertSessionHas('toast');

    $transaction = StockTransaction::query()->sole();

    expect($transaction->activity->value)->toBe('FPB')
        ->and($transaction->customer_id)->toBeNull()
        ->and($transaction->user_id)->toBe($user->id)
        ->and($transaction->items)->toHaveCount(2);

    expect(SerialNumber::query()->where('item_id', $serialItem->id)->pluck('status')->all())
        ->each->toBe(SerialStatus::Available);
});

test('a dismantle transaction requires a customer', function () {
    $user = User::factory()->admin()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.incoming.store'), [
            'activity' => 'Dismantle',
            'customer_id' => null,
            'transaction_date' => '2026-11-01',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1],
            ],
        ])
        ->assertSessionHasErrors('customer_id');

    expect(StockTransaction::query()->count())->toBe(0);
});

test('serial quantities must match the received quantity', function () {
    $user = User::factory()->admin()->create();
    $item = Item::factory()->serial()->create();

    $this->actingAs($user)
        ->post(route('inventory.incoming.store'), [
            'activity' => 'FPB',
            'transaction_date' => '2026-11-01',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 2, 'serials' => ['HANYA-SATU']],
            ],
        ])
        ->assertSessionHasErrors('items.0.serials');
});

test('updating an incoming transaction replaces its stock lines', function () {
    $user = User::factory()->admin()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 5],
        ],
    ]);

    $transaction = StockTransaction::query()->sole();

    $this->actingAs($user)
        ->put(route('inventory.incoming.update', $transaction), [
            'activity' => 'FPB',
            'transaction_date' => '2026-11-02',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 8],
            ],
        ])
        ->assertRedirect(route('inventory.incoming'));

    $transaction->refresh();

    expect($transaction->items)->toHaveCount(1)
        ->and($transaction->items->first()->quantity)->toBe(8);
});

test('deleting an incoming transaction removes the created serial numbers', function () {
    $user = User::factory()->spv()->create();
    $item = Item::factory()->serial()->create();

    $this->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 2, 'serials' => ['DEL-1', 'DEL-2']],
        ],
    ]);

    $transaction = StockTransaction::query()->sole();

    $this->actingAs($user)
        ->delete(route('inventory.incoming.destroy', $transaction))
        ->assertRedirect(route('inventory.incoming'));

    expect(StockTransaction::query()->count())->toBe(0)
        ->and(SerialNumber::query()->count())->toBe(0);
});

test('teknisi may create incoming transactions but may not update or delete them', function () {
    $teknisi = User::factory()->teknisi()->create();
    $item = Item::factory()->create();

    $this->actingAs($teknisi)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 3],
        ],
    ])->assertRedirect(route('inventory.incoming'));

    $transaction = StockTransaction::query()->sole();

    $this->actingAs($teknisi)
        ->put(route('inventory.incoming.update', $transaction), [
            'activity' => 'FPB',
            'transaction_date' => '2026-11-01',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 4],
            ],
        ])
        ->assertForbidden();

    $this->actingAs($teknisi)
        ->delete(route('inventory.incoming.destroy', $transaction))
        ->assertForbidden();

    expect($transaction->fresh())->not->toBeNull();
});
