<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;
class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
      Faq::create([
            'question' => 'How do I apply for a national ID in Kenya?',
            'answer' => 'Visit a Huduma Centre or Registrar of Persons office with your birth certificate, a copy of your parent’s ID, and be 18 or older.',
        ]);
        Faq::create([
            'question' => 'What documents are needed for a birth certificate?',
            'answer' => 'You need a birth notification from the hospital, parents’ IDs, and a completed application form.',
        ]);
    }
   
}
