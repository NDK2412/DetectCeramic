<?php

namespace App\Http\Controllers;

use App\Models\Ceramic;
use Illuminate\Http\Request;

class CeramicController extends Controller
{
    public function gallery(Request $request)
    {
        // Lấy tham số lọc từ request
        $category = $request->query('category');
        $origin = $request->query('origin');

        // Xây dựng truy vấn
        $query = Ceramic::query();

        // Áp dụng bộ lọc
        if ($category) {
            $query->where('category', $category);
        }

        if ($origin) {
            $query->where('origin', $origin);
        }

        // Phân trang (10 món đồ gốm mỗi trang)
        $ceramics = $query->paginate(10);

        // Lấy danh sách danh mục và nguồn gốc duy nhất cho bộ lọc
        $categories = Ceramic::select('category')->distinct()->pluck('category');
        $origins = Ceramic::select('origin')->distinct()->pluck('origin');

        return view('gallery', compact('ceramics', 'categories', 'origins'));
    }

    public function show($id)
    {
        $ceramic = Ceramic::findOrFail($id);
        return view('ceramic_detail', compact('ceramic'));
    }

    //     
    public function classify(Request $request)
    {
        try {
            // Xác thực dữ liệu đầu vào
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'result' => 'required|string',
                'llm_response' => 'required|string', // Thêm xác thực cho llm_response
            ]);

            // Kiểm tra người dùng đã đăng nhập chưa
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
            }

            // Lưu ảnh vào storage
            $imagePath = $request->file('image')->store('images', 'public');
            if (!$imagePath) {
                return response()->json(['error' => 'Không thể lưu ảnh'], 500);
            }

            // Lấy kết quả nhận diện và llm_response từ request
            $result = $request->input('result');
            $llmResponse = $request->input('llm_response');

            // Lưu vào bảng classifications
            $classification = \App\Models\Classification::create([
                'user_id' => $userId,
                'image_path' => '/storage/' . $imagePath,
                'result' => $result,
                'llm_response' => $llmResponse, // Lưu llm_response
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Nhận diện thành công và đã lưu vào lịch sử',
                'result' => $result,
                'classification_id' => $classification->id,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Lỗi khi lưu nhận diện: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi lưu nhận diện: ' . $e->getMessage()], 500);
        }
    }
    public function dashboard()
    {
        // Lấy người dùng đã đăng nhập
        $user = auth()->user();

        // Truy xuất lịch sử nhận diện với phân trang
        $classifications = \App\Models\Classification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Truyền dữ liệu vào view
        return view('dashboard', compact('classifications'));
    }
    public function getClassificationInfo($id)
    {
        $classification = \App\Models\Classification::findOrFail($id);
        if ($classification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }
        return response()->json(['llm_response' => $classification->llm_response]);
    }
    public function getHistory(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng chưa đăng nhập.'
                ], 401);
            }

            $classifications = \App\Models\Classification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'redirect_url' => 'your-app://redirect-to-somewhere', // Chuyển hướng URL
                'history' => $classifications->map(function ($classification) {
                    return [
                        'id' => $classification->id,
                        'image_path' => $classification->image_path,
                        'result' => $classification->result,
                        'created_at' => $classification->created_at->format('Y-m-d H:i:s'),
                        'llm_response' => $classification->llm_response
                    ];
                })->toArray()
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching classification history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy lịch sử: ' . $e->getMessage()
            ], 500);
        }
    }

}


