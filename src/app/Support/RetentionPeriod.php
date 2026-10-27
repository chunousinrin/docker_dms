<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * 保存期間の計算。フレームワークに依存しない純粋な日付計算。
 *
 * 起算日    = 事業年度終了の日の翌日から2か月（＋延長月数）を経過した日
 *            ＝ 確定申告書の提出期限の翌日
 * 保存満了日 = 起算日から保存年数を経過する日の前日
 *
 * 例（3月決算・10年）: 取引日 2026-10-26 → 事業年度末 2027-03-31
 *                      → 起算日 2027-06-01 → 保存満了日 2037-05-31
 */
final class RetentionPeriod
{
    /** 取引日が属する事業年度の末日 */
    public static function fiscalYearEnd(DateTimeInterface $date, int $endMonth): DateTimeImmutable
    {
        $year  = (int) $date->format('Y');
        $month = (int) $date->format('n');

        if ($month > $endMonth) {
            $year++;
        }

        return (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $endMonth)))
            ->modify('last day of this month');
    }

    /** 保存期間の起算日（申告期限の翌日） */
    public static function startsOn(DateTimeInterface $date, int $endMonth, int $extensionMonths = 0): DateTimeImmutable
    {
        // 事業年度末の月の1日 ＋(2か月 + 延長) ＝ 申告期限の月。その翌月1日が起算日
        return self::fiscalYearEnd($date, $endMonth)
            ->modify('first day of this month')
            ->modify('+' . (3 + $extensionMonths) . ' months');
    }

    /** 保存満了日（この日まで保存が必要。翌日以降は期限経過） */
    public static function until(DateTimeInterface $date, int $endMonth, int $years, int $extensionMonths = 0): DateTimeImmutable
    {
        return self::startsOn($date, $endMonth, $extensionMonths)
            ->modify("+{$years} years")
            ->modify('-1 day');
    }
}
