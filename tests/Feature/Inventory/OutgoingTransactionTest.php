<?php

use App\Enums\SerialStatus;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SerialNumber;
use App\Models\StockTransaction;
use App\Models\User;

function receiveStock(User $user, Item $item, int $quantity, array $serials = []): StockTransaction
{
    test()->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => $quantity, 'serials' => $serials],
        ],
    ])->assertRedirect(route('inventory.incoming'));

    return StockTransaction::query()->latest('id')->firstOrFail();
}

test('an outgoing transaction issues the selected serial numbers to the customer', function () {
    $user = User::factory()->admin()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->serial()->create();

    receiveStock($user, $item, 2, ['OUT-1', 'OUT-2']);

    $response = $this->actingAs($user)->post(route('inventory.outgoing.store'), [
        'activity' => 'Instalasi',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-05',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 1, 'serials' => ['OUT-1']],
        ],
    ]);

    $response->assertRedirect(route('inventory.outgoing'));
    $response->assertSessionHas('toast');

    $issued = SerialNumber::query()->where('serial_number', 'OUT-1')->sole();
    $available = SerialNumber::query()->where('serial_number', 'OUT-2')->sole();

    expect($issued->status)->toBe(SerialStatus::Issued)
        ->and($issued->customer_id)->toBe($customer->id)
        ->and($issued->issued_at)->not->toBeNull()
        ->and($available->status)->toBe(SerialStatus::Available);
});

test('outgoing transactions require a customer', function () {
    $user = User::factory()->admin()->create();
    $item = Item::factory()->create();

    $this->actingAs($user)
        ->post(route('inventory.outgoing.store'), [
            'activity' => 'Maintenance',
            'customer_id' => null,
            'transaction_date' => '2026-11-05',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1],
            ],
        ])
        ->assertSessionHasErrors('customer_id');
});

test('non-serial stock cannot be issued beyond what is available', function () {
    $user = User::factory()->admin()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->create();

    receiveStock($user, $item, 3);

    $this->actingAs($user)
        ->post(route('inventory.outgoing.store'), [
            'activity' => 'Peminjaman',
            'customer_id' => $customer->id,
            'transaction_date' => '2026-11-05',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 4],
            ],
        ])
        ->assertSessionHasErrors('items');

    expect(StockTransaction::query()->where('direction', 'out')->count())->toBe(0);
});

test('a serial number cannot be issued twice', function () {
    $user = User::factory()->admin()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->serial()->create();

    receiveStock($user, $item, 1, ['ONCE-1']);

    $payload = [
        'activity' => 'Instalasi',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-05',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 1, 'serials' => ['ONCE-1']],
        ],
    ];

    $this->actingAs($user)->post(route('inventory.outgoing.store'), $payload)
        ->assertRedirect(route('inventory.outgoing'));

    $this->actingAs($user)->post(route('inventory.outgoing.store'), $payload)
        ->assertSessionHasErrors('items');
});

test('deleting an outgoing transaction returns its serial numbers to stock', function () {
    $user = User::factory()->spv()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->serial()->create();

    receiveStock($user, $item, 1, ['BACK-1']);

    $this->actingAs($user)->post(route('inventory.outgoing.store'), [
        'activity' => 'Instalasi',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-05',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 1, 'serials' => ['BACK-1']],
        ],
    ]);

    $outgoing = StockTransaction::query()->where('direction', 'out')->sole();

    $this->actingAs($user)
        ->delete(route('inventory.outgoing.destroy', $outgoing))
        ->assertRedirect(route('inventory.outgoing'));

    $serial = SerialNumber::query()->where('serial_number', 'BACK-1')->sole();

    expect($serial->status)->toBe(SerialStatus::Available)
        ->and($serial->customer_id)->toBeNull();
});

test('teknisi may create outgoing transactions but may not update or delete them', function () {
    $teknisi = User::factory()->teknisi()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->create();

    receiveStock($teknisi, $item, 5);

    $this->actingAs($teknisi)->post(route('inventory.outgoing.store'), [
        'activity' => 'Maintenance',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-05',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 2],
        ],
    ])->assertRedirect(route('inventory.outgoing'));

    $outgoing = StockTransaction::query()->where('direction', 'out')->sole();

    $this->actingAs($teknisi)
        ->put(route('inventory.outgoing.update', $outgoing), [
            'activity' => 'Maintenance',
            'customer_id' => $customer->id,
            'transaction_date' => '2026-11-06',
            'items' => [
                ['item_id' => $item->id, 'quantity' => 1],
            ],
        ])
        ->assertForbidden();

    $this->actingAs($teknisi)
        ->delete(route('inventory.outgoing.destroy', $outgoing))
        ->assertForbidden();
});
