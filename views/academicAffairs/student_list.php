<?php
$title = 'Tra cứu Sinh viên - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div id="academic-student-app">
    <h2 class="mb-4"><i class="bi bi-search"></i> Tra cứu Sinh viên (<?= htmlspecialchars($faculty['name'] ?? 'Khoa của tôi') ?>)</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="search-form" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" id="search-input" placeholder="Nhập Tên hoặc Mã sinh viên...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
             <h5 class="mb-0"><i class="bi bi-list"></i> Kết quả tìm kiếm</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="students-table">
                    <thead class="table-light">
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th class="text-center">GPA Tích lũy</th>
                            <th class="text-end">Thao tác</th>
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

<script>
    const API_LIST_URL = '<?= url("/academic/students/api") ?>';
    let dataTable;

    document.addEventListener('DOMContentLoaded', function() {
        dataTable = $('#students-table').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json' },
            columns: [
                { data: 'student_code', className: 'fw-bold' },
                { data: 'full_name', className: 'fw-bold text-primary' },
                { data: 'email' },
                { 
                    data: 'gpa', 
                    className: 'text-center fw-bold',
                    render: function(data) { return data !== null ? parseFloat(data).toFixed(2) : '-'; }
                },
                {
                    data: null,
                    className: 'text-end',
                    orderable: false,
                    render: function(data) {
                        return `<a href="<?= url('/academic/students/detail?id=') ?>${data.id}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Xem hồ sơ</a>`;
                    }
                }
            ],
            dom: 'rtip',
            pageLength: 25
        });

        loadData();

        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadData();
        });
    });

    async function loadData() {
        const search = document.getElementById('search-input').value;
        $('#students-table tbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>');
        
        try {
            const response = await fetch(API_LIST_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ search: search })
            });
            const json = await response.json();
            if (json.success) {
                dataTable.clear().rows.add(json.data).draw();
            }
        } catch (e) {
            showToast('error', 'Lỗi tải dữ liệu');
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
