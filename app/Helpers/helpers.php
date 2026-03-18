<?php

use App\Helpers\DateHelper;

if (! function_exists('formatDate')) {
    /**
     * Format date only
     *
     * @param  string|\Carbon\Carbon|null  $date
     */
    function formatDate($date, string $format = 'Y-m-d'): ?string
    {
        return DateHelper::formatDate($date, $format);
    }
}

if (! function_exists('formatDateTime')) {
    /**
     * Format date and time
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatDateTime($dateTime, string $format = 'Y-m-d H:i:s'): ?string
    {
        return DateHelper::formatDateTime($dateTime, $format);
    }
}

if (! function_exists('formatDateHuman')) {
    /**
     * Format date in human readable format
     *
     * @param  string|\Carbon\Carbon|null  $date
     */
    function formatDateHuman($date): ?string
    {
        return DateHelper::formatDateHuman($date);
    }
}

if (! function_exists('formatDateTimeHuman')) {
    /**
     * Format date and time in human readable format
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatDateTimeHuman($dateTime): ?string
    {
        return DateHelper::formatDateTimeHuman($dateTime);
    }
}

if (! function_exists('formatDateShort')) {
    /**
     * Format date in short format
     *
     * @param  string|\Carbon\Carbon|null  $date
     */
    function formatDateShort($date): ?string
    {
        return DateHelper::formatDateShort($date);
    }
}

if (! function_exists('formatDateTimeShort')) {
    /**
     * Format date and time in short format
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatDateTimeShort($dateTime): ?string
    {
        return DateHelper::formatDateTimeShort($dateTime);
    }
}

if (! function_exists('formatDateISO')) {
    /**
     * Format date in ISO 8601 format
     *
     * @param  string|\Carbon\Carbon|null  $date
     */
    function formatDateISO($date): ?string
    {
        return DateHelper::formatDateISO($date);
    }
}

if (! function_exists('formatRelative')) {
    /**
     * Format relative time (e.g., "2 hours ago", "3 days ago")
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatRelative($dateTime): ?string
    {
        return DateHelper::formatRelative($dateTime);
    }
}

if (! function_exists('formatTime')) {
    /**
     * Format time only
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatTime($dateTime, string $format = 'H:i:s'): ?string
    {
        return DateHelper::formatTime($dateTime, $format);
    }
}

if (! function_exists('formatTime12Hour')) {
    /**
     * Format time in 12-hour format
     *
     * @param  string|\Carbon\Carbon|null  $dateTime
     */
    function formatTime12Hour($dateTime): ?string
    {
        return DateHelper::formatTime12Hour($dateTime);
    }
}

if (! function_exists('uploadImage')) {
    /**
     * Upload image file
     *
     * @param  object  $file
     */
    function uploadImage($file, string $directory = 'images'): ?string
    {
        return DateHelper::uploadImage($file, $directory);
    }
}

function apiResponse($success, $data = null, $message = null, $statusCode = 200, $error = null, $pagination = null)
{
    return response()->json([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'pagination' => $pagination,
        'error' => $error,
        'status' => $statusCode,
        'timestamp' => formatDateTime(now()->toISOString()),
    ], $statusCode)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
}

function getPaginationData($paginator)
{
    return [
        'current_page' => $paginator->currentPage(),
        'last_page' => $paginator->lastPage(),
        'per_page' => $paginator->perPage(),
        'total' => $paginator->total(),
        'from' => $paginator->firstItem(),
        'to' => $paginator->lastItem(),
        'has_more_pages' => $paginator->hasMorePages(),
    ];
}
