<?php

use App\Filament\Resources\BenefitSlideResource;
use App\Filament\Resources\BenefitSlideResource\Pages\CreateBenefitSlide;
use App\Filament\Resources\BenefitSlideResource\Pages\EditBenefitSlide;
use App\Filament\Resources\BenefitSlideResource\Pages\ListBenefitSlides;
use App\Models\BenefitSlide;
use App\Models\User;
use Livewire\Livewire;

function adminUser(): User
{
    return User::factory()->create(['email' => 'mahikomohammed@gmail.com']);
}

test('guests are redirected to the admin login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('non-admin users cannot access the admin panel', function () {
    $user = User::factory()->create(['email' => 'someone-else@example.com']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

test('admin can view the benefit slides list', function () {
    BenefitSlide::factory()->count(3)->create();

    $this->actingAs(adminUser())
        ->get(BenefitSlideResource::getUrl('index'))
        ->assertOk();
});

test('admin can create a benefit slide', function () {
    $this->actingAs(adminUser());

    Livewire::test(CreateBenefitSlide::class)
        ->fillForm([
            'title' => 'Low Capital',
            'body_text' => 'A machine costs a fraction of a shop.',
            'sort_order' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect(BenefitSlideResource::getUrl('index'));

    $this->assertDatabaseHas('benefit_slides', [
        'title' => 'Low Capital',
        'body_text' => 'A machine costs a fraction of a shop.',
        'sort_order' => 1,
    ]);
});

test('admin can edit a benefit slide', function () {
    $slide = BenefitSlide::factory()->create(['title' => 'Original Title']);

    $this->actingAs(adminUser());

    Livewire::test(EditBenefitSlide::class, ['record' => $slide->getRouteKey()])
        ->fillForm([
            'title' => 'Updated Title',
            'body_text' => $slide->body_text,
            'sort_order' => $slide->sort_order,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(BenefitSlideResource::getUrl('index'));

    expect($slide->refresh()->title)->toBe('Updated Title');
});

test('admin can delete a benefit slide', function () {
    $slide = BenefitSlide::factory()->create();

    $this->actingAs(adminUser());

    Livewire::test(ListBenefitSlides::class)
        ->callTableAction('delete', $slide);

    $this->assertModelMissing($slide);
});

test('admin can reorder benefit slides', function () {
    $first = BenefitSlide::factory()->create(['sort_order' => 1]);
    $second = BenefitSlide::factory()->create(['sort_order' => 2]);

    $this->actingAs(adminUser());

    Livewire::test(ListBenefitSlides::class)
        ->call('reorderTable', [$second->getKey(), $first->getKey()]);

    expect($second->refresh()->sort_order)->toBeLessThan($first->refresh()->sort_order);
});

test('creating a benefit slide at an occupied position shifts the rest down', function () {
    $first = BenefitSlide::factory()->create(['sort_order' => 1]);
    $second = BenefitSlide::factory()->create(['sort_order' => 2]);

    $this->actingAs(adminUser());

    Livewire::test(CreateBenefitSlide::class)
        ->fillForm([
            'title' => 'Inserted Slide',
            'body_text' => 'Some body text here.',
            'sort_order' => 1,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $inserted = BenefitSlide::where('title', 'Inserted Slide')->firstOrFail();

    expect($inserted->sort_order)->toBe(1)
        ->and($first->refresh()->sort_order)->toBe(2)
        ->and($second->refresh()->sort_order)->toBe(3);

    expect(BenefitSlide::pluck('sort_order')->sort()->values()->all())->toBe([1, 2, 3]);
});

test('editing a benefit slide to an occupied position reshuffles the rest', function () {
    $first = BenefitSlide::factory()->create(['sort_order' => 1]);
    $second = BenefitSlide::factory()->create(['sort_order' => 2]);
    $third = BenefitSlide::factory()->create(['sort_order' => 3]);

    $this->actingAs(adminUser());

    Livewire::test(EditBenefitSlide::class, ['record' => $third->getRouteKey()])
        ->fillForm([
            'title' => $third->title,
            'body_text' => $third->body_text,
            'sort_order' => 1,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($third->refresh()->sort_order)->toBe(1)
        ->and($first->refresh()->sort_order)->toBe(2)
        ->and($second->refresh()->sort_order)->toBe(3);

    expect(BenefitSlide::pluck('sort_order')->sort()->values()->all())->toBe([1, 2, 3]);
});

test('admin can bulk delete benefit slides', function () {
    $slides = BenefitSlide::factory()->count(3)->create();

    $this->actingAs(adminUser());

    Livewire::test(ListBenefitSlides::class)
        ->callTableBulkAction('delete', $slides);

    $slides->each(fn (BenefitSlide $slide) => $this->assertModelMissing($slide));
});

test('the homepage shows a fallback when there are no benefit slides', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Benefit highlights are coming soon.')
        ->assertDontSee('data-feature-slide', false);
});

test('the homepage is never served from the browser cache', function () {
    $response = $this->get('/')->assertOk();

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

test('the homepage renders every benefit slide in sort order', function () {
    BenefitSlide::factory()->create(['title' => 'Zeta', 'sort_order' => 2]);
    BenefitSlide::factory()->create(['title' => 'Alpha', 'sort_order' => 1]);

    $response = $this->get('/');

    $response->assertOk();
    $content = $response->getContent();

    expect(strpos($content, 'Alpha'))->toBeLessThan(strpos($content, 'Zeta'));
});
