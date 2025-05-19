<body>
    
<div class="info-content">
    @if (!empty($llm_response))
        <div class="info-sections">
            @foreach (explode("\n", $llm_response) as $paragraph)
                @if (trim($paragraph) !== '')
                    @php
                        // Loại bỏ các thẻ HTML trong $paragraph để xử lý nội dung
                        $cleanedParagraph = strip_tags($paragraph);
                        // Kiểm tra nếu đoạn bắt đầu bằng các tiêu đề chính
                        if (preg_match('/^##\s*(.*?)$/i', $cleanedParagraph, $matches)) {
                            echo '<h2 class="info-title">' . htmlspecialchars($matches[1]) . '</h2>';
                        } elseif (preg_match('/^\*\*\s*(.*?)\s*\*\*$/i', $cleanedParagraph, $matches)) {
                            echo '<h3 class="info-section-title">' . htmlspecialchars($matches[1]) . '</h3>';
                        } elseif (preg_match('/^\*\s*\*\*(.*?)\*\*:\s*$/i', $cleanedParagraph, $matches)) {
                            echo '<h4 class="info-subsection-title">' . htmlspecialchars($matches[1]) . '</h4>';
                        } else {
                            // Nếu không phải tiêu đề, hiển thị đoạn văn
                            $formattedParagraph = preg_replace('/^(.*?):/', '<strong>$1:</strong>', $paragraph);
                            echo '<p class="info-paragraph">' . $formattedParagraph . '</p>';
                        }
                    @endphp
                @endif
            @endforeach
        </div>
    @else
        <p class="info-paragraph no-data">Không có thông tin chi tiết.</p>
    @endif
</div>
</body>
<style>/* Info Content Container */
.info-content {
    padding: 15px;
    background: var(--light-gray);
    border-radius: 10px;
    transition: all 0.3s ease;
}

/* Main Title (e.g., Gốm Cây Mai) */
.info-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 25px;
    background: var(--gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    position: relative;
    padding-bottom: 10px;
}

.info-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: var(--gradient);
    border-radius: 2px;
}

/* Section Title (e.g., Mô tả, Lịch sử, Giá bán) */
.info-section-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--primary-color);
    margin: 20px 0 15px;
    position: relative;
    padding-left: 30px;
}

.info-section-title::before {
    content: '\f05a'; /* Icon thông tin từ Font Awesome */
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: var(--secondary-color);
    position: absolute;
    left: 0;
    top: 2px;
    font-size: 1.2rem;
}

/* Subsection Title (e.g., Gốm gia dụng, Gốm trang trí) */
.info-subsection-title {
    font-size: 1.2rem;
    font-weight: 500;
    color: var(--dark-gray);
    margin: 15px 0 10px;
    padding-left: 30px;
    position: relative;
}

.info-subsection-title::before {
    content: '\f00c'; /* Icon check từ Font Awesome */
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: var(--primary-color);
    position: absolute;
    left: 0;
    top: 2px;
    font-size: 1rem;
}

/* Paragraph Styling */
.info-paragraph {
    font-size: 1rem;
    color: var(--dark-gray);
    line-height: 1.7;
    margin-bottom: 15px;
    padding-left: 30px;
    position: relative;
    transition: background 0.3s ease;
}

.info-paragraph:hover {
    background: rgba(118, 218, 236, 0.1); /* Hiệu ứng hover nhẹ */
}

/* Thêm icon trước mỗi đoạn văn */
.info-paragraph::before {
    content: '\f101'; /* Icon mũi tên từ Font Awesome */
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: var(--primary-color);
    position: absolute;
    left: 0;
    top: 2px;
    font-size: 1rem;
}

/* Định dạng phần tử in đậm */
.info-paragraph strong {
    color: var(--primary-color);
    font-weight: 600;
}

/* No Data Message */
.info-paragraph.no-data {
    text-align: center;
    font-style: italic;
    color: #888;
    padding-left: 0;
}

.info-paragraph.no-data::before {
    content: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .info-title {
        font-size: 1.5rem;
    }

    .info-section-title {
        font-size: 1.2rem;
        padding-left: 25px;
    }

    .info-subsection-title {
        font-size: 1.1rem;
        padding-left: 25px;
    }

    .info-paragraph {
        font-size: 0.95rem;
        padding-left: 25px;
    }

    .info-section-title::before,
    .info-subsection-title::before,
    .info-paragraph::before {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .info-title {
        font-size: 1.3rem;
    }

    .info-section-title {
        font-size: 1.1rem;
        padding-left: 20px;
    }

    .info-subsection-title {
        font-size: 1rem;
        padding-left: 20px;
    }

    .info-paragraph {
        font-size: 0.9rem;
        padding-left: 20px;
    }

    .info-section-title::before,
    .info-subsection-title::before,
    .info-paragraph::before {
        font-size: 0.9rem;
    }
}</style>