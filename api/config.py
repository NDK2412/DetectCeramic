# import os
# from dotenv import load_dotenv
#
# # Load biến môi trường từ file .env
# load_dotenv()
#
# # Cấu hình
# GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY", "AIzaSyAH7ynNF7vZDM-3y34Bz4eMGNjqq0sUv6E")  # Thay bằng key mới
# TEXT_FOLDER = "D:\\PY_Code\\GD5\\texts"
# IMAGE_DIR = "D:\\PY_Code\\SecondModel\\images"
# MODEL_PATH_66 = "D:\\PY_Code\\Ceramic_Detection\\xception_66class_model.h5"  # Mô hình 66 class
# MODEL_PATH_67 = "D:\\PY_Code\\Ceramic_Detection\\xception_67class_model.h5"  # Mô hình 67 class
# DEFAULT_MODEL = "xception_66class_model.h5"  # Mô hình mặc định khi khởi động
#
# # Đảm bảo thư mục tồn tại
# os.makedirs(TEXT_FOLDER, exist_ok=True)
#
# # API key cho FastAPI
# API_KEY = "AuwTLoaTGAWYm2HmDzV0i9ahfemzky"  # Giữ nguyên




import os
from dotenv import load_dotenv

# Load biến môi trường từ file .env
load_dotenv()

# Cấu hình
GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY", "AIzaSyAH7ynNF7vZDM-3y34Bz4eMGNjqq0sUv6E")
TEXT_FOLDER = "/app/texts"
IMAGE_DIR = "/app/images"
MODEL_PATH_66 = "/app/ultis/xception_66class_model.h5"  # Đường dẫn tuyệt đối
MODEL_PATH_67 = "/app/ultis/xception_67class_model.h5"  # Đường dẫn tuyệt đối
# MODEL_PATH_66 = os.getenv("MODEL_PATH_66", "/app/models/xception_66class_model.h5")
# MODEL_PATH_67 = os.getenv("MODEL_PATH_67", "/app/models/xception_67class_model.h5")

DEFAULT_MODEL = "/app/ultis/xception_66class_model.h5"  # Chỉ tên file, sẽ kết hợp với /app

# Đảm bảo thư mục tồn tại
os.makedirs(TEXT_FOLDER, exist_ok=True)

# API key cho FastAPI
API_KEY = "AuwTLoaTGAWYm2HmDzV0i9ahfemzky"