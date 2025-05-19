<h2>Kết quả dự đoán:</h2>
<p>Loại gốm sứ: {{ $result['predicted_class'] }}</p>
<p>Độ tin cậy: {{ $result['confidence'] * 100 }}%</p>
<p>Thông tin chi tiết: {{ $result['llm_response'] }}</p>