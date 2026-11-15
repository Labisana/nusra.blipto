<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\User;

function seedIncomingForReport(User $user, Customer $customer, Item $item): void
{
    test()->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'Dismantle',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-03',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 2, 'serials' => ['REP-1', 'REP-2']],
        ],
    ])->assertRedirect(route('inventory.incoming'));
}

test('teknisi cannot open the reports page', function () {
    $teknisi = User::factory()->teknisi()->create();

    $this->actingAs($teknisi)
        ->get(route('inventory.reports'))
        ->assertForbidden();
});

test('teknisi cannot export reports', function () {
    $teknisi = User::factory()->teknisi()->create();

    $this->actingAs($teknisi)
        ->get(route('inventory.reports.export'))
        ->assertForbidden();
});

test('spv and admin see incoming report rows with activity and customer type', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $customer = Customer::factory()->create(['tipe_pelanggan' => 'Broadband']);
    $item = Item::factory()->serial()->create();

    seedIncomingForReport($user, $customer, $item);

    $this->actingAs($user)
        ->get(route('inventory.reports'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('inventory/Reports')
            ->has('incoming', 1)
            ->where('incoming.0.activity', 'Dismantle')
            ->where('incoming.0.tipe_pelanggan', 'Broadband')
            ->where('incoming.0.customer', $customer->nama_pelanggan)
            ->where('incoming.0.qty', 2)
            ->where('incoming.0.serials', 'REP-1, REP-2')
            ->has('outgoing')
            ->has('stockRows')
            ->has('customers')
            ->has('itemTypes'));
})->with(['spv', 'admin']);

test('report filters narrow the returned rows', function () {
    $user = User::factory()->spv()->create();
    $customer = Customer::factory()->create();
    $serialItem = Item::factory()->serial()->create();
    $nonSerialItem = Item::factory()->create();

    seedIncomingForReport($user, $customer, $serialItem);

    $this->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-04',
        'items' => [
            ['item_id' => $nonSerialItem->id, 'quantity' => 7],
        ],
    ]);

    $this->actingAs($user)
        ->get(route('inventory.reports', ['item_type' => 'non-serial']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('incoming', 1)
            ->where('incoming.0.item', $nonSerialItem->name));

    $this->actingAs($user)
        ->get(route('inventory.reports', [
            'customer_id' => $customer->id,
            'from' => '2026-11-01',
            'to' => '2026-11-03',
        ]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('incoming', 1)
            ->where('incoming.0.item', $serialItem->name));
});

test('reports can be exported as csv', function (string $tab, string $expectedHeader) {
    $user = User::factory()->spv()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->serial()->create();

    seedIncomingForReport($user, $customer, $item);

    $response = $this->actingAs($user)->get(route('inventory.reports.export', ['tab' => $tab]));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

    expect($response->streamedContent())->toContain($expectedHeader);
})->with([
    ['masuk', 'Tanggal,Ref,"Jenis Transaksi"'],
    ['keluar', 'Tanggal,Ref,"Jenis Transaksi"'],
    ['stok', 'SKU,Barang,Jenis'],
]);
