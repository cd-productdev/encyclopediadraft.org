<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format date only
     *
     * @param  string|Carbon|null  $date
     */
    public static function formatDate($date, string $format = 'Y-m-d'): ?string
    {
        if (! $date) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }

    /**
     * Format date and time
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatDateTime($dateTime, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->format($format);
    }

    /**
     * Format date in human readable format
     *
     * @param  string|Carbon|null  $date
     */
    public static function formatDateHuman($date): ?string
    {
        if (! $date) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format('F j, Y'); // e.g., "January 15, 2024"
    }

    /**
     * Format date and time in human readable format
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatDateTimeHuman($dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->format('F j, Y g:i A'); // e.g., "January 15, 2024 3:30 PM"
    }

    /**
     * Format date in short format
     *
     * @param  string|Carbon|null  $date
     */
    public static function formatDateShort($date): ?string
    {
        if (! $date) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format('M d, Y'); // e.g., "Jan 15, 2024"
    }

    /**
     * Format date and time in short format
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatDateTimeShort($dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->format('M d, Y H:i'); // e.g., "Jan 15, 2024 15:30"
    }

    /**
     * Format date in ISO 8601 format
     *
     * @param  string|Carbon|null  $date
     */
    public static function formatDateISO($date): ?string
    {
        if (! $date) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->toIso8601String();
    }

    /**
     * Format relative time (e.g., "2 hours ago", "3 days ago")
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatRelative($dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->diffForHumans();
    }

    /**
     * Format time only
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatTime($dateTime, string $format = 'H:i:s'): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->format($format);
    }

    /**
     * Format time in 12-hour format
     *
     * @param  string|Carbon|null  $dateTime
     */
    public static function formatTime12Hour($dateTime): ?string
    {
        if (! $dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            $dateTime = Carbon::parse($dateTime);
        }

        return $dateTime->format('g:i A'); // e.g., "3:30 PM"
    }

    /**
     * Upload image file
     *
     * @param  object  $file
     */
    public static function uploadImage($file, string $directory = 'images'): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        // Validate extension (very important for security)
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, $allowed)) {
            return null;
        }

        // Ensure directory exists
        $storagePath = storage_path("app/public/{$directory}");
        if (! file_exists($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        // Generate unique filename
        $filename = uniqid().'_'.time().'.'.$extension;

        // Move the file
        $file->move($storagePath, $filename);

        return "{$directory}/{$filename}";
    }
}
