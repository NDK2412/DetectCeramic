
import psutil
from fastapi import HTTPException
import pynvml

class SystemController:
    @staticmethod
    async def get_system_stats():
        try:
            # Khởi tạo pynvml để lấy thông tin GPU
            pynvml.nvmlInit()
            device_count = pynvml.nvmlDeviceGetCount()

            gpu_usage = 0
            gpu_total = 0
            gpu_used = 0
            if device_count > 0:
                handle = pynvml.nvmlDeviceGetHandleByIndex(0)  # Lấy GPU đầu tiên
                gpu_usage = pynvml.nvmlDeviceGetUtilizationRates(handle).gpu
                mem_info = pynvml.nvmlDeviceGetMemoryInfo(handle)
                gpu_total = round(mem_info.total / (1024 ** 2), 2)  # MB
                gpu_used = round(mem_info.used / (1024 ** 2), 2)    # MB

            # Lấy thông tin CPU
            cpu_usage = psutil.cpu_percent(interval=1)

            # Lấy thông tin RAM
            memory = psutil.virtual_memory()
            ram_total = round(memory.total / (1024 ** 2), 2)  # MB
            ram_used = round(memory.used / (1024 ** 2), 2)    # MB
            ram_percent = memory.percent

            # Đóng pynvml
            pynvml.nvmlShutdown()

            return {
                "cpu_usage_percent": cpu_usage,
                "ram_total_mb": ram_total,
                "ram_used_mb": ram_used,
                "ram_usage_percent": ram_percent,
                "gpu_usage_percent": gpu_usage,
                "gpu_total_mb": gpu_total,
                "gpu_used_mb": gpu_used
            }
        except pynvml.NVMLError as e:
            return {
                "cpu_usage_percent": psutil.cpu_percent(interval=1),
                "ram_total_mb": round(psutil.virtual_memory().total / (1024 ** 2), 2),
                "ram_used_mb": round(psutil.virtual_memory().used / (1024 ** 2), 2),
                "ram_usage_percent": psutil.virtual_memory().percent,
                "gpu_usage_percent": 0,
                "gpu_total_mb": 0,
                "gpu_used_mb": 0
            }
        except Exception as e:
            raise HTTPException(status_code=500, detail=f"Lỗi khi lấy thông tin hệ thống: {str(e)}")