# retrieval.py
import logging
import google.generativeai as genai
from googlesearch import search
import requests
from bs4 import BeautifulSoup
from sentence_transformers import SentenceTransformer, util
import numpy as np
from openai import OpenAI

# Cấu hình logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Khởi tạo mô hình nhúng văn bản
embedder = SentenceTransformer('paraphrase-multilingual-mpnet-base-v2')
embedder.save_pretrained('/app/ultis')

# Hàm tìm kiếm thông tin từ Google và scrape nội dung
def search_google(query, num_results=10):
    try:
        urls = list(search(query, num_results=num_results))
        logger.info(f"Tìm thấy {len(urls)} URL cho truy vấn: {query}")
        contents = []
        for url in urls:
            try:
                response = requests.get(url, timeout=5)
                response.raise_for_status()
                soup = BeautifulSoup(response.text, 'html.parser')
                paragraphs = soup.find_all('p')
                page_content = " ".join([p.get_text() for p in paragraphs])
                if page_content.strip():
                    contents.append(f"Nội dung từ {url}: {page_content[:10000]}")
                    logger.info(f"Đã scrape nội dung từ: {url}")
                else:
                    logger.warning(f"Không tìm thấy nội dung hữu ích từ: {url}")
            except Exception as e:
                logger.error(f"Lỗi khi scrape {url}: {e}")
        return contents if contents else ["Không tìm thấy nội dung từ các trang web."]
    except Exception as e:
        logger.error(f"Lỗi khi tìm kiếm Google: {e}")
        return ["Không tìm thấy thông tin từ Google."]

# Hàm tổng hợp nội dung bằng LLM (Gemini hoặc OpenAI)
def summarize_with_llm(predicted_class, contents, llm_provider, llm_api_key):
    if not contents or contents == ["Không tìm thấy nội dung từ các trang web."]:
        content_text = "Không đủ thông tin để tổng hợp."
    else:
        content_text = "\n".join(contents)

    prompt = (
        f"Tôi đã nhận diện được dòng gốm '{predicted_class}'. "
        f"Dựa trên thông tin từ các trang web sau:\n"
        f"{content_text}\n"
        f"Hãy cung cấp mô tả chi tiết về dòng gốm này bằng tiếng Việt, bao gồm:Dòng gốm sau khi quan sát cụ thể chi tiết hoa văn, mô tả, giá bán, và lịch sử hình thành. "
        "Chỉ trả về thông tin liên quan và chính xác nhất có thể"
    )

    try:
        if llm_provider.lower() == "gemini":
            if not llm_api_key:
                raise ValueError("API key cho Gemini không được cung cấp.")
            # Cấu hình API Gemini với key từ người dùng
            genai.configure(api_key=llm_api_key)
            gemini_model = genai.GenerativeModel('gemini-2.0-flash')
            response = gemini_model.generate_content(prompt)
            if not response.text:
                raise ValueError("Gemini không trả về nội dung hợp lệ.")
            logger.info(f"Đã nhận phản hồi từ Gemini: {response.text[:100]}...")
            return response.text
        elif llm_provider.lower() == "openai":
            if not llm_api_key:
                raise ValueError("API key cho OpenAI không được cung cấp.")
            # Gọi API OpenAI với key từ người dùng
            client = OpenAI(api_key=llm_api_key)
            response = client.chat.completions.create(
                model="gpt-4o-mini",
                messages=[
                    {"role": "system", "content": "Bạn là trợ lý cung cấp thông tin chi tiết về gốm sứ."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=1000
            )
            logger.info(f"Đã nhận phản hồi từ OpenAI: {response.choices[0].message.content[:100]}...")
            return response.choices[0].message.content
        else:
            raise ValueError("Nhà cung cấp LLM không hợp lệ. Chọn 'gemini' hoặc 'openai'.")
    except Exception as e:
        logger.error(f"Lỗi khi gọi {llm_provider}: {e}")
        return f"Lỗi: Không thể lấy thông tin từ {llm_provider}. Vui lòng kiểm tra API key hoặc kết nối."

# Hàm lọc nội dung liên quan dựa trên vector
def filter_relevant_content(summary, query):
    if not summary or summary.startswith("Lỗi:") or summary == "Không đủ thông tin để tổng hợp.":
        return summary
    sentences = summary.split(". ")
    query_embedding = embedder.encode(query, convert_to_tensor=True)
    sentence_embeddings = embedder.encode(sentences, convert_to_tensor=True)
    cos_scores = util.pytorch_cos_sim(query_embedding, sentence_embeddings)[0]
    top_k = min(3, len(sentences))
    top_indices = np.argsort(cos_scores.cpu().numpy())[::-1][:top_k]
    filtered_sentences = [sentences[idx] for idx in top_indices]
    return ". ".join(filtered_sentences) + "."

# Hàm lấy thông tin chi tiết từ LLM dựa trên dòng gốm nhận diện
def get_ceramic_info(predicted_class, llm_provider, llm_api_key):
    search_query = f"gốm: {predicted_class}"
    logger.info(f"Truy vấn Google: {search_query}")
    contents = search_google(search_query)
    summary = summarize_with_llm(predicted_class, contents, llm_provider, llm_api_key)
    return summary