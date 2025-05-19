#
##FROM nvidia/cuda:11.8.0-base-ubuntu22.04 as builder
#FROM nvidia/cuda:11.8.0-base-ubuntu22.04 AS BUILDER
## Thiết lập múi giờ không tương tác và các biến môi trường
#ENV DEBIAN_FRONTEND=noninteractive \
#    TZ=Etc/UTC \
#    PYTHONUNBUFFERED=1 \
#    PIP_NO_CACHE_DIR=1
#
## Sử dụng mirror nhanh cho apt (tùy chọn)
#RUN sed -i 's|http://archive.ubuntu.com|http://mirrors.aliyun.com|' /etc/apt/sources.list
#COPY . .
## Cài đặt Python 3.10 và các dependencies tối thiểu
#RUN apt-get update && \
#    apt-get install -y --no-install-recommends software-properties-common && \
#    add-apt-repository ppa:deadsnakes/ppa -y && \
#    apt-get install -y --no-install-recommends python3.10 python3.10-dev python3.10-distutils curl && \
#    apt-get clean && \
#    rm -rf /var/lib/apt/lists/*
#    #&& \
##    update-alternatives --install /usr/bin/python3 python3 /usr/bin/python3.10 1 && \
##    update-alternatives --install /usr/bin/python python /usr/bin/python3.10 1
#COPY . .
## Cài đặt pip và các packages từ requirements.txt
#RUN curl -sS https://bootstrap.pypa.io/get-pip.py | python3.10 \
#    && pip3 install --no-cache-dir --upgrade pip setuptools wheel
#COPY . .
## Tạo và chuyển vào thư mục làm việc
#WORKDIR /app
#COPY requirements.txt .
#RUN pip3 install --no-cache-dir -r requirements.txt -i https://pypi.tuna.tsinghua.edu.cn/simple
#COPY . .
#
#EXPOSE 55001
#
#CMD ["python3.10", "-m", "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "55001"]
# Sử dụng Python base image
#FROM python:3.10-slim
#
## Đặt môi trường không tương tác cho apt-get
#ENV DEBIAN_FRONTEND=noninteractive
#
## Cài đặt các gói cần thiết và dọn dẹp sau khi cài đặt
#RUN apt-get update && \
#    apt-get install -y --no-install-recommends \
#    python3 python3-pip && \
#    apt-get clean && \
#    rm -rf /var/lib/apt/lists/*
#
## Thiết lập thư mục làm việc
#WORKDIR /app
#
## Copy file requirements.txt vào container
#COPY ./requirements.txt /app/requirements.txt
#
## Cài đặt các thư viện Python và xóa cache
#RUN pip3 install --no-cache-dir -r /app/requirements.txt && \
#    pip cache purge
#
## Xóa các tệp không cần thiết (nếu có)
#RUN find /app -type d -name "__pycache__" -exec rm -r {} + && \
#    find /app -name "*.pyc" -delete
#
## Thiết lập lệnh mặc định
#CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "55001"]
# Sử dụng Python base image
#FROM tensorflow/tensorflow:2.18.0rc2
FROM python:3.10-slim

# Đặt môi trường không tương tác cho apt-get
ENV DEBIAN_FRONTEND=noninteractive


# Thiết lập thư mục làm việc
WORKDIR /app

# Copy file requirements.txt vào container
COPY ./requirements.txt /app/requirements.txt
RUN apt-get update && \
    apt-get install -y --no-install-recommends git && \
    pip install --no-cache-dir --only-binary=:all: -r /app/requirements.txt && \
    pip install --no-cache-dir torch --index-url https://download.pytorch.org/whl/cpu && \
    pip install --no-cache-dir sentence-transformers && \
    pip install --no-cache-dir --no-deps tensorflow-cpu==2.9.0 && \
    apt-get clean && apt-get remove -y git && apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/* /root/.cache/pip/* /tmp/* && \
    find / -type d -name "__pycache__" -exec rm -rf {} + && \
    find / -name "*.pyc" -delete && \
    find / -name "*.whl" -delete && \
    find / -name "*.tar.gz" -delete && \
    find /usr/local/lib/python3.10/site-packages -name "README*" -delete && \
    find /usr/local/lib/python3.10/site-packages -name "tests" -type d -exec rm -rf {} + && \
    find /usr/local/lib/python3.10/site-packages -name "*.md" -delete && \
    find /usr/local/lib/python3.10/site-packages -name "*.txt" -not -name "requirements.txt" -delete

COPY . /app

# Thiết lập lệnh mặc định
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "55001"]
