<?php

namespace Database\Seeders;

use App\Models\Affirmation;
use Illuminate\Database\Seeder;

class AffirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $affirmations = [
            ['text' => 'I am capable of achieving my academic goals.', 'author' => 'Mindful Scholar'],
            ['text' => 'Mistakes are just opportunities to learn and grow.', 'author' => 'Mindful Scholar'],
            ['text' => 'I am focused, productive, and efficient with my time.', 'author' => 'Mindful Scholar'],
            ['text' => 'Every day, I am becoming a better version of myself.', 'author' => 'Mindful Scholar'],
            ['text' => 'I choose to be positive and find joy in the learning process.', 'author' => 'Mindful Scholar'],
            ['text' => 'I am calm, confident, and prepared for my exams.', 'author' => 'Mindful Scholar'],
            ['text' => 'My potential to succeed is limitless.', 'author' => 'Mindful Scholar'],
            ['text' => 'I am worthy of success and happiness.', 'author' => 'Mindful Scholar'],
            ['text' => 'I take care of my mind, body, and soul.', 'author' => 'Mindful Scholar'],
            ['text' => 'I am resilient and can overcome any academic challenge.', 'author' => 'Mindful Scholar'],
        ];

        foreach ($affirmations as $affirmation) {
            Affirmation::create($affirmation);
        }
    }
}
