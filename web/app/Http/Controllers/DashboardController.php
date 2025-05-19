<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classifications = Classification::where('user_id', Auth::id())->get();
        // Lấy danh sách người dùng có rating > 0
        $allUserRatings = User::where('rating', '>', 0)
            ->select('name', 'rating', 'feedback','tokens_used')
            ->orderBy('rating', 'desc')
            ->get();

        return view('dashboard', compact('classifications', 'allUserRatings'));
    }
    public function updateUserInfo(Request $request)
    {
        try {
            \Log::info('updateUserInfo called', $request->all());

            $request->validate([
                'userId' => 'required',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:15',
                'gender' => 'nullable|in:male,female,other',
                'address' => 'nullable|string|max:255',
            ]);

            $user = User::find($request->userId);

            if (!$user || $user->id !== Auth::id()) {
                \Log::warning('Unauthorized access attempt', ['userId' => $request->userId, 'authId' => Auth::id()]);
                return response()->json(['success' => false, 'error' => 'Không có quyền cập nhật thông tin!'], 403);
            }

            try {
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->gender = $request->gender;
                $user->address = $request->address;

                \Log::info('Data to be saved', [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'address' => $request->address
                ]);

                $user->save();
                \Log::info('User info updated successfully', ['userId' => $user->id]);
                return response()->json(['success' => true]);
            } catch (\Illuminate\Database\QueryException $e) {
                \Log::error('Database error saving user data: ' . $e->getMessage(), [
                    'userId' => $user->id,
                    'data' => $request->all(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['success' => false, 'error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()], 500);
            } catch (\Exception $e) {
                \Log::error('Error saving user data: ' . $e->getMessage(), [
                    'userId' => $user->id,
                    'data' => $request->all(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['success' => false, 'error' => 'Lỗi khi lưu thông tin người dùng: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error in updateUserInfo: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra khi cập nhật thông tin. Vui lòng thử lại!'], 500);
        }
    }
}