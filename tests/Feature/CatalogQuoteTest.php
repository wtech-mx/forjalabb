<?php

namespace Tests\Feature;

use App\Models\CatalogProduct;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogQuoteTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Super admin', 'slug' => Role::SUPER_ADMIN]);
        $user->roles()->attach($role);

        return $user;
    }

    private function product(): CatalogProduct
    {
        return CatalogProduct::create(['name' => 'Termo personalizado', 'slug' => 'termo-personalizado', 'public_price' => 250]);
    }

    public function test_form_shows_the_default_package_price_and_preserves_invalid_input(): void
    {
        $product = $this->product();
        $product->salePackages()->create(['name' => 'Individual', 'quantity' => 1, 'public_price' => 250]);
        $product->salePackages()->create(['name' => 'Set de cuatro', 'quantity' => 4, 'public_price' => 900, 'is_default' => true]);
        $this->actingAs($this->administrator())->get(route('admin.catalog-quotes.index'))
            ->assertOk()->assertSee('Set de cuatro (4 piezas)')->assertSee('$900.00 MXN');

        $this->from(route('admin.catalog-quotes.index'))->post(route('admin.catalog-quotes.pdf'), [
            'items' => [$product->id => ['product_id' => $product->id, 'price' => 800, 'minimum_quantity' => 0]],
        ])->assertRedirect(route('admin.catalog-quotes.index'))->assertSessionHasErrors('items.'.$product->id.'.minimum_quantity');
        $this->get(route('admin.catalog-quotes.index'))->assertOk()->assertSee('value="800"', false);
    }

    public function test_pdf_download_does_not_change_catalog_prices(): void
    {
        $product = $this->product();
        $product->update(['cover_photo_path' => 'images/catalog/aguacate-teq.jpeg', 'image_path' => 'images/catalog/no-existe.jpg']);
        $response = $this->actingAs($this->administrator())->post(route('admin.catalog-quotes.pdf'), [
            'recipient' => 'Cliente de prueba',
            'notes' => 'Vigencia de 15 días.',
            'items' => [['product_id' => $product->id, 'price' => '199.50', 'minimum_quantity' => 12]],
        ]);
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('/Subtype /Image', $response->getContent());
        $this->assertSame('250.00', $product->fresh()->public_price);
    }

    public function test_pdf_template_compares_package_prices_and_escapes_notes(): void
    {
        $html = view('admin.catalog-quotes.pdf', [
            'rows' => [['product' => $this->product(), 'presentation' => 'Set de cuatro (4 piezas)', 'public_price' => 900, 'price' => 800, 'minimum_quantity' => 12]],
            'recipient' => 'Cliente', 'notes' => '<script>ejemplo</script>', 'generatedAt' => now(),
        ])->render();
        foreach (['$900.00', '$800.00', '12 piezas', 'Set de cuatro (4 piezas)', '&lt;script&gt;'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_invalid_and_duplicate_products_are_rejected(): void
    {
        $product = $this->product();
        $this->actingAs($this->administrator());
        $this->post(route('admin.catalog-quotes.pdf'), [])->assertRedirect()->assertSessionHasErrors('items');
        $this->post(route('admin.catalog-quotes.pdf'), ['items' => [
            ['product_id' => $product->id, 'price' => -1, 'minimum_quantity' => 0],
            ['product_id' => $product->id, 'price' => 5.555, 'minimum_quantity' => 1.5],
            ['product_id' => 999999, 'price' => 10, 'minimum_quantity' => 1],
        ]])->assertRedirect()->assertSessionHasErrors([
            'items.0.product_id', 'items.0.price', 'items.0.minimum_quantity',
            'items.1.price', 'items.1.minimum_quantity', 'items.2.product_id',
        ]);
    }

    public function test_only_users_with_catalog_access_can_use_the_module(): void
    {
        $this->get(route('admin.catalog-quotes.index'))->assertRedirect(route('login'));
        $this->post(route('admin.catalog-quotes.pdf'), [])->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.catalog-quotes.index'))->assertForbidden();
        $this->post(route('admin.catalog-quotes.pdf'), [])->assertForbidden();
    }
}
