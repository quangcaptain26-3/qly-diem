<?php
$title = 'Cảnh báo Học tập - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div id="warning-app">
    <h2 class="mb-4 text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Cảnh báo Học tập</h2>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Bộ lọc Mức độ</h5>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Ngưỡng GPA (Thấp hơn)</label>
                    <select class="form-select" id="threshold-select">
                        <option value="2.0">GPA < 2.0 (Cảnh báo chung)</option>
                        <option value="1.5">GPA < 1.5 (Cảnh báo nghiêm trọng)</option>
                        <option value="1.0">GPA < 1.0 (Nguy cơ buộc thôi học)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-search"></i> Lọc danh sách
                    </button>
                </div>
                <div class="col-md-3">
                     <button type="button" class="btn btn-success w-100" onclick="exportData()">
                        <i class="bi bi-file-earmark-excel"></i> Xuất danh sách
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-danger"><i class="bi bi-list-ul"></i> Danh sách Sinh viên thuộc diện Cảnh báo</h5>
            <span class="badge bg-danger fs-6" id="total-count">0 sinh viên</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="warnings-table" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ tên</th>
                            <th class="text-center">GPA Tích lũy</th>
                            <th class="text-center">Tín chỉ</th>
                            <th class="text-center">Mức độ rủi ro</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const API_LIST_URL = '<?= url("/academic/warning/api") ?>';
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#warnings-table').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json' },
            columns: [
                { data: 'student_code', className: 'fw-bold' },
                { data: 'full_name' },
                { 
                    data: 'gpa', 
                    className: 'text-center fw-bold text-danger',
                    render: function(data) { return parseFloat(data).toFixed(2); }
                },
                { data: 'total_credits', className: 'text-center' },
                { 
                    data: 'risk_level',
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
                        return `<a href="<?= url('/academic/students/detail?id=') ?>${data.id}" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-file-medical"></i> Xem hồ sơ
                                </a>`;
                    }
                }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Xuất Excel',
                    className: 'd-none', // Hidden, triggered by custom button
                    title: 'Danh_Sach_Canh_Bao_Hoc_Tap'
                }
            ],
            pageLength: 25
        });

        loadData();

        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadData();
        });
    });

    async function loadData() {
        const threshold = document.getElementById('threshold-select').value;
        $('#warnings-table tbody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-danger"></div></td></tr>');
        
        try {
            const response = await fetch(API_LIST_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ threshold: threshold })
            });
            const json = await response.json();
            
            if (json.success) {
                dataTable.clear().rows.add(json.data).draw();
                document.getElementById('total-count').textContent = json.data.length + ' sinh viên';
            }
        } catch (e) {
            showToast('error', 'Lỗi tải dữ liệu cảnh báo');
        }
    }

    function exportData() {
        dataTable.button('.buttons-excel').trigger();
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>