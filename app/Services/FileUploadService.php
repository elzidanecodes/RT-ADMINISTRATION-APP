<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function storeKtpPhoto(UploadedFile $file): string
    {
        return $this->store($file, 'ktp');
    }

    public function storeReceipt(UploadedFile $file): string
    {
        return $this->store($file, 'receipts');
    }

    public function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // Returns the relative path stored in DB (e.g. "ktp/uuid.jpg")
    private function store(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $file->storeAs($folder, $filename, 'public');

        return $folder . '/' . $filename;
    }
}
