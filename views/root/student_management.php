<?php
$title = 'Quản lý Học sinh - Root';
require __DIR__ . '/../layouts/header.php';
?>

<div id="student-management-app">
    <h2 class="mb-4"><i class="bi bi-people-fill"></i> Quản lý Học sinh</h2>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Bộ lọc tìm kiếm</h5>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search-input" placeholder="Tên hoặc Mã SV...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Khoa</label>
                    <select class="form-select" id="faculty-select">
                        <option value="">-- Tất cả Khoa --</option>
                        <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Học lực</label>
                    <select class="form-select" id="performance-select">
                        <option value="">-- Tất cả --</option>
                        <option value="excellent">Xuất sắc (>= 3.6)</option>
                        <option value="good">Giỏi (3.2 - 3.59)</option>
                        <option value="average">Khá (2.5 - 3.19)</option>
                        <option value="weak">Trung bình (2.0 - 2.49)</option>
                        <option value="poor">Kém (< 2.0)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">GPA (Thang 4)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="min-gpa" placeholder="Từ" step="0.1" min="0" max="4">
                        <span class="input-group-text">-</span>
                        <input type="number" class="form-control" id="max-gpa" placeholder="Đến" step="0.1" min="0" max="4">
                    </div>
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-secondary me-2" id="reset-btn">
                        <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-list-ul"></i> Danh sách Học sinh</h5>
            <span class="badge bg-secondary" id="total-count">0 học sinh</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped" id="students-table" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ và Tên</th>
                            <th>Khoa</th>
                            <th>Email</th>
                            <th class="text-center">GPA Tích lũy</th>
                            <th class="text-center">Xếp loại</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const API_LIST_URL = '<?= url("/root/students/api") ?>';
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        dataTable = $('#students-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
            },
            columns: [
                { data: 'student_code', className: 'fw-bold' },
                { data: 'full_name', className: 'fw-bold text-primary' },
                { data: 'faculty_name' },
                { data: 'email' },
                { 
                    data: 'gpa', 
                    className: 'text-center fw-bold',
                    render: function(data) {
                        return data !== null ? parseFloat(data).toFixed(2) : '<span class="text-muted">-</span>';
                    }
                },
                { 
                    data: null,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<span class="badge bg-${row.performance_color}">${row.performance}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-info" title="Xem chi tiết" onclick="viewDetails(${row.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                        `;
                    }
                }
            ],
            dom: '<"d-none"f>rt<"d-flex justify-content-between mt-3"ip>', // Hide default search
            pageLength: 25
        });

        // Load initial data
        loadData();

        // Handle Filter Submit
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadData();
        });

        // Handle Reset
        document.getElementById('reset-btn').addEventListener('click', function() {
            document.getElementById('filter-form').reset();
            loadData();
        });
    });

    async function loadData() {
        // Show loading state
        $('#students-table tbody').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Đang tải dữ liệu...</p></td></tr>');

        const params = {
            search: document.getElementById('search-input').value,
            faculty_id: document.getElementById('faculty-select').value,
            performance: document.getElementById('performance-select').value,
            min_gpa: document.getElementById('min-gpa').value,
            max_gpa: document.getElementById('max-gpa').value
        };

        try {
            const response = await fetch(API_LIST_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(params)
            });
            
            const json = await response.json();
            
            if (json.success) {
                dataTable.clear();
                dataTable.rows.add(json.data);
                dataTable.draw();
                document.getElementById('total-count').textContent = json.data.length + ' học sinh';
            } else {
                showToast('error', 'Lỗi tải dữ liệu: ' + json.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Không thể kết nối đến máy chủ.');
        }
    }
    
    function viewDetails(studentId) {
        window.location.href = '<?= url("/root/students/detail?id=") ?>' + studentId;
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
