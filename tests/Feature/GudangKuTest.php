<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GudangKuTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = new User([
            'name' => ucfirst($role).' Test',
            'email' => $role.'@example.test',
            'password' => 'password123',
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }

    private function makeProduct(int $stock = 10): Product
    {
        $category = Category::create(['name' => 'Kategori Uji']);

        $product = new Product([
            'sku' => 'TST-001',
            'name' => 'Produk Uji',
            'price' => 10000,
            'min_stock' => 3,
            'category_id' => $category->id,
        ]);
        $product->stock = $stock;
        $product->save();

        return $product;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/products')->assertRedirect('/login');
    }

    public function test_register_always_creates_staff_even_if_role_is_sent(): void
    {
        $this->post('/register', [
            'name' => 'Pendaftar Baru',
            'email' => 'baru@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // upaya menaikkan hak akses
        ])->assertRedirect('/dashboard');

        $this->assertSame('staff', User::where('email', 'baru@example.test')->value('role'));
    }

    public function test_login_success_and_wrong_password(): void
    {
        $this->makeUser('admin');

        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'password123'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = $this->makeUser('staff');

        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_staff_cannot_open_admin_pages(): void
    {
        $staff = $this->makeUser('staff');

        $this->actingAs($staff)->get('/categories')->assertForbidden();
        $this->actingAs($staff)->get('/suppliers')->assertForbidden();
        $this->actingAs($staff)->get('/users')->assertForbidden();
    }

    public function test_staff_cannot_delete_product_but_admin_can(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->makeUser('staff'))
            ->delete('/products/'.$product->id)->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $this->actingAs($this->makeUser('admin'))
            ->delete('/products/'.$product->id)->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_crud_category(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post('/categories', ['name' => 'Baru'])->assertRedirect('/categories');
        $category = Category::where('name', 'Baru')->firstOrFail();

        $this->actingAs($admin)->put('/categories/'.$category->id, ['name' => 'Diubah'])->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Diubah']);

        $this->actingAs($admin)->delete('/categories/'.$category->id)->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->makeUser('admin'))
            ->delete('/categories/'.$product->category_id)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }

    public function test_stock_in_increases_and_out_decreases_stock(): void
    {
        $staff = $this->makeUser('staff');
        $product = $this->makeProduct(10);

        $this->actingAs($staff)->post('/transactions', [
            'product_id' => $product->id, 'type' => 'in', 'quantity' => 5,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect('/transactions');
        $this->assertSame(15, $product->fresh()->stock);

        $this->actingAs($staff)->post('/transactions', [
            'product_id' => $product->id, 'type' => 'out', 'quantity' => 4,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect('/transactions');
        $this->assertSame(11, $product->fresh()->stock);
        $this->assertSame(2, StockTransaction::count());
    }

    public function test_stock_out_cannot_exceed_available_stock(): void
    {
        $product = $this->makeProduct(3);

        $this->actingAs($this->makeUser('staff'))->post('/transactions', [
            'product_id' => $product->id, 'type' => 'out', 'quantity' => 99,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('quantity');

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(0, StockTransaction::count());
    }

    public function test_html_tags_are_stripped_from_input(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post('/categories', [
            'name' => '<script>alert(1)</script>Aman',
        ]);

        $this->assertDatabaseMissing('categories', ['name' => '<script>alert(1)</script>Aman']);
        $this->assertDatabaseHas('categories', ['name' => 'alert(1)Aman']);
    }

    public function test_output_is_escaped_in_views(): void
    {
        // Data berbahaya yang sudah terlanjur ada di database tetap di-escape oleh Blade.
        Category::create(['name' => '<b id="x">tebal</b>']);

        $this->actingAs($this->makeUser('admin'))
            ->get('/categories')
            ->assertSee('&lt;b id=&quot;x&quot;&gt;tebal&lt;/b&gt;', false);
    }

    public function test_logout_ends_session(): void
    {
        $this->actingAs($this->makeUser('staff'))->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
