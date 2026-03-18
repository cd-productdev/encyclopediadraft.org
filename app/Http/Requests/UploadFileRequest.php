<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Maximum file size: 3.86 MB = 4047503 bytes (approximately)
        // Laravel validation uses KB, so 3.86 MB = 3950 KB (approximately)
        $maxSizeKB = 3950;

        // Allowed MIME types
        $allowedMimes = [
            'image/png', 'image/gif', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.oasis.opendocument.presentation',
            'audio/flac', 'video/x-matroska', 'video/quicktime', 'audio/mpeg', 'video/mp4',
            'audio/ogg', 'video/ogg', 'audio/wav', 'video/webm',
        ];

        return [
            'file' => [
                'required',
                'file',
                'mimes:png,gif,jpg,jpeg,svg,webp,pdf,doc,docx,odt,xls,xlsx,ods,ppt,pptx,odp,flac,mkv,mov,mp3,mp4,oga,ogg,ogv,wav,webm',
                'max:'.$maxSizeKB,
            ],
            'destination_filename' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'license' => 'nullable|string|max:100',
            'watch' => 'nullable|boolean',
            'ignore_warnings' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'The file is required.',
            'file.file' => 'The uploaded file is not valid.',
            'file.max' => 'The file size must not exceed 3.86 MB.',
            'file.mimes' => 'The file type is not allowed. Allowed types: png, gif, jpg, jpeg, svg, webp, pdf, doc, docx, odt, xls, xlsx, ods, ppt, pp tx, odp, flac, mkv, mov, mp3, mp4, oga, ogg, ogv, wav, webm.',
        ];
    }
}
