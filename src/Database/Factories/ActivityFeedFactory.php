<?php

namespace Skillcraft\ActivityFeed\Database\Factories;

use Botble\ACL\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Skillcraft\ActivityFeed\Models\ActivityFeed>
 */
class ActivityFeedFactory extends Factory
{
    protected $model = ActivityFeed::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'owner_type' => User::class,
            'module_id' => null,
            'module_type' => null,
            'title' => $this->faker->title,
            'message' => $this->faker->sentence,
            'is_private' => false,
        ];
    }
    
    public function forPrivate(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    public function forPublic(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
        ]);
    }

    public function forOwner(Model $owner): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $owner->getKey(),
            'owner_type' => $owner->getMorphClass(),
        ]);
    }

    public function forModule(Model $module): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => $module->getKey(),
            'module_type' => $module->getMorphClass(),
        ]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
        ]);
    }

    public function withMessage(string $message): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => $message,
        ]);
    }
}
