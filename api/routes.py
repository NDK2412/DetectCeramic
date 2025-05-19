import base64
import time
from collections import Counter

from fastapi import FastAPI, UploadFile, File, Request, HTTPException, Header, Form
from fastapi.responses import HTMLResponse, JSONResponse
from fastapi.staticfiles import StaticFiles
from openai import OpenAI
from pydantic import BaseModel
import os
import logging
import numpy as np
from models import current_model, current_class_names, switch_model, current_model_type, load_model, load_state
from utils import preprocess_image
from retrieval import get_ceramic_info
from config import IMAGE_DIR, GOOGLE_API_KEY, DEFAULT_MODEL
from system_controller import SystemController
import asyncio
from threading import Lock
from fastapi import FastAPI, UploadFile, File, Request, HTTPException, Header
from fastapi.responses import HTMLResponse
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel
import os
import logging
import numpy as np
import google.generativeai as genai
from PIL import Image
import io
import base64
from openai import OpenAI
from models import current_model, current_class_names
from utils import preprocess_image
from retrieval import get_ceramic_info
from config import IMAGE_DIR, GOOGLE_API_KEY
from system_controller import SystemController
state_lock = Lock()
model_lock = asyncio.Lock()

# Cấu hình logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Khởi tạo FastAPI với cấu hình tài liệu chi tiết
app = FastAPI(
    title="API Phân loại Gốm Sứ",
    description="""**API hỗ trợ phân loại đồ gốm sứ** bằng mô hình học máy kết hợp với LLM để cung cấp thông tin chi tiết. API được thiết kế để hỗ trợ các nhà khảo cổ học, nhà sưu tầm, và người dùng phổ thông trong việc nhận diện và tìm hiểu về các loại gốm sứ.

    ### Chức năng chính:
    - **Phân loại ảnh gốm sứ**: Nhận diện loại gốm sứ từ ảnh đầu vào (hỗ trợ 66 hoặc 67 lớp phân loại).
    - **Tích hợp chatbot**: Cung cấp thông tin chi tiết về gốm sứ thông qua giao tiếp với mô hình ngôn ngữ lớn (LLM).
    - **Quản lý mô hình**: Cho phép chuyển đổi giữa các mô hình phân loại (66 lớp, 67 lớp, hoặc mô hình tùy chỉnh).
    - **Quản lý hệ thống**: Theo dõi hiệu năng và trạng thái hệ thống (CPU, RAM, GPU).

    ### Yêu cầu chung:
    - Tất cả các endpoint yêu cầu xác thực thông qua API key (truyền qua header `api_key`).
    - API key mặc định: `AuwTLoaTGAWYm2HmDzV0i9ahfemzky` (có thể thay đổi qua biến môi trường `API_KEY`).
    """,
    version="1.0.0",
    contact={
        "name": "Nhóm phát triển",
        "email": "support@ceramic-classification.com",
    },
    license_info={
        "name": "MIT",
        "url": "https://opensource.org/licenses/MIT"
    },
    openapi_tags=[
        {
            "name": "Phân loại",
            "description": "Các endpoint liên quan đến việc phân loại ảnh gốm sứ và nhận diện loại gốm."
        },
        {
            "name": "Chat",
            "description": "Tương tác với chatbot để tìm hiểu thông tin chi tiết về gốm sứ."
        },
        {
            "name": "Quản lý Mô hình",
            "description": "Quản lý và chuyển đổi giữa các mô hình phân loại (66 lớp, 67 lớp, hoặc mô hình tùy chỉnh)."
        },
        {
            "name": "LLM",
            "description": "Quản lý cấu hình mô hình ngôn ngữ lớn (LLM) để cung cấp thông tin chi tiết."
        },
        {
            "name": "Hệ thống",
            "description": "Theo dõi và giám sát hiệu năng hệ thống (CPU, RAM, GPU)."
        }
    ],
    docs_url="/api-docs",
    redoc_url=None,
    openapi_url="/api/openapi.json",
    swagger_ui_parameters={"defaultModelsExpandDepth": -1}
)


@app.get("/")
def read_root():
    return {"message": "Application is running!"}

# Biến toàn cục lưu cấu hình LLM
llm_config = {
    "provider": "gemini",
    "api_key": GOOGLE_API_KEY
}

# Model cho dữ liệu cập nhật LLM
class LLMConfig(BaseModel):
    """
    Cấu hình cho mô hình ngôn ngữ lớn (LLM).

    Attributes:
        model (str): Loại mô hình LLM, chỉ chấp nhận giá trị 'gemini' hoặc 'openai'.
        api_key (str): Khóa API để truy cập dịch vụ LLM (ví dụ: Google Gemini API key hoặc OpenAI API key).
    """
    model: str
    api_key: str

# Model cho yêu cầu chuyển đổi mô hình
class ModelSwitchRequest(BaseModel):
    """
    Yêu cầu chuyển đổi mô hình phân loại.

    Attributes:
        model_path (str, optional): Đường dẫn đến file mô hình tùy chỉnh (định dạng .h5). Bắt buộc nếu sử dụng mô hình tùy chỉnh.
        class_names (list, optional): Danh sách tên các lớp phân loại (strings) khi sử dụng mô hình tùy chỉnh. Bắt buộc nếu cung cấp `model_path`.
        model_type (str, optional): Loại mô hình mặc định ('66' hoặc '67'). Bắt buộc nếu không cung cấp `model_path`.
    """
    model_path: str = None
    class_names: list = None
    model_type: str = None



# Lấy danh sách ảnh mẫu
@app.get(
    "/images",
    tags=["Phân loại"],
    summary="Lấy danh sách ảnh mẫu",
    description="Trả về danh sách các tệp ảnh mẫu (định dạng .jpg, .jpeg, .png) trong thư mục `IMAGE_DIR`. Dùng để hiển thị các ảnh mẫu trên giao diện hoặc kiểm tra hệ thống.",
    responses={
        200: {
            "description": "Danh sách các tệp ảnh mẫu",
            "content": {
                "application/json": {
                    "example": {
                        "images": ["image1.jpg", "image2.png", "image3.jpeg"]
                    }
                }
            }
        }
    }
)
async def get_images():
    image_files = [f for f in os.listdir(IMAGE_DIR) if f.endswith(('.jpg', '.jpeg', '.png'))]
    return {"images": image_files}

# Router chat độc lập
@app.post(
    "/chat",
    tags=["Chat"],
    summary="Tương tác với chatbot về gốm sứ",
    description="""Gửi một tin nhắn hỏi về thông tin liên quan đến gốm sứ, chatbot sẽ trả về câu trả lời dựa trên mô hình ngôn ngữ lớn (LLM).

    **Đầu vào:**
    - Header `api_key`: Khóa API để xác thực.
    - Body JSON chứa trường `message` (chuỗi) là nội dung câu hỏi.

    **Phản hồi:**
    - Trường `response` chứa câu trả lời từ LLM.

    **Lưu ý:**
    - API key phải khớp với giá trị được thiết lập trong biến môi trường `API_KEY` (mặc định: `AuwTLoaTGAWYm2HmDzV0i9ahfemzky`).
    - Nếu tin nhắn rỗng, API sẽ trả về thông báo lỗi.
    """,
    responses={
        200: {
            "description": "Phản hồi thành công từ chatbot",
            "content": {
                "application/json": {
                    "example": {
                        "response": "Gốm Bát Tràng là loại gốm nổi tiếng ở Việt Nam, được sản xuất từ làng Bát Tràng, Gia Lâm, Hà Nội..."
                    }
                }
            }
        },
        401: {
            "description": "Xác thực thất bại do API key không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Khóa API không hợp lệ"}
                }
            }
        },
        500: {
            "description": "Lỗi server khi xử lý yêu cầu",
            "content": {
                "application/json": {
                    "example": {"response": "Lỗi: Không thể kết nối với LLM"}
                }
            }
        }
    }
)
async def chat(request: Request, api_key: str = Header(...)):
    try:
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        data = await request.json()
        message = data.get("message", "").strip()

        if not message:
            return {"response": "Vui lòng gửi một tin nhắn hợp lệ."}

        logger.info(f"Nhận tin nhắn từ người dùng: {message}")
        llmm_response = get_ceramic_info(message, llm_config["provider"], llm_config["api_key"])
        logger.info(f"Phản hồi từ {llm_config['provider']}: {llmm_response}")
        return {"response": llmm_response}
    except Exception as e:
        logger.error(f"Lỗi khi xử lý chat: {str(e)}")
        return {"response": f"Lỗi: {str(e)}"}

# Dự đoán và lấy thông tin
@app.post(
    "/predict",
    tags=["Phân loại"],
    summary="Phân loại ảnh gốm sứ và lấy thông tin chi tiết",
    description="""Nhận một ảnh gốm sứ, phân loại loại gốm bằng mô hình học máy. Nếu độ chính xác dự đoán >= 0.75, sử dụng LLM để cung cấp thông tin chi tiết từ văn bản, Gemini Vision (nếu provider là 'gemini'), và xAI Vision (nếu provider là 'xai') để phân tích hình ảnh, sau đó tổng hợp thành một câu trả lời duy nhất. Nếu không, trả về thông báo rằng ảnh không thuộc dòng gốm Việt Nam.

    **Đầu vào:**
    - File ảnh (form-data): Tệp ảnh (.jpg, .jpeg, .png) với tên trường `file`.
    - Header `api_key`: Khóa API để xác thực.

    **Phản hồi:**
    - Nếu độ chính xác >= 0.75:
      - Trường `predicted_class`: Tên loại gốm được dự đoán.
      - Trường `combined_response`: Thông tin tổng hợp từ LLM, Gemini Vision, và xAI Vision.
      - Trường `confidence`: Độ chính xác của dự đoán.
    - Nếu độ chính xác < 0.75:
      - Trường `message`: "Đây không phải là dòng gốm Việt Nam".
      - Trường `confidence`: Độ chính xác của dự đoán.
    - Nếu lỗi:
      - Trường `error`: Thông báo lỗi.

    **Lưu ý:**
    - API key phải hợp lệ.
    - Ảnh cần có định dạng hợp lệ và không bị hỏng.
    - Gemini Vision và xAI Vision chỉ được gọi nếu provider tương ứng được chọn và độ chính xác >= 0.75.
    - Nếu LLM, Gemini Vision, hoặc xAI Vision không thể cung cấp thông tin, phản hồi sẽ chứa thông báo lỗi.
    """,
    responses={
        200: {
            "description": "Kết quả phân loại và thông tin chi tiết hoặc thông báo không phải gốm Việt Nam",
            "content": {
                "application/json": {
                    "examples": {
                        "success_with_combined_response": {
                            "summary": "Dự đoán thành công với độ chính xác cao",
                            "value": {
                                "predicted_class": "Gốm Bát Tràng",
                                "combined_response": "Gốm Bát Tràng là loại gốm nổi tiếng ở Việt Nam, được sản xuất từ làng Bát Tràng, Gia Lâm, Hà Nội. Hình ảnh cho thấy một bình gốm với men lam đặc trưng, hoa văn rồng tinh xảo, kiểu dáng cổ điển.",
                                "confidence": 0.92
                            }
                        },
                        "not_vietnamese_ceramic": {
                            "summary": "Độ chính xác thấp, không phải gốm Việt Nam",
                            "value": {
                                "message": "Đây không phải là dòng gốm Việt Nam",
                                "confidence": 0.65
                            }
                        }
                    }
                }
            }
        },
        401: {
            "description": "Xác thực thất bại do API key không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Khóa API không hợp lệ"}
                }
            }
        },
        500: {
            "description": "Lỗi server khi xử lý ảnh, LLM, Gemini Vision, hoặc xAI Vision",
            "content": {
                "application/json": {
                    "example": {
                        "error": "Lỗi: Không thể xử lý ảnh. Vui lòng kiểm tra API key hoặc thử lại."
                    }
                }
            }
        }
    }
)

async def predict(
    file: UploadFile = File(...),
    api_key: str = Header(...),
):
    try:
        # Xác thực API key
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        # Đọc và xử lý ảnh
        image_bytes = await file.read()
        logger.info(f"Đang xử lý ảnh: {file.filename}")
        processed_image = preprocess_image(image_bytes)
        predictions = current_model.predict(processed_image)
        predicted_class_idx = np.argmax(predictions[0])
        confidence = float(np.max(predictions[0]))  # Độ chính xác cao nhất

        # Kiểm tra ngưỡng độ chính xác
        if confidence < 0.75:
            logger.info(f"Độ chính xác {confidence} < 0.75, không phải gốm Việt Nam")
            return {
                "predicted_class": "Không xác định",
                "llm_response": "Đây không phải là dòng gốm Việt Nam",
                "confidence": confidence,
            }

        # Dự đoán ban đầu
        initial_predicted_class = current_class_names[predicted_class_idx]
        logger.info(f"Dự đoán ban đầu: {initial_predicted_class} với độ chính xác {confidence}")

        # Cấu hình và gọi Gemini Vision 5 lần để xác minh
        gemini_predictions = []
        foreign_keywords = ["china", "japan", "korea", "thai", "europe", "america", "foreign"]

        genai.configure(api_key=llm_config["api_key"])
        gemini_model = genai.GenerativeModel("gemini-2.0-flash")
        img = Image.open(io.BytesIO(image_bytes))
        prompt = (
            "cho tôi biết đây là dòng gốm gì, "
            "(Chỉ trả lời dòng gốm và loại men)"
        )

        for i in range(5):
            try:
                response = gemini_model.generate_content([prompt, img])
                prediction = (response.text or "").strip().lower()
                gemini_predictions.append(prediction)
                logger.info(f"Gemini Vision lần {i+1}: {prediction}")

                # Kiểm tra gốm nước ngoài
                if any(keyword in prediction for keyword in foreign_keywords):
                    logger.info(f"Phát hiện gốm nước ngoài trong lần {i+1}: {prediction}")
                    return {
                        "predicted_class": "Không phải dòng gốm Việt Nam",
                        "llm_response": "Không có kết quả truy vấn",
                        "confidence": confidence,
                    }
            except Exception as e:
                logger.error(f"Lỗi khi gọi Gemini Vision lần {i+1}: {e}")
                gemini_predictions.append("")

        # Lọc và xác định kết quả đồng thuận
        valid_predictions = [p for p in gemini_predictions if p]
        all_predictions = [initial_predicted_class] + valid_predictions

        if valid_predictions:
            most_common = Counter(all_predictions).most_common(1)[0][0]
            logger.info(f"Kết quả đồng thuận: {most_common}")
            most_common_prediction = most_common
        else:
            most_common_prediction = initial_predicted_class
            logger.warning("Không có dự đoán hợp lệ từ Gemini, sử dụng dự đoán ban đầu")

        # Lấy thông tin chi tiết từ LLM (văn bản)
        llm_response = get_ceramic_info(
            most_common_prediction, llm_config["provider"], llm_config["api_key"]
        )
        if "Lỗi" in llm_response:
            llm_response = "Không có thông tin văn bản chi tiết."

        # Lấy mô tả chi tiết từ Gemini Vision
        try:
            detailed_prompt = (
                f"Hình ảnh này là một sản phẩm gốm sứ, được xác định là '"
                f"{most_common_prediction}'. Hãy cung cấp mô tả chi tiết "
                "bằng tiếng Việt về đặc điểm của gốm trong hình, bao gồm: "
                "màu sắc, hoa văn, kiểu dáng, bất kỳ đặc trưng nào nổi bật, "
                "lịch sử hình thành, giá bán, và các đặc điểm nhận diện chính."
            )
            response = gemini_model.generate_content([detailed_prompt, img])
            gemini_vision_response = response.text or "Không thể lấy thông tin chi tiết từ Gemini Vision."
            logger.info(f"Phản hồi chi tiết từ Gemini Vision: {gemini_vision_response[:100]}...")
        except Exception as e:
            logger.error(f"Lỗi khi gọi Gemini Vision cho mô tả chi tiết: {e}")
            gemini_vision_response = "Không thể lấy thông tin chi tiết từ Gemini Vision."

        # Tổng hợp phản hồi cuối cùng
        combined_response = llm_response
        if gemini_vision_response:
            combined_response += f" {gemini_vision_response}"

        if combined_response.strip() == llm_response and "Không có thông tin" in llm_response:
            combined_response = "Không có thông tin chi tiết về gốm sứ này."

        PROMPT_TEMPLATE = (
            "Kết quả dự đoán cuối cùng sau khi quan sát ảnh, mô tả, "
            "lịch sử hình thành, giá bán, đặc điểm nhận diện: {response}"
        )
        final_response = PROMPT_TEMPLATE.format(response=combined_response.strip())

        logger.info(f"Kết quả cuối cùng: {most_common_prediction} với độ chính xác: {confidence}")
        return {
            "predicted_class": most_common_prediction,
            "llm_response": final_response,
            "confidence": confidence,
        }

    except Exception as e:
        logger.error(f"Lỗi khi xử lý: {e}")
        return {
            "predicted_class": "Không xác định",
            "llm_response": "Lỗi: Không thể xử lý ảnh. Vui lòng kiểm tra API key hoặc thử lại.",
            "confidence": 0.0,
        }

# Reset mô hình về trạng thái mặc định
@app.post(
    "/reset-model",
    tags=["Quản lý Mô hình"],
    summary="Reset mô hình về trạng thái mặc định (66 lớp)",
    description="""Đưa mô hình phân loại về trạng thái mặc định (66 lớp phân loại) được định nghĩa trong `DEFAULT_MODEL`.

    **Đầu vào:**
    - Header `api_key`: Khóa API để xác thực.

    **Phản hồi:**
    - Trường `status`: Trạng thái của yêu cầu (`success`).
    - Trường `message`: Thông báo chi tiết về kết quả.
    - Trường `class_count`: Số lượng lớp phân loại sau khi reset.

    **Lưu ý:**
    - API key phải hợp lệ.
    - Mô hình mặc định phải tồn tại tại đường dẫn được định nghĩa trong `DEFAULT_MODEL`.
    """,
    responses={
        200: {
            "description": "Reset mô hình thành công",
            "content": {
                "application/json": {
                    "example": {
                        "status": "success",
                        "message": "Mô hình đã được reset về trạng thái mặc định: 66 class",
                        "class_count": 66
                    }
                }
            }
        },
        401: {
            "description": "Xác thực thất bại do API key không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Khóa API không hợp lệ"}
                }
            }
        },
        500: {
            "description": "Lỗi server khi reset mô hình",
            "content": {
                "application/json": {
                    "example": {"detail": "Lỗi khi reset mô hình: Không tìm thấy file mô hình mặc định"}
                }
            }
        }
    }
)
async def reset_model(api_key: str = Header(...)):
    try:
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        load_model(os.path.join(os.path.dirname(__file__), DEFAULT_MODEL), "66")
        logger.info("Mô hình đã được reset về trạng thái mặc định: 66 class")

        return {
            "status": "success",
            "message": "Mô hình đã được reset về trạng thái mặc định: 66 class",
            "class_count": len(current_class_names)
        }
    except Exception as e:
        logger.error(f"Lỗi khi reset mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Lỗi khi reset mô hình: {str(e)}")

# Chuyển đổi mô hình
@app.post(
    "/switch-model",
    tags=["Quản lý Mô hình"],
    summary="Chuyển đổi giữa các mô hình phân loại",
    description="""Cho phép chuyển đổi giữa mô hình 66 lớp, 67 lớp, hoặc sử dụng mô hình tùy chỉnh.

    **Đầu vào:**
    - Header `api_key`: Khóa API để xác thực.
    - Body JSON chứa các trường:
        - `model_path` (str, optional): Đường dẫn đến file mô hình tùy chỉnh (định dạng .h5).
        - `class_names` (list, optional): Danh sách tên các lớp phân loại (strings), bắt buộc nếu sử dụng `model_path`.
        - `model_type` (str, optional): Loại mô hình mặc định ('66' hoặc '67'), bắt buộc nếu không cung cấp `model_path`.

    **Phản hồi:**
    - Trường `status`: Trạng thái của yêu cầu (`success`).
    - Trường `message`: Thông báo chi tiết về kết quả.
    - Trường `class_count`: Số lượng lớp phân loại sau khi chuyển đổi.

    **Lưu ý:**
    - API key phải hợp lệ.
    - Nếu sử dụng mô hình tùy chỉnh, `model_path` phải trỏ đến file .h5 hợp lệ và `class_names` không được rỗng.
    - Nếu không cung cấp `model_path`, `model_type` phải là '66' hoặc '67'.
    - Quá trình chuyển đổi được đồng bộ hóa để tránh xung đột.
    """,
    responses={
        200: {
            "description": "Chuyển đổi mô hình thành công",
            "content": {
                "application/json": {
                    "example": {
                        "status": "success",
                        "message": "Đã chuyển đổi mô hình thành công",
                        "class_count": 67
                    }
                }
            }
        },
        400: {
            "description": "Dữ liệu đầu vào không hợp lệ",
            "content": {
                "application/json": {
                    "example": {
                        "detail": "model_type phải là '66' hoặc '67'"
                    }
                }
            }
        },
        401: {
            "description": "Xác thực thất bại do API key không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Khóa API không hợp lệ"}
                }
            }
        },
        500: {
            "description": "Lỗi server khi chuyển đổi mô hình",
            "content": {
                "application/json": {
                    "example": {"detail": "Lỗi khi chuyển đổi mô hình: File mô hình không hợp lệ"}
                }
            }
        }
    }
)
async def switch_model_endpoint(request: ModelSwitchRequest, api_key: str = Header(...)):
    try:
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            logger.error("Khóa API không hợp lệ")
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        with state_lock:
            load_state()

            if request.model_path:
                if not os.path.exists(request.model_path):
                    logger.error("Đường dẫn mô hình không tồn tại.")
                    raise HTTPException(status_code=400, detail="Đường dẫn mô hình không tồn tại.")
                if not request.model_path.endswith('.h5'):
                    logger.error("Mô hình phải có định dạng .h5.")
                    raise HTTPException(status_code=400, detail="Mô hình phải có định dạng .h5.")
                if not request.class_names or len(request.class_names) <= 0:
                    logger.error("Danh sách class phải được cung cấp và không được rỗng khi sử dụng mô hình tùy chỉnh.")
                    raise HTTPException(status_code=400,
                                        detail="Danh sách class phải được cung cấp và không được rỗng khi sử dụng mô hình tùy chỉnh.")
                switch_model(model_path=request.model_path, class_names=request.class_names)
            else:
                if request.model_type not in ["66", "67"]:
                    logger.error("model_type không hợp lệ")
                    raise HTTPException(status_code=400, detail="model_type phải là '66' hoặc '67'.")
                if current_model_type != request.model_type:
                    switch_model()
                else:
                    logger.info(f"Không cần chuyển đổi, mô hình đã ở trạng thái: {request.model_type}")

            load_state()

            if current_model_type not in ["66", "67"] and not request.model_path:
                logger.error("Trạng thái mô hình không hợp lệ sau khi chuyển đổi.")
                raise HTTPException(status_code=500, detail="Trạng thái mô hình không hợp lệ sau khi chuyển đổi.")
            if len(current_class_names) == 0:
                logger.error("Danh sách class bị trống sau khi chuyển đổi.")
                raise HTTPException(status_code=500, detail="Danh sách class bị trống sau khi chuyển đổi.")

        message = f"Đã chuyển đổi mô hình thành công"
        logger.info(message)

        return {
            "status": "success",
            "message": message,
            "class_count": len(current_class_names)
        }
    except Exception as e:
        logger.error(f"Lỗi khi chuyển đổi mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Lỗi khi chuyển đổi mô hình: {str(e)}")

# Cập nhật cấu hình LLM
@app.post(
    "/update-llm",
    tags=["LLM"],
    summary="Cập nhật cấu hình mô hình ngôn ngữ lớn (LLM)",
    description="""Thay đổi nhà cung cấp LLM (Gemini hoặc OpenAI) và API key tương ứng để chatbot sử dụng.

    **Đầu vào:**
    - Header `api_key`: Khóa API để xác thực.
    - Body JSON chứa các trường:
        - `model` (str): Loại mô hình LLM ('gemini' hoặc 'openai').
        - `api_key` (str): Khóa API mới để truy cập dịch vụ LLM.

    **Phản hồi:**
    - Trường `status`: Trạng thái của yêu cầu (`success`).
    - Trường `model`: Nhà cung cấp LLM đã được cập nhật.

    **Lưu ý:**
    - API key phải hợp lệ.
    - Trường `model` chỉ chấp nhận giá trị 'gemini' hoặc 'openai'.
    - Trường `api_key` không được để trống.
    """,
    responses={
        200: {
            "description": "Cập nhật cấu hình LLM thành công",
            "content": {
                "application/json": {
                    "example": {
                        "status": "success",
                        "model": "gemini"
                    }
                }
            }
        },
        400: {
            "description": "Dữ liệu đầu vào không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Nhà cung cấp LLM không hợp lệ. Phải là 'gemini' hoặc 'openai'"}
                }
            }
        },
        401: {
            "description": "Xác thực thất bại do API key không hợp lệ",
            "content": {
                "application/json": {
                    "example": {"detail": "Khóa API không hợp lệ"}
                }
            }
        },
        500: {
            "description": "Lỗi server khi cập nhật cấu hình",
            "content": {
                "application/json": {
                    "example": {"detail": "Lỗi khi cập nhật LLM: Không thể lưu cấu hình"}
                }
            }
        }
    }
)
async def update_llm(config: LLMConfig, api_key: str = Header(...)):
    try:
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        if config.model.lower() not in ["gemini", "openai"]:
            raise HTTPException(status_code=400,
                                detail="Nhà cung cấp LLM không hợp lệ. Phải là 'gemini' hoặc 'openai'.")

        if not config.api_key:
            raise HTTPException(status_code=400, detail="API key không được để trống.")

        llm_config["provider"] = config.model.lower()
        llm_config["api_key"] = config.api_key

        logger.info(f"Cấu hình LLM đã được cập nhật: provider={llm_config['provider']}")
        return {"status": "success", "model": llm_config["provider"]}
    except Exception as e:
        logger.error(f"Lỗi khi cập nhật LLM: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Lỗi khi cập nhật LLM: {str(e)}")

# Lấy thông tin hệ thống
@app.get(
    "/system-stats",
    tags=["Hệ thống"],
    summary="Lấy thông tin hiệu năng hệ thống",
    description="""Trả về các chỉ số hiệu năng và trạng thái của hệ thống, bao gồm việc sử dụng CPU, RAM, và GPU.

    **Phản hồi:**
    - Trường `cpu_usage_percent`: Phần trăm sử dụng CPU (%).
    - Trường `ram_total_mb`: Tổng dung lượng RAM (MB).
    - Trường `ram_used_mb`: Dung lượng RAM đã sử dụng (MB).
    - Trường `ram_usage_percent`: Phần trăm sử dụng RAM (%).
    - Trường `gpu_usage_percent`: Phần trăm sử dụng GPU (%).
    - Trường `gpu_total_mb`: Tổng dung lượng GPU (MB).
    - Trường `gpu_used_mb`: Dung lượng GPU đã sử dụng (MB).

    **Lưu ý:**
    - Yêu cầu quyền truy cập hệ thống để lấy thông tin.
    - Một số chỉ số có thể trả về giá trị mặc định (0) nếu không hỗ trợ trên hệ thống.
    """,
    responses={
        200: {
            "description": "Thông tin hiệu năng hệ thống",
            "content": {
                "application/json": {
                    "example": {
                        "cpu_usage_percent": 45.3,
                        "ram_total_mb": 8192,
                        "ram_used_mb": 4096,
                        "ram_usage_percent": 50.0,
                        "gpu_usage_percent": 20.5,
                        "gpu_total_mb": 2048,
                        "gpu_used_mb": 512
                    }
                }
            }
        },
        500: {
            "description": "Lỗi server khi lấy thông tin hệ thống",
            "content": {
                "application/json": {
                    "example": {"error": "Không thể lấy thông tin hệ thống"}
                }
            }
        }
    }
)
async def get_system_stats():
    return await SystemController.get_system_stats()


# Model cho yêu cầu upload mô hình
class ModelUploadRequest(BaseModel):
    """
    Yêu cầu upload mô hình từ bên ngoài.

    Attributes:
        model_class (str): Class của mô hình (ví dụ: '66').
    """
    model_class: str


# Router upload mô hình
@app.post(
    "/upload-model",
    tags=["Quản lý Mô hình"],
    summary="Upload mô hình từ bên ngoài",
    description="""Cho phép upload một file mô hình từ bên ngoài và lưu vào thư mục `/app/models/`.

    **Đầu vào:**
    - Header `api_key`: Khóa API để xác thực.
    - Form-data:
        - `file`: File mô hình (hỗ trợ định dạng .h5, .pth, .pt, .onnx, tối đa 500MB).
        - `model_class`: Danh sách các class của mô hình (định dạng: "Class1, Class2, Class3").

    **Phản hồi:**
    - Trường `status`: Trạng thái của yêu cầu (`success`).
    - Trường `model_path`: Đường dẫn nơi file được lưu.
    - Trường `model_class`: Danh sách class đã được cung cấp.
    """
)
async def upload_model(file: UploadFile = File(...), model_class: str = Form(...), api_key: str = Header(...)):
    try:
        # Xác thực API key
        expected_api_key = os.getenv("API_KEY", "AuwTLoaTGAWYm2HmDzV0i9ahfemzky")
        if api_key != expected_api_key:
            logger.error("Khóa API không hợp lệ")
            raise HTTPException(status_code=401, detail="Khóa API không hợp lệ")

        # Xác thực định dạng model_class
        import re
        if not re.match(r'^[\w\s]+(,\s*[\w\s]+)*$', model_class):
            logger.error(f"Định dạng model_class không hợp lệ: {model_class}")
            raise HTTPException(
                status_code=400,
                detail="model_class phải có định dạng 'Class1, Class2, Class3' (các class phân tách bằng dấu phẩy)."
            )

        # Tách chuỗi thành danh sách các class
        classes = [cls.strip() for cls in model_class.split(',')]
        classes = [cls for cls in classes if cls]  # Loại bỏ phần tử rỗng

        # Kiểm tra số lượng class
        if len(classes) < 1:
            logger.error("Danh sách class không được rỗng")
            raise HTTPException(
                status_code=400,
                detail="Danh sách class không được rỗng."
            )

        # Kiểm tra độ dài từng class
        for cls in classes:
            if len(cls) < 1 or len(cls) > 255:
                logger.error(f"Class không hợp lệ: {cls}")
                raise HTTPException(
                    status_code=400,
                    detail="Mỗi class phải có độ dài từ 1 đến 255 ký tự."
                )

        # Kiểm tra định dạng file
        allowed_extensions = {'.h5', '.pth', '.pt', '.onnx'}
        file_extension = os.path.splitext(file.filename)[1].lower()
        if file_extension not in allowed_extensions:
            logger.error(f"Định dạng file không hợp lệ: {file_extension}")
            raise HTTPException(
                status_code=400,
                detail="File mô hình phải có định dạng .h5, .pth, .pt, hoặc .onnx"
            )

        # Kiểm tra kích thước file (500MB = 500 * 1024 * 1024 bytes)
        file_size = 0
        file_content = b""
        async for chunk in file:
            file_content += chunk
            file_size += len(chunk)
            if file_size > 500 * 1024 * 1024:
                logger.error(f"File vượt quá kích thước tối đa (500MB): {file_size} bytes")
                raise HTTPException(
                    status_code=400,
                    detail="File vượt quá kích thước tối đa 500MB"
                )

        # Đảm bảo thư mục /app/models/ tồn tại
        model_dir = "/app/models/"
        os.makedirs(model_dir, exist_ok=True)

        # Tạo tên file duy nhất với timestamp
        timestamp = int(time.time())
        new_filename = f"{timestamp}_{file.filename}"
        model_path = os.path.join(model_dir, new_filename)

        # Lưu file vào thư mục /app/models/
        with open(model_path, "wb") as f:
            f.write(file_content)

        logger.info(
            f"Mô hình đã được upload và lưu tại: {model_path}, kích thước: {file_size} bytes, classes: {model_class}")

        return {
            "status": "success",
            "model_path": model_path,
            "model_class": model_class
        }
    except Exception as e:
        logger.error(f"Lỗi khi upload mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Lỗi khi upload mô hình: {str(e)}")