<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MetadataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $metadata = Metadata::all();
        return view('index-metadata', compact('metadata'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255|unique:metadata,page',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:png,ico|max:2048',
        ]);

        // Kiểm tra và tạo thư mục 'favicons' nếu chưa tồn tại
        if (!Storage::exists('public/favicons')) {
            Storage::makeDirectory('public/favicons');
        }

        // Lưu favicon vào thư mục 'favicons'
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicons', 'public'); // Lưu vào thư mục 'favicons'
            $validated['favicon'] = $path;
        }

        Metadata::create($validated);

        return redirect()->route('admin.index')->with('success', 'Metadata đã được thêm thành công!');
    }

    public function edit($id)
    {
        $metadata = Metadata::findOrFail($id);
        return view('edit-metadata', compact('metadata'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'page' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:jpeg,png,ico|max:2048', // Kiểm tra file ảnh
        ]);

        $metadata = Metadata::findOrFail($id);

        $metadata->page = $request->page;
        $metadata->title = $request->title;
        $metadata->description = $request->description;
        $metadata->keywords = $request->keywords;

        // Lưu favicon mới vào thư mục 'favicons' nếu có file tải lên
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $path = $file->store('favicons', 'public'); // Lưu file vào thư mục 'favicons'
            $metadata->favicon = $path;
        }

        $metadata->update();

        return redirect()->route('admin.metadata.index')->with('success', 'Metadata đã được cập nhật thành công!');
    }
}
