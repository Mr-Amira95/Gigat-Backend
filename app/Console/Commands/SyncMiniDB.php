<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMiniDB extends Command
{
    protected $signature = 'sync:to-mini-db';
    protected $description = 'Sync selected tables from Main DB to Mini DB';

    public function handle()
    {
        $tablesToSync = [
            'categories',
            'category_translations',
            'sub_categories',
            'sub_category_translations',
            'tags',
            'tag_translations',
            'professions',
            'profession_translations',
            'languages',
            'countries',
            'faqs',
            'faq_translations',
            'generals',
            'plans',
            'plan_translations',
            'users',
            'user_languages',
            'freelancers',
            'freelancer_certificates',
            'freelancer_cateogries',
            'services',
            'service_media',
            'service_tags',
            'service_translations',
            'portfolios',
            'portfolio_media',
            'portfolio_services',
            'plan_features',
            'plan_feature_translations',
            'wishlists',
        ];

        foreach ($tablesToSync as $table) {
            $this->info("Syncing table: $table");

            try {
                $mainIds = DB::table($table)->pluck('id')->toArray();
                $miniIds = DB::connection('mini_db')->table($table)->pluck('id')->toArray();

                $idsToDelete = array_diff($miniIds, $mainIds);

                if (!empty($idsToDelete)) {
                    DB::connection('mini_db')->table($table)->whereIn('id', $idsToDelete)->delete();
                    $this->info("🗑️ Deleted " . count($idsToDelete) . " obsolete rows from {$table}");
                }

                // $lastSync = now()->subDay();
                $rows = DB::table($table)
                    // ->where('updated_at', '>=', $lastSync)
                    // ->orWhere('created_at', '>=', $lastSync)
                    ->get();
            } catch (\Exception $e) {
                $this->error("❌ Error reading table {$table}: {$e->getMessage()}");
                continue;
            }

            foreach ($rows as $row) {
                try {
                    // write to mini_db
                    DB::connection('mini_db')->table($table)->updateOrInsert(
                        ['id' => $row->id],
                        (array) $row
                    );
                } catch (\Exception $e) {
                    $this->error("❌ Error syncing row ID {$row->id} in table {$table}: {$e->getMessage()}");
                    continue;
                }
            }
        }

        $this->info('✅ Sync completed.');
    }
}
