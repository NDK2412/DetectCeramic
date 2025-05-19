<?php

namespace App\Http\Controllers;

use App\Models\Apk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApkController extends Controller
{
    public function upload(Request $request)
    {
        Log::info('APK upload request data:', $request->all());
        Log::info('Files:', $request->file());
        try {
            // Xác thực dữ liệu
            $request->validate([
                'apkFile' => 'required|file', // Chấp nhận mọi loại tệp
                'version' => 'required|integer|min:1',
            ], [
                'apkFile.max' => 'File không được vượt quá 100MB', // Lưu lại nếu vẫn cần giới hạn kích thước
            ]);

            // Kiểm tra version trùng lặp
            $existingApk = Apk::where('version', $request->version)->first();
            if ($existingApk) {
                return redirect()->back()->withErrors(['version' => 'Version đã được sử dụng']);
            }

            // Lưu file
            $file = $request->file('apkFile');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public'); // Thay đổi thư mục lưu nếu cần

            // Lưu thông tin vào database
            Apk::create([
                'version' => $request->version, // Đổi từ version_code thành version
                'file_name' => $fileName,
                'file_path' => $filePath,
            ]);

            return redirect()->back()->with('success', 'Tải lên file thành công!');
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'version' => $request->version,
            ]);
            return redirect()->back()->withErrors(['apkFile' => 'Tải lên thất bại: ' . $e->getMessage()]);
        }
    }
}
