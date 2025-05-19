from PIL import Image
import io
import numpy as np

def preprocess_image(image):
    """Xử lý ảnh trước khi dự đoán"""
    img = Image.open(io.BytesIO(image)).convert('RGB')
    img = img.resize((224, 224))
    img_array = np.array(img) / 255.0
    img_array = np.expand_dims(img_array, axis=0)
    return img_array