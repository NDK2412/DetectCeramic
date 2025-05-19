<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Metadata</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<style>/* Metadata Manager - Unique namespace */
.metadata-manager {
  font-family: 'Roboto', sans-serif;
  color: #333;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.metadata-manager h2 {
  color: #2c3e50;
  margin-bottom: 25px;
  font-weight: 500;
  border-bottom: 2px solid #3498db;
  padding-bottom: 10px;
}

/* Table styling */
.metadata-manager-table {
  width: 100%;
  border-collapse: collapse;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 30px;
  background-color: #fff;
}

.metadata-manager-table thead {
  background-color: #3498db;
  color: #fff;
}

.metadata-manager-table th {
  padding: 15px 12px;
  text-align: left;
  font-weight: 500;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.metadata-manager-table td {
  padding: 12px;
  border-bottom: 1px solid #e9ecef;
  font-size: 14px;
  vertical-align: middle;
}

.metadata-manager-table tr:hover {
  background-color: #f8f9fa;
}

.metadata-manager-table tr:last-child td {
  border-bottom: none;
}

/* Image styling in table */
.metadata-manager-table .favicon-img {
  border-radius: 4px;
  border: 1px solid #eaeaea;
  padding: 2px;
  background-color: #fff;
}

/* Action buttons */
.metadata-manager .action-btn {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 4px;
  color: #fff;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.3s ease;
  text-decoration: none;
  margin-right: 5px;
}

.metadata-manager .edit-metadata-btn {
  background-color: #3498db;
}

.metadata-manager .edit-metadata-btn:hover {
  background-color: #2980b9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.metadata-manager .action-btn i {
  margin-right: 5px;
  font-size: 12px;
}

/* Modal styling */
.metadata-manager-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  justify-content: center;
  align-items: center;
}

.metadata-manager-modal-content {
  background-color: #fff;
  border-radius: 8px;
  width: 100%;
  max-width: 600px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  position: relative;
}

.metadata-manager-modal h3 {
  margin-top: 0;
  color: #2c3e50;
  border-bottom: 2px solid #3498db;
  padding-bottom: 10px;
  margin-bottom: 20px;
}

.metadata-manager-modal .form-group {
  margin-bottom: 15px;
}

.metadata-manager-modal label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #555;
  font-size: 14px;
}

.metadata-manager-modal input[type="text"],
.metadata-manager-modal textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: border 0.3s ease;
}

.metadata-manager-modal input[type="text"]:focus,
.metadata-manager-modal textarea:focus {
  border-color: #3498db;
  outline: none;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.metadata-manager-modal textarea {
  min-height: 100px;
  resize: vertical;
}

.metadata-manager-modal input[type="file"] {
  padding: 8px 0;
}

/* Button styling */
.metadata-manager-modal .btn-group {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  gap: 10px;
}

.metadata-manager-modal .btn {
  padding: 8px 15px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s ease;
  border: none;
}

.metadata-manager-modal .btn-save {
  background-color: #2ecc71;
  color: white;
}

.metadata-manager-modal .btn-save:hover {
  background-color: #27ae60;
}

.metadata-manager-modal .btn-cancel {
  background-color: #e74c3c;
  color: white;
}

.metadata-manager-modal .btn-cancel:hover {
  background-color: #c0392b;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .metadata-manager-table th,
  .metadata-manager-table td {
    padding: 10px 8px;
    font-size: 13px;
  }
  
  .metadata-manager-modal-content {
    width: 90%;
    padding: 15px;
  }
}

/* Current file preview */
.metadata-manager-current-file {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.metadata-manager-current-file img {
  margin-right: 10px;
  border: 1px solid #ddd;
  padding: 2px;
  border-radius: 4px;
}

.metadata-manager-current-file span {
  font-size: 13px;
  color: #666;
}</style>
</head>
<body>
    <div class="metadata-manager">
        <h2>Quản lý Metadata</h2>
        
        <table class="metadata-manager-table" id="metadata-table">
            <thead>
                <tr>
                    <th>Trang</th>
                    <th>Tiêu đề</th>
                    <th>Mô tả</th>
                    <th>Key Word</th>
                    <th>Icon</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($metadata as $item)
                <tr data-id="{{ $item->id }}">
                    <td>{{ $item->page }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->keywords }}</td>
                    <td>
                        @if ($item->favicon)
                            <img src="{{ asset('storage/' . $item->favicon) }}" alt="Favicon" width="30" class="favicon-img">
                        @endif
                    </td>
                    <td>
                        <a class="action-btn edit-metadata-btn" data-id="{{ $item->id }}"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal for editing Metadata -->
        <div id="editModal" class="metadata-manager-modal">
            <div class="metadata-manager-modal-content">
                <h3>Chỉnh sửa Metadata</h3>
                <form id="editMetadataForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="metadataId">
                    
                    <div class="form-group">
                        <label for="page">Trang:</label>
                        <input type="text" name="page" id="page" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Tiêu đề:</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Mô tả:</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="keywords">Key Word:</label>
                        <input type="text" name="keywords" id="keywords">
                    </div>
                    
                    <div class="form-group">
                        <label for="favicon">Icon:</label>
                        <div id="currentFavicon" class="metadata-manager-current-file">
                            <!-- Current favicon will be displayed here -->
                        </div>
                        <input type="file" name="favicon" id="favicon" accept="image/*">
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" id="cancelEdit" class="btn btn-cancel">Hủy</button>
                        <button type="submit" class="btn btn-save">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Xử lý nút chỉnh sửa
            $(document).on('click', '.edit-metadata-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');

                // Lấy dữ liệu từ hàng hiện tại
                const page = row.find('td:nth-child(1)').text();
                const title = row.find('td:nth-child(2)').text();
                const description = row.find('td:nth-child(3)').text();
                const keywords = row.find('td:nth-child(4)').text();
                const favicon = row.find('td:nth-child(5) img').attr('src');

                // Điền dữ liệu vào form
                $('#metadataId').val(id);
                $('#page').val(page);
                $('#title').val(title);
                $('#description').val(description);
                $('#keywords').val(keywords);
                
                // Hiển thị favicon hiện tại nếu có
                if (favicon) {
                    $('#currentFavicon').html(`<img src="${favicon}" alt="Current Favicon" width="30"> <span>Icon hiện tại</span>`);
                } else {
                    $('#currentFavicon').html('<span>Chưa có icon</span>');
                }

                // Hiển thị modal
                $('.metadata-manager-modal').css('display', 'flex');
            });

            // Hủy chỉnh sửa
            $('#cancelEdit').click(function() {
                $('.metadata-manager-modal').css('display', 'none');
            });

            // Đóng modal khi click bên ngoài
            $(window).click(function(e) {
                if ($(e.target).hasClass('metadata-manager-modal')) {
                    $('.metadata-manager-modal').css('display', 'none');
                }
            });

            // Gửi form chỉnh sửa
            $('#editMetadataForm').submit(function(e) {
                e.preventDefault();

                const id = $('#metadataId').val();
                const formData = new FormData(this);

                $.ajax({
                    url: '/metadata/' + id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert('Cập nhật thành công!');
                        location.reload();
                    },
                    error: function(error) {
                        alert('Có lỗi xảy ra: ' + error.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>
</html>