<?php

namespace Tests\Unit;

use App\Support\RetentionPeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RetentionPeriodTest extends TestCase
{
    public static function cases(): array
    {
        // 取引日, 事業年度末, 起算日, 保存満了日（3月決算・10年・延長なし）
        return [
            '期中'       => ['2026-10-26', '2027-03-31', '2027-06-01', '2037-05-31'],
            '年明け'     => ['2027-02-10', '2027-03-31', '2027-06-01', '2037-05-31'],
            '期末日'     => ['2027-03-31', '2027-03-31', '2027-06-01', '2037-05-31'],
            '翌期首日'   => ['2027-04-01', '2028-03-31', '2028-06-01', '2038-05-31'],
            '期首日'     => ['2026-04-01', '2027-03-31', '2027-06-01', '2037-05-31'],
            'うるう日'   => ['2028-02-29', '2028-03-31', '2028-06-01', '2038-05-31'],
        ];
    }

    #[DataProvider('cases')]
    public function test_three_month_fiscal_year(string $date, string $fyEnd, string $start, string $until): void
    {
        $d = new DateTimeImmutable($date);

        $this->assertSame($fyEnd, RetentionPeriod::fiscalYearEnd($d, 3)->format('Y-m-d'));
        $this->assertSame($start, RetentionPeriod::startsOn($d, 3)->format('Y-m-d'));
        $this->assertSame($until, RetentionPeriod::until($d, 3, 10)->format('Y-m-d'));
    }

    public function test_filing_extension_moves_start_date(): void
    {
        $d = new DateTimeImmutable('2026-10-26');

        $this->assertSame('2027-07-01', RetentionPeriod::startsOn($d, 3, 1)->format('Y-m-d'));
    }
}
