<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function admin()
    {
        // Lấy tất cả liên hệ từ database
        $contacts = Contact::latest()->get();
        // Debug: Kiểm tra dữ liệu
        if ($contacts->isEmpty()) {
            \Log::info('No contacts found in database.');
        } else {
            \Log::info('Contacts found: ' . $contacts->count());
            \Log::info('Contacts data: ' . $contacts->toJson());
        }
        // Lấy giao diện hiện tại (nếu đã tích hợp theme từ trước)
        $currentTheme = \App\Models\Setting::where('key', 'theme')->first()->value ?? 'index';
        dd($contacts);
        // Truyền biến $contacts và $currentTheme vào view
        return view('admin', [
            'contacts' => $contacts,
            'currentTheme' => $currentTheme
        ]);
    }

    // Các phương thức khác giữ nguyên
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        Contact::create($request->all());

        return response()->json(['message' => 'Liên hệ đã được gửi thành công!'], 200);
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return view('admin-contact-show', compact('contact'));
    }

    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return response()->json(['success' => true, 'message' => 'Đã đánh dấu là đã đọc.']);
    }
}