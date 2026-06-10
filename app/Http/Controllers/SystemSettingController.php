<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Exception;

class SystemSettingController extends Controller
{
    /**
     * Ambil data logo saat ini (Public Route).
     * GET /api/system-settings/logos
     */
    public function getLogos()
    {
        try {
            $logoKiriSidebar = SystemSetting::where('key', 'logo_kiri_sidebar')->value('value');
            $logoKiriPdf = SystemSetting::where('key', 'logo_kiri_pdf')->value('value');
            $logoKananPdf = SystemSetting::where('key', 'logo_kanan_pdf')->value('value');
            $stampImage = SystemSetting::where('key', 'stamp_image')->value('value');

            return response()->json([
                'message' => 'Logos fetched successfully',
                'data' => [
                    'logo_kiri_sidebar' => $logoKiriSidebar ? asset('storage/' . $logoKiriSidebar) : null,
                    'logo_kiri_pdf'     => $logoKiriPdf ? asset('storage/' . $logoKiriPdf) : null,
                    'logo_kanan_pdf'    => $logoKananPdf ? asset('storage/' . $logoKananPdf) : null,
                    'stamp_image'       => $stampImage ? asset('storage/' . $stampImage) : null,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data logo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload logo baru (Protected Route - Super Admin).
     * POST /api/logo/upload
     */
    public function uploadLogo(Request $request)
    {
        try {
            // 1. Cek otentikasi & otorisasi
            if (!$request->user() || $request->user()->role_id !== 1) {
                return response()->json([
                    'message' => 'Hanya Super Admin yang diperbolehkan mengubah logo'
                ], 403);
            }

            // 2. Validasi input
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // max 5MB
                'type'  => 'required|string|in:logo_kiri_sidebar,logo_kiri_pdf,logo_kanan_pdf,stamp_image'
            ]);

            $file = $request->file('image');
            $type = $request->input('type');

            // 3. Hapus file lama jika ada
            $oldPath = SystemSetting::where('key', $type)->value('value');
            if ($oldPath && Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }

            // 4. Simpan file baru dengan nama unik (timestamp) untuk mencegah caching browser
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $filename = "{$type}_" . time() . ".{$extension}";
            
            // Simpan di subfolder 'logos' dalam disk public
            $newPath = Storage::putFileAs('logos', $file, $filename);

            // 5. Update data di database
            $setting = SystemSetting::updateOrCreate(
                ['key' => $type],
                ['value' => $newPath]
            );

            return response()->json([
                'message' => 'Logo berhasil diperbarui',
                'data' => [
                    'key' => $type,
                    'url' => asset('storage/' . $newPath)
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengunggah logo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
