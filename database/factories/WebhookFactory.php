<?php

namespace Database\Factories;

use App\Enums\WebhookEvent;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Webhook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'url' => $this->faker->url(),
            'secret' => $this->faker->sha256(),
            'events' => [WebhookEvent::USER_CREATED->value],
            'enabled' => true,
        ];
    }
}