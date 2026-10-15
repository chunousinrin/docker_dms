<?php
if (!function_exists('to_wareki')) {
    /**
     * 日付文字列を和暦に変換するHelper関数
     *
     * @param string|null $date
     * @return string|null
     */
    function to_wareki($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            $formatter = new IntlDateFormatter(
                'ja_JP@calendar=japanese',
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                'Asia/Tokyo',
                IntlDateFormatter::TRADITIONAL
            );

            return $formatter->format(new DateTime($date));
        } catch (Exception $e) {
            // 日付のパースに失敗した場合は、元の値を返すか空文字を返すなどの例外処理
            return $date;
        }
    }
}
