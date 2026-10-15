<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ForestPermit\ForestPermitApplication;

class ImportForestPermitCsv extends Command
{
    protected $signature = 'forest-permit:import-csv {--truncate}';

    protected $description = '保安林許可申請CSV取込';

    public function handle(): int
    {
        if ($this->option('truncate')) {

            if (
                $this->confirm(
                    '既存データを削除してから取込しますか？'
                )
            ) {
                ForestPermitApplication::truncate();

                $this->info('既存データを削除しました。');
            }
        }

        $path = storage_path(
            'app/license.csv'
        );

        if (!file_exists($path)) {

            $this->error(
                "CSVファイルが見つかりません。\n{$path}"
            );

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if (!$handle) {

            $this->error('CSVを開けませんでした。');

            return self::FAILURE;
        }

        // ヘッダ取得
        $header = fgetcsv($handle);

        if (!$header) {

            $this->error('ヘッダ行を取得できません。');

            fclose($handle);

            return self::FAILURE;
        }

        // BOM除去
        $header[0] = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $header[0]
        );

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_map(
                fn($value) => $this->normalize($value),
                array_combine($header, $row)
            );

            ForestPermitApplication::updateOrCreate(
                [
                    'management_no' => $data['ReferenceNumber'],
                    'branch_no' => $data['BranchNumber'] ?? 0,
                ],
                [
                    'facility_name' => $data['FacilityName'],
                    'protection_forest_type' => $data['ForestReserve'],
                    'forest_location' => $data['Location'],
                    'parcel_count' => $data['Stock'],
                    'applicant_name' => $data['Applicant'],
                    'contact' => $data['Contact'],
                    'permit_area' => $data['PermittedArea'],

                    'application_date' => $data['ApplicationDate'],
                    'permit_date' => $data['PermitDate'],

                    'order_no' => $data['InstructionNumber'],

                    'permit_start_date' => $data['LicensedStartDate'],
                    'permit_end_date' => $data['LicensedEndDate'],

                    'clear_cut_completion_date' => $data['DeforestationDate'],
                    'planting_completion_date' => $data['PlantingDate'],

                    'submission_date' => $data['SubmissionDate'],
                    'start_date' => $data['StartDate'],
                    'completion_date' => $data['CompletionDate'],

                    'remarks' => $data['Remark'],
                ]
            );

            $count++;

            if ($count % 100 === 0) {
                $this->info("{$count}件処理...");
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info("取込完了：{$count}件");

        return self::SUCCESS;
    }

    /**
     * CSV値の正規化
     */
    private function normalize($value): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim($value);

        if (
            $value === '' ||
            strtoupper($value) === 'NULL'
        ) {
            return null;
        }

        return $value;
    }
}
