<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportOldRingi extends Command
{
    protected $signature = 'ringi:import-csv';

    protected $description = '旧稟議CSV取込';

    public function handle()
    {
        $path = storage_path('app/draft.csv');

        if (!file_exists($path)) {
            $this->error('CSVが見つかりません');
            return Command::FAILURE;
        }

        $fp = fopen($path, 'r');

        $header = fgetcsv($fp);

        $count = 0;

        DB::beginTransaction();

        try {

            while (($row = fgetcsv($fp)) !== false) {

                $row = array_combine($header, $row);

                DB::table('ringi_requests')->insert([
                    'document_no'   => $row['DraftNumber'],
                    'title'         => $row['Title'],
                    'content'       => $row['Contents'],
                    'user_id'       => $row['userID'],
                    'ringi_type_id' => $row['DraftTypeId'],

                    'status'        => 'approved',
                    'current_step'  => null,

                    'applied_date'  => $row['CreatedDate'],

                    'created_at'    => $row['CreatedDate'],
                    'updated_at'    => $row['CreatedDate'],
                ]);

                $count++;
            }

            DB::commit();
        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        fclose($fp);

        $this->info("{$count}件インポートしました");

        return Command::SUCCESS;
    }
}
