<?php
namespace App\Traits;

trait SoftDeletes
{

    protected static function filterDeleted(array $allRecords): array
    {
        return array_values(array_filter(
            $allRecords,
            fn(array $item) =>
            ! array_key_exists('deleted_at', $item)
            || $item['deleted_at'] === null
        ));
    }
}
