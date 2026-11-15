<?php

use App\Models\BtsBsc;
use App\Models\Customer;
use App\Models\Item;
use App\Models\User;

test('customer visit is filled from the classification on create and update', function () {
    $user = User::factory()->admin()->create();
    $bts = BtsBsc::factory()->create();

    $this->actingAs($user)->post(route('customers.store'), [
        'nama_pelanggan' => 'PT Contoh Platinum',
        'tipe_pelanggan' => 'Broadband',
        'status' => 'Aktif',
        'klasifikasi_pelanggan' => 'Platinum',
        'bts_bsc_id' => $bts->id,
    ])->assertRedirect();

    $customer = Customer::query()->where('nama_pelanggan', 'PT Contoh Platinum')->sole();

    expect($customer->visit)->toBe('/1Bulan')
        ->and($customer->bts_bsc_id)->toBe($bts->id);

    $this->actingAs($user)->put(route('customers.update', $customer), [
        'nama_pelanggan' => 'PT Contoh Gold',
        'tipe_pelanggan' => 'Broadband',
        'status' => 'Aktif',
        'klasifikasi_pelanggan' => 'Gold',
        'bts_bsc_id' => $bts->id,
    ])->assertRedirect();

    expect($customer->fresh()->visit)->toBe('/3Bulan');
});

test('teknisi cannot manage customers', function () {
    $teknisi = User::factory()->teknisi()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($teknisi)->post(route('customers.store'), [
        'nama_pelanggan' => 'Tidak Boleh',
        'tipe_pelanggan' => 'Busol',
        'status' => 'Aktif',
    ])->assertForbidden();

    $this->actingAs($teknisi)
        ->put(route('customers.update', $customer), [
            'nama_pelanggan' => 'Diubah',
            'tipe_pelanggan' => 'Busol',
            'status' => 'Aktif',
        ])
        ->assertForbidden();

    $this->actingAs($teknisi)
        ->delete(route('customers.destroy', $customer))
        ->assertRedirect();

    expect($customer->fresh())->not->toBeNull();
});

test('an admin can delete an unused customer', function () {
    $admin = User::factory()->admin()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($admin)
        ->delete(route('customers.destroy', $customer))
        ->assertRedirect();

    expect(Customer::query()->find($customer->id))->toBeNull();
});

test('a customer with transactions cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $customer = Customer::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($admin)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 1],
        ],
    ]);

    $this->actingAs($admin)->post(route('inventory.outgoing.store'), [
        'activity' => 'Instalasi',
        'customer_id' => $customer->id,
        'transaction_date' => '2026-11-02',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 1],
        ],
    ]);

    $this->actingAs($admin)
        ->delete(route('customers.destroy', $customer))
        ->assertRedirect()
        ->assertSessionHas('toast');

    expect($customer->fresh())->not->toBeNull();
});

test('an admin can manage BTS/BSC data', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('bts.store'), [
        'nama' => 'BTS-TEST-01',
        'alamat' => 'Jl. Uji No. 1',
        'status' => 'Aktif',
    ])->assertRedirect();

    $bts = BtsBsc::query()->where('nama', 'BTS-TEST-01')->sole();

    $this->actingAs($admin)->put(route('bts.update', $bts), [
        'nama' => 'BTS-TEST-01',
        'alamat' => 'Jl. Uji No. 2',
        'status' => 'Non Aktif',
    ])->assertRedirect();

    expect($bts->fresh()->alamat)->toBe('Jl. Uji No. 2')
        ->and($bts->fresh()->status->value)->toBe('Non Aktif');

    $this->actingAs($admin)->delete(route('bts.destroy', $bts))->assertRedirect();

    expect(BtsBsc::query()->find($bts->id))->toBeNull();
});

test('a BTS/BSC used by a customer cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $bts = BtsBsc::factory()->create();
    Customer::factory()->create(['bts_bsc_id' => $bts->id]);

    $this->actingAs($admin)
        ->delete(route('bts.destroy', $bts))
        ->assertRedirect()
        ->assertSessionHas('toast');

    expect($bts->fresh())->not->toBeNull();
});

test('teknisi cannot manage BTS/BSC or stock items', function () {
    $teknisi = User::factory()->teknisi()->create();
    $bts = BtsBsc::factory()->create();
    $item = Item::factory()->create();

    $this->actingAs($teknisi)->post(route('bts.store'), [
        'nama' => 'BTS-NO',
        'alamat' => 'Jl. Tidak',
        'status' => 'Aktif',
    ])->assertForbidden();

    $this->actingAs($teknisi)->post(route('inventory.stock.store'), [
        'sku' => 'NO-1',
        'name' => 'Tidak Boleh',
        'type' => 'non-serial',
        'unit' => 'pcs',
    ])->assertForbidden();

    $this->actingAs($teknisi)
        ->delete(route('bts.destroy', $bts))
        ->assertRedirect();

    $this->actingAs($teknisi)
        ->delete(route('inventory.stock.destroy', $item))
        ->assertRedirect();

    expect($bts->fresh())->not->toBeNull()
        ->and($item->fresh())->not->toBeNull();
});

test('an admin can create, update and delete an unused stock item', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('inventory.stock.store'), [
        'sku' => 'NEW-SKU-1',
        'name' => 'Barang Baru',
        'type' => 'non-serial',
        'unit' => 'pcs',
        'location' => 'Rak Z1',
        'min_stock' => 3,
    ])->assertRedirect();

    $item = Item::query()->where('sku', 'NEW-SKU-1')->sole();

    $this->actingAs($admin)->put(route('inventory.stock.update', $item), [
        'sku' => 'NEW-SKU-1',
        'name' => 'Barang Diubah',
        'type' => 'non-serial',
        'unit' => 'box',
        'location' => 'Rak Z2',
        'min_stock' => 5,
    ])->assertRedirect();

    expect($item->fresh()->name)->toBe('Barang Diubah')
        ->and($item->fresh()->min_stock)->toBe(5);

    $this->actingAs($admin)
        ->delete(route('inventory.stock.destroy', $item))
        ->assertRedirect();

    expect(Item::query()->find($item->id))->toBeNull();
});

test('the stock page lists totals and serial tracking for every role', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $item = Item::factory()->serial()->create();

    $this->actingAs($user)->post(route('inventory.incoming.store'), [
        'activity' => 'FPB',
        'transaction_date' => '2026-11-01',
        'items' => [
            ['item_id' => $item->id, 'quantity' => 2, 'serials' => ['TRACK-1', 'TRACK-2']],
        ],
    ]);

    $this->actingAs($user)
        ->get(route('inventory.stock'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('inventory/Stock')
            ->has('items', 1)
            ->where('items.0.remaining', 2)
            ->has('serials', 2)
            ->has('itemTypes'));
})->with(['spv', 'admin', 'teknisi']);
