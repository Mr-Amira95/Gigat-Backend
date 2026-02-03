<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
            ['title' => 'English', 'flag' => 'https://flagcdn.com/w320/gb.png'],
            ['title' => 'Arabic', 'flag' => 'https://flagcdn.com/w320/sa.png'],
            ['title' => 'French', 'flag' => 'https://flagcdn.com/w320/fr.png'],
            ['title' => 'Spanish', 'flag' => 'https://flagcdn.com/w320/es.png'],
            ['title' => 'German', 'flag' => 'https://flagcdn.com/w320/de.png'],
            ['title' => 'Italian', 'flag' => 'https://flagcdn.com/w320/it.png'],
            ['title' => 'Portuguese', 'flag' => 'https://flagcdn.com/w320/pt.png'],
            ['title' => 'Russian', 'flag' => 'https://flagcdn.com/w320/ru.png'],
            ['title' => 'Chinese', 'flag' => 'https://flagcdn.com/w320/cn.png'],
            ['title' => 'Japanese', 'flag' => 'https://flagcdn.com/w320/jp.png'],
            ['title' => 'Korean', 'flag' => 'https://flagcdn.com/w320/kr.png'],
            ['title' => 'Dutch', 'flag' => 'https://flagcdn.com/w320/nl.png'],
            ['title' => 'Turkish', 'flag' => 'https://flagcdn.com/w320/tr.png'],
            ['title' => 'Hindi', 'flag' => 'https://flagcdn.com/w320/in.png'],
            ['title' => 'Bengali', 'flag' => 'https://flagcdn.com/w320/bd.png'],
            ['title' => 'Urdu', 'flag' => 'https://flagcdn.com/w320/pk.png'],
            ['title' => 'Greek', 'flag' => 'https://flagcdn.com/w320/gr.png'],
            ['title' => 'Hebrew', 'flag' => 'https://flagcdn.com/w320/il.png'],
            ['title' => 'Swedish', 'flag' => 'https://flagcdn.com/w320/se.png'],
            ['title' => 'Polish', 'flag' => 'https://flagcdn.com/w320/pl.png'],
            ['title' => 'Thai', 'flag' => 'https://flagcdn.com/w320/th.png'],
            ['title' => 'Viettitlese', 'flag' => 'https://flagcdn.com/w320/vn.png'],
            ['title' => 'Persian', 'flag' => 'https://flagcdn.com/w320/ir.png'],
            ['title' => 'Indonesian', 'flag' => 'https://flagcdn.com/w320/id.png'],
            ['title' => 'Malay', 'flag' => 'https://flagcdn.com/w320/my.png'],
            ['title' => 'Romanian', 'flag' => 'https://flagcdn.com/w320/ro.png'],
            ['title' => 'Hungarian', 'flag' => 'https://flagcdn.com/w320/hu.png'],
            ['title' => 'Czech', 'flag' => 'https://flagcdn.com/w320/cz.png'],
            ['title' => 'Danish', 'flag' => 'https://flagcdn.com/w320/dk.png'],
            ['title' => 'Finnish', 'flag' => 'https://flagcdn.com/w320/fi.png'],
            ['title' => 'Ukrainian', 'flag' => 'https://flagcdn.com/w320/ua.png'],
            ['title' => 'Serbian', 'flag' => 'https://flagcdn.com/w320/rs.png'],
            ['title' => 'Croatian', 'flag' => 'https://flagcdn.com/w320/hr.png'],
            ['title' => 'Slovak', 'flag' => 'https://flagcdn.com/w320/sk.png'],
            ['title' => 'Bulgarian', 'flag' => 'https://flagcdn.com/w320/bg.png'],
            ['title' => 'Lithuanian', 'flag' => 'https://flagcdn.com/w320/lt.png'],
            ['title' => 'Latvian', 'flag' => 'https://flagcdn.com/w320/lv.png'],
            ['title' => 'Estonian', 'flag' => 'https://flagcdn.com/w320/ee.png'],
            ['title' => 'Slovenian', 'flag' => 'https://flagcdn.com/w320/si.png'],
            ['title' => 'Maltese', 'flag' => 'https://flagcdn.com/w320/mt.png'],
            ['title' => 'Filipino', 'flag' => 'https://flagcdn.com/w320/ph.png'],
            ['title' => 'Swahili', 'flag' => 'https://flagcdn.com/w320/ke.png'],
            ['title' => 'Afrikaans', 'flag' => 'https://flagcdn.com/w320/za.png'],
            ['title' => 'Norwegian', 'flag' => 'https://flagcdn.com/w320/no.png'],
            ['title' => 'Icelandic', 'flag' => 'https://flagcdn.com/w320/is.png'],
        ]);
    }
}
