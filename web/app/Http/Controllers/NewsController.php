<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::all();
        return view('admin.news.index', compact('news')); // Giả sử view vẫn nằm trong admin
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|string',
        ]);
        if (!empty($validated['image'])) {
            $validated['image'] = 'ceramics/' . ltrim($validated['image'], '/'); 
        }
    
        // Lưu vào cơ sở dữ liệu
        News::create($validated);
        return redirect()->back()->with('news_success', 'Thêm bài viết thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $news = News::findOrFail($id);
        $news->update($request->all());
        return redirect()->back()->with('news_success', 'Cập nhật bài viết thành công!');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return redirect()->back()->with('news_success', 'Xóa bài viết thành công!');
    }
}