<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class YtFormat
{
    public static function count(float|int|null $n): string
    {
        if ($n === null || $n == 0) {
            return '0';
        }

        $n = (float) $n;

        if ($n >= 1_000_000_000) {
            return self::short($n / 1_000_000_000).' tỷ';
        }

        if ($n >= 1_000_000) {
            return self::short($n / 1_000_000).' Tr';
        }

        if ($n >= 1_000) {
            return self::short($n / 1_000).' N';
        }

        return (string) (int) $n;
    }

    public static function ago($date): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            $d = Carbon::parse($date);
        } catch (\Throwable $e) {
            return '';
        }

        $diff = $d->diff(Carbon::now());

        if ($diff->y > 0) {
            return $diff->y.' năm trước';
        }

        if ($diff->m > 0) {
            return $diff->m.' tháng trước';
        }

        if ($diff->d >= 7) {
            return (int) floor($diff->d / 7).' tuần trước';
        }

        if ($diff->d > 0) {
            return $diff->d.' ngày trước';
        }

        if ($diff->h > 0) {
            return $diff->h.' giờ trước';
        }

        if ($diff->i > 0) {
            return $diff->i.' phút trước';
        }

        return 'vừa xong';
    }

    public static function fullDate($date): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected static function short(float $v): string
    {
        $v = round($v, 1);

        return rtrim(rtrim((string) $v, '0'), '.');
    }
}
