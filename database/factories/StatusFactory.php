<?php

namespace Database\Factories;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
{
    protected $model = Status::class;
    /**
     * 定义模型的默认状态。
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date_time = $this->faker->date. ' '.$this->faker->time;
        return [
            'user_id' =>$this->faker->randomElement(['1','2','3','4']),
            'content' => $this->faker->text(),
            'created_at' => $date_time,
            'updated_at' => $date_time
        ];
    }
}
