<?php
$title = 'Quản lý Giảng viên - Root';
require __DIR__ . '/../layouts/header.php';
?>

<div id="lecturer-management-app">
    <h2 class="mb-4"><i class="bi bi-person-workspace"></i> Quản lý Giảng viên</h2>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Bộ lọc</h5>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search-input" placeholder="Tên hoặc Mã GV...">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Khoa</label>
                    <select class="form-select" id="faculty-select">
                        <option value="">-- Tất cả Khoa --</option>
                        <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-list-stars"></i> Hiệu suất Giảng dạy</h5>
            <span class="badge bg-info" id="total-count">0 giảng viên</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="lecturers-table" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Giảng viên</th>
                            <th>Khoa / Bộ môn</th>
                            <th class="text-center">Số lớp</th>
                            <th class="text-center">Tổng SV</th>
                            <th class="text-center">Điểm TB đã cho</th>
                            <th class="text-center">Phong cách chấm</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chi tiết Lớp học -->
<div class="modal fade" id="lecturerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin Giảng dạy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tính năng chi tiết lớp dạy đang được cập nhật...</p>
            </div>
        </div>
    </div>
</div>

<script>
    const API_LIST_URL = '<?= url("/root/lecturers/api") ?>';
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#lecturers-table').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json' },
            columns: [
                { 
                    data: null,
                    render: function(data) {
                        return `<div>
                                    <div class="fw-bold">${data.full_name}</div>
                                    <small class="text-muted">${data.username}</small>
                                </div>`;
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        return `<div>
                                    <div class="small">${data.faculty_name || 'N/A'}</div>
                                    <div class="text-muted smaller">${data.department_name || 'N/A'}</div>
                                </div>`;
                    }
                },
                { data: 'total_classes', className: 'text-center' },
                { data: 'total_students', className: 'text-center' },
                { 
                    data: 'avg_grade_given', 
                    className: 'text-center fw-bold text-primary',
                    render: function(data) { return data || '-'; }
                },
                { 
                    data: 'grading_style',
                    className: 'text-center',
                    render: function(data) {
                        return `<span class="badge bg-${data.color}">${data.label}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    orderable: false,
                    render: function(data) {
                        return `<button class="btn btn-sm btn-outline-primary" onclick="showClasses(${data.id})">
                                    <i class="bi bi-eye"></i> Lớp dạy
                                </button>`;
                    }
                }
            ],
            dom: 'rtip',
            pageLength: 20
        });

        loadData();

        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadData();
        });
    });

    async function loadData() {
        $('#lecturers-table tbody').html('<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>');
        
        const params = {
            search: document.getElementById('search-input').value,
            faculty_id: document.getElementById('faculty-select').value
        };

        try {
            const response = await fetch(API_LIST_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(params)
            });
            const json = await response.json();
            if (json.success) {
                dataTable.clear().rows.add(json.data).draw();
                document.getElementById('total-count').textContent = json.data.length + ' giảng viên';
            }
        } catch (e) {
            showToast('error', 'Lỗi tải dữ liệu giảng viên');
        }
    }

    function showClasses(id) {
        showToast('info', 'Tính năng đang được phát triển...');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
