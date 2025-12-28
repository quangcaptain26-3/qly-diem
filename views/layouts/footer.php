<?php if (Auth::check()): ?>
            </div> <!-- End .main-content -->
        </div> <!-- End #content -->
    </div> <!-- End #wrapper -->
<?php endif; ?>

    <!-- jQuery (for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Moment.js (for date formatting) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent document click from immediately closing it
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                    // If click is not on sidebar and not on the toggle button
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }

        // Toast notification function
        function showToast(type, message) {
            const toastId = 'toast' + type.charAt(0).toUpperCase() + type.slice(1);
            const toastBodyId = toastId + 'Body';
            const toastElement = document.getElementById(toastId);
            const toastBody = document.getElementById(toastBodyId);
            
            if (toastElement && toastBody) {
                toastBody.innerHTML = message;
                const toast = new bootstrap.Toast(toastElement, {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
            }
        }
        
        // Show toast from session messages
        $(document).ready(function() {
            // Check for success message
            <?php if (isset($_SESSION['success'])): ?>
                showToast('success', <?= json_encode($_SESSION['success'], JSON_UNESCAPED_UNICODE) ?>);
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            // Check for error message
            <?php if (isset($_SESSION['error'])): ?>
                showToast('error', <?= json_encode($_SESSION['error'], JSON_UNESCAPED_UNICODE) ?>);
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            // Check for warning message
            <?php if (isset($_SESSION['warning'])): ?>
                showToast('warning', <?= json_encode($_SESSION['warning'], JSON_UNESCAPED_UNICODE) ?>);
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>
            
            // Check for info message
            <?php if (isset($_SESSION['info'])): ?>
                showToast('info', <?= json_encode($_SESSION['info'], JSON_UNESCAPED_UNICODE) ?>);
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>
            
            // Initialize DataTables on all tables with class 'datatable'
            $('.datatable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'asc']]
            });
        });
        
        // Format dates with moment.js
        moment.locale('vi');
        
        // Global function to show toast from anywhere
        window.showToast = showToast;
    </script>
</body>
</html>

