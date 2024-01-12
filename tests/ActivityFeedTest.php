<?php

namespace Skillcraft\ActivityFeed\Tests\Unit;

use Tests\TestCase;
use Botble\ACL\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Botble\Base\Supports\MacroableModels;
use Botble\ACL\Services\ActivateUserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Skillcraft\ActivityFeed\Facades\ActivityFeedHelper;
use Skillcraft\ActivityFeed\Supports\ActivityFeedHookManager;

class ActivityFeedTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    
    public User $owner;

    public User $module;

    public MacroableModels $macroModel;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->macroModel = new MacroableModels();

        $this->artisan('cms:plugin:activate skillcraft-core', []);

        $this->artisan('cms:plugin:activate activity-feed', []);

        ActivityFeedHookManager::registerHooks(User::class, 'user');

        ActivityFeed::truncate();

        $this->owner = $this->createUser(true);

        $this->module = $this->createUser();
    }

    public function testGetActivityFeedItems(): void
    {
        ActivityFeed::factory()->count(20)->create();
        
        $result = ActivityFeedHelper::getActivityFeedItems();

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(10, $result->count());
    }
    
    public function testGetPrivateActivityFeedItems(): void
    {
        ActivityFeed::factory()->forPrivate()->count(5)->create();

        $result = ActivityFeedHelper::getActivityFeedItems(null, null, true);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(5, $result->count());
    }
    
    public function testGetCustomLimitActivityFeedItems(): void
    {
        ActivityFeed::factory()->forPublic()->count(50)->create();
       
        $result = ActivityFeedHelper::getActivityFeedItems(null, null, false, 5, 10);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(5, $result->count());

        $this->assertEquals(11, $result->first()->id);
    }

    public function testGetPublicActivityFeedItems(): void
    {
        ActivityFeed::factory()->forPublic()->count(10)->create();

        $result = ActivityFeedHelper::getActivityFeedItems(null, null, false);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(10, $result->count());
    }

    public function testGetActivityFeedItemsWithOwner(): void
    {
        ActivityFeed::factory()->forOwner($this->owner)->count(5)->create();

        $result = ActivityFeedHelper::getActivityFeedItems($this->owner);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(5, $result->count());

        $this->assertEquals($this->owner->id, $result->first()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->first()->owner_type);

        $this->assertEquals($this->owner->id, $result->last()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->last()->owner_type);
    }

    public function testGetActivityFeedItemsWithCustomModule(): void
    {
        ActivityFeed::factory()->forModule($this->module)->count(5)->create();

        $result = ActivityFeedHelper::getActivityFeedItems(null, $this->module);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(5, $result->count());

        $this->assertEquals($this->module->id, $result->first()->module_id);

        $this->assertEquals($this->module->getMorphClass(), $result->first()->module_type);

        $this->assertEquals($this->module->id, $result->last()->module_id);

        $this->assertEquals($this->module->getMorphClass(), $result->last()->module_type);
    }

    public function testGetActivityFeedItemsWithCustomLimitAndOffset(): void
    {
        $limit = 10; // Set the custom limit
        $offset = 5; // Set the custom offset

        ActivityFeed::truncate();

        ActivityFeed::factory()->forPublic()->count(20)->create();

        $result = ActivityFeedHelper::getActivityFeedItems(null, null, false, $limit, $offset);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals($limit, $result->count());

        $this->assertEquals($offset + 1, $result->first()->id);

        $this->assertEquals($limit + $offset, $result->last()->id);
    }

    public function testGetActivityFeedItemsWithCustomOwnerAndModule(): void
    {
        ActivityFeed::factory()->forOwner($this->owner)->forModule($this->module)->count(5)->create();

        $result = ActivityFeedHelper::getActivityFeedItems($this->owner, $this->module);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(5, $result->count());

        $this->assertEquals($this->owner->id, $result->first()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->first()->owner_type);

        $this->assertEquals($this->owner->id, $result->last()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->last()->owner_type);

        $this->assertEquals($this->module->id, $result->first()->module_id);

        $this->assertEquals($this->module->getMorphClass(), $result->first()->module_type);

        $this->assertEquals($this->module->id, $result->last()->module_id);

        $this->assertEquals($this->module->getMorphClass(), $result->last()->module_type);
    }

    public function testGetActivityFeedItemsWithCustomLimitOffsetOwnerAndModule(): void
    {
        $limit = 10; // Set the custom limit
        $offset = 5; // Set the custom offset

        ActivityFeed::factory()->forPublic()->forOwner($this->owner)->forModule($this->module)->count(20)->create();

        $result = ActivityFeedHelper::getActivityFeedItems($this->owner, $this->module, false, $limit, $offset);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals($limit, $result->count());

        $this->assertEquals($offset + 1, $result->first()->id);

        $this->assertEquals($limit + $offset, $result->last()->id);

        $this->assertEquals($this->owner->id, $result->first()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->first()->owner_type);

        $this->assertEquals($this->owner->id, $result->last()->owner_id);

        $this->assertEquals($this->owner->getMorphClass(), $result->last()->owner_type);

        $this->assertEquals($this->module->id, $result->first()->module_id);

        $this->assertEquals($this->module->getMorphClass(), $result->first()->module_type);

        $this->assertEquals($this->module->id, $result->last()->module_id);
    }

    public function testAddMacroHooks()
    {
        (new ActivityFeedHookManager)->load();

        $this->owner->ownerActivityFeed();

        $this->owner->moduleActivityFeed();

        $this->module->addActivityFeedItem($this->owner, 'Test 1', 'Some Message');

        $this->owner->addOwnerActivityFeedItem('Test 1', 'Some Message');

        $this->owner->getOwnerActivityFeedItems();

        $this->module->getModuleActivityFeedItems();

        $this->assertTrue(true);
    }

    //Tests validation runs
    public function testAddingActivityFeedItemWithExceptions()
    {
        (new ActivityFeedHookManager)->load();

        $this->expectException(ValidationException::class);

        $title = $this->faker->sentences(asText: true);

        $message = $this->faker->sentences(asText: true);

        $this->module->addActivityFeedItem($this->createUser(false), $title, $message);
    }

    public function testAddingActivityFeedItem()
    {
        $this->module->addActivityFeedItem($this->createUser(false), 'Test 1', 'A Message from a rogue module and mystery owner');
        
        $this->module->addActivityFeedItem($this->owner, 'Test 2', 'A message from a module and expected owner');

        $item = $this->module->getModuleActivityFeedItems($this->owner);

        $this->assertInstanceOf(Collection::class, $item);

        $this->assertEquals(1, $item->count());

        $this->assertEquals($this->module->id, $item->first()->module_id);


        $this->owner->addOwnerActivityFeedItem('Test 3', 'A known owner message');

        $item = $this->owner->getOwnerActivityFeedItems();

        $this->assertInstanceOf(Collection::class, $item);

        $this->assertEquals(2, $item->count());

        $this->assertEquals($this->owner->id, $item->first()->owner_id);
    }

    private function createUser(bool $truncate = true): User
    {
        if ($truncate) {
            Schema::disableForeignKeyConstraints();
            User::truncate();
        }
        
        $user = new User();
        $user->forceFill([
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->companyEmail(),
            'username' => $this->faker->userName(),
            'password' => Hash::make('12345678'),
            'super_user' => 1,
            'manage_supers' => 1,
        ]);
        $user->save();

        app(ActivateUserService::class)->activate($user);

        return $user;
    }
}
