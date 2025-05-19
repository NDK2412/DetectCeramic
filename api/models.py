# import tensorflow as tf
# import logging
# import os
# import json
# import tensorflow.keras as k3
# from config import MODEL_PATH_66, MODEL_PATH_67, DEFAULT_MODEL
#
# # Cấu hình logging
# logging.basicConfig(level=logging.INFO)
# logger = logging.getLogger(__name__)
#
# # Đường dẫn tới file lưu trạng thái
# STATE_FILE = "model_state.json"
#
# # Danh sách class cho mô hình 66 class (không có 'Bát Tràng Men đỏ')
# CLASS_NAMES_66 = [
#     'Bát Tràng Men chảy', 'Bát Tràng Men hoa biến', 'Bát Tràng Men lam',
#     'Bát Tràng Men nâu', 'Bát Tràng Men nâu da lươn', 'Bát Tràng Men ngọc',
#     'Bát Tràng Men rạn', 'Bát Tràng Men trắng', 'Bát Tràng Men vàng',
#     'Bàu Trúc Men nâu đất', 'Cây Mai Men Nâu', 'Cây Mai Men Xanh Lam',
#     'Cây Mai Men xanh rêu', 'Chu Đậu Men nâu', 'Chu Đậu Men ngọc',
#     'Chu Đậu Men trắng chấm', 'Đồng Triều Men đỏ', 'Đồng Triều Men nâu',
#     'Đồng Triều Men trắng vẽ lam', 'Đồng Triều Men vàng đất',
#     'Gốm Biên Hòa Men Nâu', 'Gốm Biên Hòa Men Xanh Đồng Trổ Bông',
#     'Gốm Biên Hòa Men Xanh Lục', 'Gốm Biên Hòa Men nâu Da lươn',
#     'Gốm Biên Hòa Men xanh lam', 'Gốm Bình Dương Men Đen Xanh Chảy',
#     'Gốm Bình Dương Men Nâu', 'Gốm Bình Dương Men Trắng',
#     'Gốm Bình Dương Men Xanh', 'Gốm Cây Men lam nhạt', 'Gốm Cây Men trắng',
#     'Gốm Cây Men xanh ngọc', 'Gốm Bồ Bát Men trắng đục',
#     'Gốm Bồ Bát Men trắng vẽ lam', 'Gốm Gia Thủy Màu nâu đất',
#     'Gốm Gò Sành (Bình Định) Men nâu', 'Gốm Hoa Nâu Men trắng nâu',
#     'Gốm Hương Canh Vuốt tay không men', 'Gốm Kim Lan Men chảy giả cổ',
#     'Gốm Kim Lan Men nâu', 'Gốm Kim Lan Men xanh', 'Gốm Lai Thêu Màu đất nung',
#     'Gốm Lai Thêu Men nâu da lươn', 'Gốm Lai Thêu Men ngũ thái',
#     'Gốm Lai Thêu Men trắng', 'Gốm Lai Thêu Men xanh',
#     'Gốm Lai Thêu Men xanh lục', 'Gốm Lai Thêu Men xanh trắng',
#     'Gốm Mường Chanh Màu đất nung (không men)', 'Gốm Mỹ Thiên Màu đất nung',
#     'Gốm Mỹ Thiên Men vàng nâu', 'Gốm Mỹ Thiên Men xanh lá',
#     'Gốm Quảng Đức Men nâu', 'Gốm Quảng Đức Men xanh lục',
#     'Gốm Sa Huỳnh Màu đỏ đất nung', 'Gốm Thanh Lễ Men màu',
#     'Phù Lãng Men Nâu da lươn', 'Phù Lãng Men nâu đen', 'Phù Lãng Men nâu đỏ',
#     'Phước Tích Men nâu hoàng gia', 'Phước Tích Men xám đen',
#     'Tân Vân Men Nâu', 'Tân Vân Men Xanh', 'Thanh Hà Men đỏ cam',
#     'Thổ Hà Men nâu', 'Vĩnh Long Men đỏ'
# ]
#
# # Danh sách class cho mô hình 67 class (có thêm 'Bát Tràng Men đỏ')
# CLASS_NAMES_67 = [
#     'Bát Tràng Men chảy', 'Bát Tràng Men đỏ', 'Bát Tràng Men hoa biến', 'Bát Tràng Men lam',
#     'Bát Tràng Men nâu', 'Bát Tràng Men nâu da lươn', 'Bát Tràng Men ngọc',
#     'Bát Tràng Men rạn', 'Bát Tràng Men trắng', 'Bát Tràng Men vàng',
#     'Bàu Trúc Men nâu đất', 'Cây Mai Men Nâu', 'Cây Mai Men Xanh Lam',
#     'Cây Mai Men xanh rêu', 'Chu Đậu Men nâu', 'Chu Đậu Men ngọc',
#     'Chu Đậu Men trắng chấm', 'Đồng Triều Men đỏ', 'Đồng Triều Men nâu',
#     'Đồng Triều Men trắng vẽ lam', 'Đồng Triều Men vàng đất',
#     'Gốm Biên Hòa Men Nâu', 'Gốm Biên Hòa Men Xanh Đồng Trổ Bông',
#     'Gốm Biên Hòa Men Xanh Lục', 'Gốm Biên Hòa Men nâu Da lươn',
#     'Gốm Biên Hòa Men xanh lam', 'Gốm Bình Dương Men Đen Xanh Chảy',
#     'Gốm Bình Dương Men Nâu', 'Gốm Bình Dương Men Trắng',
#     'Gốm Bình Dương Men Xanh', 'Gốm Cây Men lam nhạt', 'Gốm Cây Men trắng',
#     'Gốm Cây Men xanh ngọc', 'Gốm Bồ Bát Men trắng đục',
#     'Gốm Bồ Bát Men trắng vẽ lam', 'Gốm Gia Thủy Màu nâu đất',
#     'Gốm Gò Sành (Bình Định) Men nâu', 'Gốm Hoa Nâu Men trắng nâu',
#     'Gốm Hương Canh Vuốt tay không men', 'Gốm Kim Lan Men chảy giả cổ',
#     'Gốm Kim Lan Men nâu', 'Gốm Kim Lan Men xanh', 'Gốm Lai Thêu Màu đất nung',
#     'Gốm Lai Thêu Men nâu da lươn', 'Gốm Lai Thêu Men ngũ thái',
#     'Gốm Lai Thêu Men trắng', 'Gốm Lai Thêu Men xanh',
#     'Gốm Lai Thêu Men xanh lục', 'Gốm Lai Thêu Men xanh trắng',
#     'Gốm Mường Chanh Màu đất nung (không men)', 'Gốm Mỹ Thiên Màu đất nung',
#     'Gốm Mỹ Thiên Men vàng nâu', 'Gốm Mỹ Thiên Men xanh lá',
#     'Gốm Quảng Đức Men nâu', 'Gốm Quảng Đức Men xanh lục',
#     'Gốm Sa Huỳnh Màu đỏ đất nung', 'Gốm Thanh Lễ Men màu',
#     'Phù Lãng Men Nâu da lươn', 'Phù Lãng Men nâu đen', 'Phù Lãng Men nâu đỏ',
#     'Phước Tích Men nâu hoàng gia', 'Phước Tích Men xám đen',
#     'Tân Vân Men Nâu', 'Tân Vân Men Xanh', 'Thanh Hà Men đỏ cam',
#     'Thổ Hà Men nâu', 'Vĩnh Long Men đỏ'
# ]
#
# # Biến toàn cục để lưu mô hình
# current_model = None
# current_class_names = None
# current_model_type = None
#
# # Hàm lưu trạng thái vào file
# def save_state():
#     state = {
#         "model_type": current_model_type,
#         "class_names": current_class_names
#     }
#     with open(STATE_FILE, "w") as f:
#         json.dump(state, f)
#     logger.info(f"Saved state to {STATE_FILE}: {state}")
#
# # Hàm tải trạng thái từ file
# def load_state():
#     """
#     Tải trạng thái của mô hình từ tệp lưu trữ. Nếu tệp không tồn tại hoặc không hợp lệ,
#     trạng thái mặc định sẽ được sử dụng.
#     """
#     global current_model_type, current_class_names
#
#     try:
#         # Kiểm tra sự tồn tại của tệp trạng thái
#         if os.path.exists(STATE_FILE):
#             with open(STATE_FILE, "r") as f:
#                 state = json.load(f)
#                 # Kiểm tra định dạng của dữ liệu trong tệp
#                 if "model_type" in state and "class_names" in state:
#                     current_model_type = state["model_type"]
#                     current_class_names = state["class_names"]
#                     logger.info(
#                         f"Trạng thái đã được tải từ {STATE_FILE}: "
#                         f"model_type={current_model_type}, class_count={len(current_class_names)}"
#                     )
#                 else:
#                     # Nếu dữ liệu không hợp lệ, sử dụng trạng thái mặc định
#                     raise ValueError("Định dạng tệp trạng thái không hợp lệ.")
#         else:
#             # Nếu tệp không tồn tại, sử dụng trạng thái mặc định
#             logger.warning(f"Tệp trạng thái {STATE_FILE} không tồn tại. Sử dụng trạng thái mặc định.")
#             initialize_default_state()
#     except (json.JSONDecodeError, ValueError) as e:
#         # Nếu xảy ra lỗi khi giải mã JSON hoặc định dạng không hợp lệ
#         logger.error(f"Lỗi khi đọc tệp trạng thái: {str(e)}. Sử dụng trạng thái mặc định.")
#         initialize_default_state()
#     except Exception as e:
#         # Bắt các lỗi không xác định khác
#         logger.critical(f"Lỗi không mong muốn khi tải trạng thái: {str(e)}. Sử dụng trạng thái mặc định.")
#         initialize_default_state()
#
#
# def initialize_default_state():
#     """
#     Khởi tạo trạng thái mặc định.
#     """
#     global current_model_type, current_class_names
#     current_model_type = "66"  # Mặc định là 66 class
#     current_class_names = []   # Danh sách class trống
#     logger.info("Đã khởi tạo trạng thái mặc định: model_type=66, class_count=0.")
#
#
# # Hàm tải mô hình
# def load_model(model_path, model_type):
#     global current_model, current_class_names, current_model_type
#     try:
#         logger.info(f"Before loading model - Current state: model_type={current_model_type}, class_count={len(current_class_names) if current_class_names else None}")
#         logger.info(f"Đang tải mô hình từ: {model_path}")
#         # Kiểm tra file mô hình tồn tại
#         if not os.path.exists(model_path):
#             raise FileNotFoundError(f"Không tìm thấy file mô hình tại: {model_path}")
#         current_model = tf.keras.models.load_model(model_path)
#         if model_type == "66":
#             current_class_names = CLASS_NAMES_66
#         else:  # model_type == "67"
#             current_class_names = CLASS_NAMES_67
#         current_model_type = model_type
#         logger.info(f"After loading model - Updated state: model_type={current_model_type}, class_count={len(current_class_names)}")
#         # Lưu trạng thái vào file
#         save_state()
#     except Exception as e:
#         logger.error(f"Lỗi khi tải mô hình từ {model_path}: {e}")
#         raise
#
# # Tải trạng thái từ file khi khởi động
#
#
# # Tải mô hình mặc định nếu chưa có trạng thái
# if current_model_type is None:
#     try:
#         default_model_path = os.path.join(os.path.dirname(__file__), DEFAULT_MODEL)
#         logger.info(f"Khởi tạo mô hình mặc định tại: {default_model_path}")
#         load_model(default_model_path, "66")
#     except Exception as e:
#         logger.error(f"Lỗi khi tải mô hình mặc định: {e}")
#         raise
#
# # Hàm để người dùng thay đổi mô hình
# def switch_model(model_path=None, class_names=None):
#     global current_model, current_class_names, current_model_type
#     logger.info(f"Before switch - Current state: model_type={current_model_type}, class_count={len(current_class_names) if current_class_names else None}")
#     if model_path:  # Nếu người dùng truyền mô hình tùy chỉnh
#         try:
#             logger.info(f"Đang tải mô hình tùy chỉnh từ: {model_path}")
#             current_model = tf.keras.models.load_model(model_path)
#             if class_names:  # Nếu người dùng truyền danh sách class
#                 current_class_names = class_names
#                 current_model_type = "custom"
#             else:
#                 raise ValueError("Danh sách class phải được cung cấp khi sử dụng mô hình tùy chỉnh.")
#             logger.info(f"Đã cập nhật danh sách class: {len(current_class_names)} class")
#             save_state()
#         except Exception as e:
#             logger.error(f"Lỗi khi tải mô hình tùy chỉnh từ {model_path}: {e}")
#             raise
#     else:  # Chuyển đổi giữa 66 và 67
#         # Kiểm tra nếu trạng thái không hợp lệ
#         if current_model_type not in ["66", "67"]:
#             logger.error(f"Trạng thái mô hình không hợp lệ: {current_model_type}")
#             raise ValueError(f"Trạng thái mô hình không hợp lệ: {current_model_type}. Vui lòng reset mô hình về trạng thái mặc định.")
#         if current_model_type == "66":
#             load_model(MODEL_PATH_67, "67")
#         elif current_model_type == "67":
#             load_model(MODEL_PATH_66, "66")
#     logger.info(f"After switch - Updated state: model_type={current_model_type}, class_count={len(current_class_names)}")
#
import tensorflow as tf
import logging
import os
import json
import tensorflow.keras as k3
from config import MODEL_PATH_66, MODEL_PATH_67, DEFAULT_MODEL

# Cấu hình logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Đường dẫn tới file lưu trạng thái
STATE_FILE = "/app/model_state.json"

# Danh sách class cho mô hình 66 class (không có 'Bát Tràng Men đỏ')
CLASS_NAMES_66 = [
    'Bát Tràng Men chảy', 'Bát Tràng Men hoa biến', 'Bát Tràng Men lam',
    'Bát Tràng Men nâu', 'Bát Tràng Men nâu da lươn', 'Bát Tràng Men ngọc',
    'Bát Tràng Men rạn', 'Bát Tràng Men trắng', 'Bát Tràng Men vàng',
    'Bàu Trúc Men nâu đất', 'Cây Mai Men Nâu', 'Cây Mai Men Xanh Lam',
    'Cây Mai Men xanh rêu', 'Chu Đậu Men nâu', 'Chu Đậu Men ngọc',
    'Chu Đậu Men trắng chấm', 'Đồng Triều Men đỏ', 'Đồng Triều Men nâu',
    'Đồng Triều Men trắng vẽ lam', 'Đồng Triều Men vàng đất',
    'Gốm Biên Hòa Men Nâu', 'Gốm Biên Hòa Men Xanh Đồng Trổ Bông',
    'Gốm Biên Hòa Men Xanh Lục', 'Gốm Biên Hòa Men nâu Da lươn',
    'Gốm Biên Hòa Men xanh lam', 'Gốm Bình Dương Men Đen Xanh Chảy',
    'Gốm Bình Dương Men Nâu', 'Gốm Bình Dương Men Trắng',
    'Gốm Bình Dương Men Xanh', 'Gốm Cây Men lam nhạt', 'Gốm Cây Men trắng',
    'Gốm Cây Men xanh ngọc', 'Gốm Bồ Bát Men trắng đục',
    'Gốm Bồ Bát Men trắng vẽ lam', 'Gốm Gia Thủy Màu nâu đất',
    'Gốm Gò Sành (Bình Định) Men nâu', 'Gốm Hoa Nâu Men trắng nâu',
    'Gốm Hương Canh Vuốt tay không men', 'Gốm Kim Lan Men chảy giả cổ',
    'Gốm Kim Lan Men nâu', 'Gốm Kim Lan Men xanh', 'Gốm Lai Thêu Màu đất nung',
    'Gốm Lai Thêu Men nâu da lươn', 'Gốm Lai Thêu Men ngũ thái',
    'Gốm Lai Thêu Men trắng', 'Gốm Lai Thêu Men xanh',
    'Gốm Lai Thêu Men xanh lục', 'Gốm Lai Thêu Men xanh trắng',
    'Gốm Mường Chanh Màu đất nung (không men)', 'Gốm Mỹ Thiên Màu đất nung',
    'Gốm Mỹ Thiên Men vàng nâu', 'Gốm Mỹ Thiên Men xanh lá',
    'Gốm Quảng Đức Men nâu', 'Gốm Quảng Đức Men xanh lục',
    'Gốm Sa Huỳnh Màu đỏ đất nung', 'Gốm Thanh Lễ Men màu',
    'Phù Lãng Men Nâu da lươn', 'Phù Lãng Men nâu đen', 'Phù Lãng Men nâu đỏ',
    'Phước Tích Men nâu hoàng gia', 'Phước Tích Men xám đen',
    'Tân Vân Men Nâu', 'Tân Vân Men Xanh', 'Thanh Hà Men đỏ cam',
    'Thổ Hà Men nâu', 'Vĩnh Long Men đỏ'
]

# Danh sách class cho mô hình 67 class (có thêm 'Bát Tràng Men đỏ')
CLASS_NAMES_67 = [
    'Bát Tràng Men chảy', 'Bát Tràng Men đỏ', 'Bát Tràng Men hoa biến', 'Bát Tràng Men lam',
    'Bát Tràng Men nâu', 'Bát Tràng Men nâu da lươn', 'Bát Tràng Men ngọc',
    'Bát Tràng Men rạn', 'Bát Tràng Men trắng', 'Bát Tràng Men vàng',
    'Bàu Trúc Men nâu đất', 'Cây Mai Men Nâu', 'Cây Mai Men Xanh Lam',
    'Cây Mai Men xanh rêu', 'Chu Đậu Men nâu', 'Chu Đậu Men ngọc',
    'Chu Đậu Men trắng chấm', 'Đồng Triều Men đỏ', 'Đồng Triều Men nâu',
    'Đồng Triều Men trắng vẽ lam', 'Đồng Triều Men vàng đất',
    'Gốm Biên Hòa Men Nâu', 'Gốm Biên Hòa Men Xanh Đồng Trổ Bông',
    'Gốm Biên Hòa Men Xanh Lục', 'Gốm Biên Hòa Men nâu Da lươn',
    'Gốm Biên Hòa Men xanh lam', 'Gốm Bình Dương Men Đen Xanh Chảy',
    'Gốm Bình Dương Men Nâu', 'Gốm Bình Dương Men Trắng',
    'Gốm Bình Dương Men Xanh', 'Gốm Cây Men lam nhạt', 'Gốm Cây Men trắng',
    'Gốm Cây Men xanh ngọc', 'Gốm Bồ Bát Men trắng đục',
    'Gốm Bồ Bát Men trắng vẽ lam', 'Gốm Gia Thủy Màu nâu đất',
    'Gốm Gò Sành (Bình Định) Men nâu', 'Gốm Hoa Nâu Men trắng nâu',
    'Gốm Hương Canh Vuốt tay không men', 'Gốm Kim Lan Men chảy giả cổ',
    'Gốm Kim Lan Men nâu', 'Gốm Kim Lan Men xanh', 'Gốm Lai Thêu Màu đất nung',
    'Gốm Lai Thêu Men nâu da lươn', 'Gốm Lai Thêu Men ngũ thái',
    'Gốm Lai Thêu Men trắng', 'Gốm Lai Thêu Men xanh',
    'Gốm Lai Thêu Men xanh lục', 'Gốm Lai Thêu Men xanh trắng',
    'Gốm Mường Chanh Màu đất nung (không men)', 'Gốm Mỹ Thiên Màu đất nung',
    'Gốm Mỹ Thiên Men vàng nâu', 'Gốm Mỹ Thiên Men xanh lá',
    'Gốm Quảng Đức Men nâu', 'Gốm Quảng Đức Men xanh lục',
    'Gốm Sa Huỳnh Màu đỏ đất nung', 'Gốm Thanh Lễ Men màu',
    'Phù Lãng Men Nâu da lươn', 'Phù Lãng Men nâu đen', 'Phù Lãng Men nâu đỏ',
    'Phước Tích Men nâu hoàng gia', 'Phước Tích Men xám đen',
    'Tân Vân Men Nâu', 'Tân Vân Men Xanh', 'Thanh Hà Men đỏ cam',
    'Thổ Hà Men nâu', 'Vĩnh Long Men đỏ'
]

# Biến toàn cục để lưu mô hình
current_model = None
current_class_names = None
current_model_type = None

# Hàm lưu trạng thái vào file
def save_state():
    state = {
        "model_type": current_model_type,
        "class_names": current_class_names
    }
    with open(STATE_FILE, "w") as f:
        json.dump(state, f)
    logger.info(f"Saved state to {STATE_FILE}: {state}")

# Hàm tải trạng thái từ file
def load_state():
    global current_model_type, current_class_names
    try:
        if os.path.exists(STATE_FILE):
            with open(STATE_FILE, "r") as f:
                state = json.load(f)
                if "model_type" in state and "class_names" in state:
                    current_model_type = state["model_type"]
                    current_class_names = state["class_names"]
                    logger.info(
                        f"Trạng thái đã được tải từ {STATE_FILE}: "
                        f"model_type={current_model_type}, class_count={len(current_class_names)}"
                    )
                else:
                    raise ValueError("Định dạng tệp trạng thái không hợp lệ.")
        else:
            logger.warning(f"Tệp trạng thái {STATE_FILE} không tồn tại. Sử dụng trạng thái mặc định.")
            initialize_default_state()
    except (json.JSONDecodeError, ValueError) as e:
        logger.error(f"Lỗi khi đọc tệp trạng thái: {str(e)}. Sử dụng trạng thái mặc định.")
        initialize_default_state()
    except Exception as e:
        logger.critical(f"Lỗi không mong muốn khi tải trạng thái: {str(e)}. Sử dụng trạng thái mặc định.")
        initialize_default_state()

def initialize_default_state():
    global current_model_type, current_class_names
    current_model_type = "66"  # Mặc định là 66 class
    current_class_names = CLASS_NAMES_66
    logger.info("Đã khởi tạo trạng thái mặc định: model_type=66, class_count=66.")

# Hàm tải mô hình
def load_model(model_path, model_type):
    global current_model, current_class_names, current_model_type
    try:
        logger.info(f"Before loading model - Current state: model_type={current_model_type}, class_count={len(current_class_names) if current_class_names else None}")
        logger.info(f"Đang tải mô hình từ: {model_path}")
        # Kiểm tra file mô hình tồn tại
        if not os.path.exists(model_path):
            raise FileNotFoundError(f"Không tìm thấy file mô hình tại: {model_path}")
        # Tải mô hình từ đường dẫn
        current_model = tf.keras.models.load_model(model_path)
        # Gán danh sách class dựa trên model_type
        if model_type == "66":
            current_class_names = CLASS_NAMES_66
        else:  # model_type == "67"
            current_class_names = CLASS_NAMES_67
        current_model_type = model_type
        logger.info(f"After loading model - Updated state: model_type={current_model_type}, class_count={len(current_class_names)}")
        # Lưu trạng thái vào file
        save_state()
    except Exception as e:
        logger.error(f"Lỗi khi tải mô hình từ {model_path}: {e}")
        raise

# Tải trạng thái từ file khi khởi động
load_state()

# Tải mô hình mặc định nếu chưa có trạng thái
if current_model is None:
    try:
        default_model_path = os.path.join('/app', DEFAULT_MODEL)
        logger.info(f"Khởi tạo mô hình mặc định tại: {default_model_path}")
        load_model(default_model_path, "66")
    except Exception as e:
        logger.error(f"Lỗi khi tải mô hình mặc định: {e}")
        raise

# Hàm để người dùng thay đổi mô hình
def switch_model(model_path=None, class_names=None):
    global current_model, current_class_names, current_model_type
    logger.info(f"Before switch - Current state: model_type={current_model_type}, class_count={len(current_class_names) if current_class_names else None}")
    if model_path:  # Nếu người dùng truyền mô hình tùy chỉnh
        try:
            logger.info(f"Đang tải mô hình tùy chỉnh từ: {model_path}")
            current_model = tf.keras.models.load_model(model_path)
            if class_names:  # Nếu người dùng truyền danh sách class
                current_class_names = class_names
                current_model_type = "custom"
            else:
                raise ValueError("Danh sách class phải được cung cấp khi sử dụng mô hình tùy chỉnh.")
            logger.info(f"Đã cập nhật danh sách class: {len(current_class_names)} class")
            save_state()
        except Exception as e:
            logger.error(f"Lỗi khi tải mô hình tùy chỉnh từ {model_path}: {e}")
            raise
    else:  # Chuyển đổi giữa 66 và 67
        if current_model_type not in ["66", "67"]:
            logger.error(f"Trạng thái mô hình không hợp lệ: {current_model_type}")
            raise ValueError(f"Trạng thái mô hình không hợp lệ: {current_model_type}. Vui lòng reset mô hình về trạng thái mặc định.")
        if current_model_type == "66":
            load_model(MODEL_PATH_67, "67")
        elif current_model_type == "67":
            load_model(MODEL_PATH_66, "66")
    logger.info(f"After switch - Updated state: model_type={current_model_type}, class_count={len(current_class_names)}")