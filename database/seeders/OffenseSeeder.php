<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OffenseSeeder extends Seeder
{
    public function run(): void
    {
        $offenses = [
            [
                'offense_type' => 'Tardiness',
                'offense_description' => 'Being consistently late or arriving after the designated time for class or school activities.',
            ],
            [
                'offense_type' => 'Incomplete homework',
                'offense_description' => 'Failing to complete assigned homework or submitting unfinished tasks.',
            ],
            [
                'offense_type' => 'Disruptive behavior',
                'offense_description' => 'Engaging in actions that disturb or hinder the learning process, such as talking out of turn or being excessively noisy.',
            ],
            [
                'offense_type' => 'Bullying/harassment',
                'offense_description' => 'Intimidating, teasing, or harassing others through physical, verbal, or online means.',
            ],
            [
                'offense_type' => 'Cheating/plagiarism',
                'offense_description' => 'Using unauthorized materials, copying from others, or presenting someone else\'s work as one\'s own.',
            ],
            [
                'offense_type' => 'Truancy',
                'offense_description' => 'Unexcused absence from school or classes without proper authorization.',
            ],
            [
                'offense_type' => 'Substance abuse',
                'offense_description' => 'Using, possessing, or distributing drugs, alcohol, or other prohibited substances.',
            ],
            [
                'offense_type' => 'Physical aggression',
                'offense_description' => 'Engaging in physical harm or aggressive behavior towards others.',
            ],
            [
                'offense_type' => 'Theft',
                'offense_description' => 'Stealing or taking someone else\'s belongings without permission or rightful ownership.',
            ],
            [
                'offense_type' => 'Vandalism',
                'offense_description' => 'Deliberate destruction, defacement, or damage to school property or personal belongings.',
            ],
            [
                'offense_type' => 'Unauthorized technology use',
                'offense_description' => 'Using electronic devices or accessing restricted websites or apps without permission.',
            ],
            [
                'offense_type' => 'Defiance/resisting authority',
                'offense_description' => 'Disregarding or challenging the authority of teachers, administrators, or school staff.',
            ],
            [
                'offense_type' => 'Dress code violation',
                'offense_description' => 'Violating the established guidelines for appropriate attire and appearance.',
            ],
            [
                'offense_type' => 'Academic dishonesty',
                'offense_description' => 'Engaging in any form of dishonest behavior related to academic work, such as cheating or plagiarism.',
            ],
            [
                'offense_type' => 'Disrespectful language',
                'offense_description' => 'Using rude, offensive, or disrespectful language towards others.',
            ],
            [
                'offense_type' => 'Forgery/falsification',
                'offense_description' => 'Forging or falsifying signatures, documents, or official school records.',
            ],
            [
                'offense_type' => 'Cyberbullying',
                'offense_description' => 'Bullying or harassing others using digital platforms or online communication.',
            ],
            [
                'offense_type' => 'Gambling',
                'offense_description' => 'Participating in games of chance or betting, including gambling-related activities.',
            ],
            [
                'offense_type' => 'Destruction of property',
                'offense_description' => 'Intentionally damaging or destroying school property or the belongings of others.',
            ],
            [
                'offense_type' => 'Hate speech',
                'offense_description' => 'Using language or expressions that promote discrimination or express prejudice towards certain groups.',
            ],
            [
                'offense_type' => 'Excessive noise',
                'offense_description' => 'Creating disruptive or loud noises that interfere with the learning environment.',
            ],
            [
                'offense_type' => 'Skipping class',
                'offense_description' => 'Skipping or intentionally missing scheduled classes without valid reasons or authorization.',
            ],
            [
                'offense_type' => 'Academic misconduct',
                'offense_description' => 'Engaging in dishonest or unethical behavior related to academic activities.',
            ],
            [
                'offense_type' => 'Verbal harassment',
                'offense_description' => 'Harassing, intimidating, or using offensive language towards others.',
            ],
            [
                'offense_type' => 'Plagiarism',
                'offense_description' => 'Using someone else\'s work, ideas, or words without proper citation or attribution.',
            ],
            [
                'offense_type' => 'Inappropriate use of social media',
                'offense_description' => 'Posting inappropriate content or engaging in cyberbullying online.',
            ],
            [
                'offense_type' => 'Littering',
                'offense_description' => 'Improperly disposing of trash or waste materials on school premises.',
            ],
            [
                'offense_type' => 'Skipping school',
                'offense_description' => 'Missing an entire day of school without valid reasons or authorization.',
            ],
            [
                'offense_type' => 'Forgery/faking signatures',
                'offense_description' => 'Forging or faking signatures on documents or forms.',
            ],
            [
                'offense_type' => 'Discrimination',
                'offense_description' => 'Treating others unfairly based on race, gender, religion, or other characteristics.',
            ],
            [
                'offense_type' => 'Unauthorized use of school equipment',
                'offense_description' => 'Using school equipment without proper authorization.',
            ],
            [
                'offense_type' => 'Inappropriate physical contact',
                'offense_description' => 'Engaging in unwelcome or inappropriate physical contact.',
            ],
            [
                'offense_type' => 'Unauthorized materials',
                'offense_description' => 'Possessing or using prohibited materials on school premises.',
            ],
            [
                'offense_type' => 'Threats or intimidation',
                'offense_description' => 'Threatening or intimidating others through words or actions.',
            ],
            [
                'offense_type' => 'Use of profanity',
                'offense_description' => 'Using offensive or vulgar language.',
            ],
        ];

        foreach ($offenses as $offense) {
            DB::table('tbl_offense')->insert([
                'offense_type' => $offense['offense_type'],
                'offense_description' => $offense['offense_description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
