<!-- resources/views/admin/llm_settings.blade.php -->
<div class="mt-6">
    <h2 class="text-lg font-semibold mb-4">Cài đặt mô hình hệ thống</h2>
    @if (session('llm_success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('llm_success') }}
        </div>
    @endif
    @if (session('llm_error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('llm_error') }}
        </div>
    @endif
    <form action="{{ route('admin.updateLLMSettings') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="llm_model" class="block text-sm font-medium text-gray-700">Chọn mô hình LLM</label>
            <select name="llm_model" id="llm_model" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($availableModels as $model)
                    <option value="{{ $model }}" {{ ($llmModel && $llmModel->value === $model) ? 'selected' : '' }}>{{ $model }}</option>
                @endforeach
            </select>
            @error('llm_model')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="llm_api_key" class="block text-sm font-medium text-gray-700">API Key</label>
            <input type="text" name="llm_api_key" id="llm_api_key" value="{{ $llmApiKey->value ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nhập API Key">
            @error('llm_api_key')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <button type="submit" class="action-btn save-btn">
            <i class="fas fa-download"></i>Lưu cài đặt
            </button>
        </div>
    </form>
</div>