# 🎓 Hệ thống Quản lý Điểm Đại học

Hệ thống quản lý điểm số toàn diện cho trường đại học với hỗ trợ đa khoa, phân quyền chi tiết, và các tính năng quản lý điểm số, học bổng, cảnh báo học tập.

## 📋 Mục lục

- [Giới thiệu](#giới-thiệu)
- [Tính năng](#tính-năng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt](#cài-đặt)
- [Cấu hình](#cấu-hình)
- [Git và Version Control](#git-và-version-control)
- [Cấu trúc dự án](#cấu-trúc-dự-án)
- [Cấu trúc Database](#cấu-trúc-database)
- [Vai trò và Quyền](#vai-trò-và-quyền)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
- [API Routes](#api-routes)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Giới thiệu

Hệ thống Quản lý Điểm Đại học là một ứng dụng web PHP thuần được xây dựng để quản lý điểm số, học bổng, và các hoạt động học tập của sinh viên trong môi trường đại học. Hệ thống hỗ trợ 3 khoa chính: Công nghệ Thông tin (CNTT), Ngoại Ngữ (NN), và Kinh tế (KT).

### Đặc điểm nổi bật

- ✅ **Phân quyền đa cấp**: 5 vai trò với quyền hạn riêng biệt
- ✅ **Tính toán điểm tự động**: Tự động tính Z, GPA, Letter Grade
- ✅ **Import/Export Excel**: Hỗ trợ nhập/xuất điểm hàng loạt
- ✅ **Chốt điểm an toàn**: Cơ chế chốt điểm để bảo vệ dữ liệu
- ✅ **Học bổng thông minh**: Tự động xét học bổng theo GPA và quy định
- ✅ **Cảnh báo học tập**: Hệ thống cảnh báo sinh viên có nguy cơ
- ✅ **Responsive Design**: Giao diện đẹp với Bootstrap 5, theme màu tím
- ✅ **Tương thích InfinityFree**: Đã được cấu hình sẵn cho hosting miễn phí

---

## ✨ Tính năng

### 1. Quản lý Điểm số

- Nhập điểm chi tiết: X1, X2, X3 (điểm tư cách), CC (chuyên cần), Y (cuối kỳ)
- Tự động tính toán: Z (tổng kết), GPA (0-4.0), Letter Grade (A, B, C, D)
- Kiểm tra tư cách: Tự động xác định sinh viên đủ/mất tư cách thi
- Chốt điểm: Giáo vụ có thể chốt điểm, chỉ Root có thể mở lại
- Import điểm từ Excel: Hỗ trợ nhập điểm hàng loạt
- Export điểm ra Excel/PDF: Xuất báo cáo điểm số

### 2. Điểm danh/Chuyên cần

- Điểm danh trực tuyến: Giảng viên có thể điểm danh sinh viên
- Tính điểm chuyên cần: Tự động tính dựa trên số buổi có mặt/vắng mặt
- Lịch sử điểm danh: Xem lịch sử điểm danh của từng sinh viên

### 3. Quản lý Học bổng

- Quy tắc học bổng theo khoa: Mỗi khoa có quy tắc riêng
- Tự động xét học bổng: Dựa trên GPA và số tín chỉ
- Danh sách học bổng: Xem danh sách sinh viên đạt học bổng

### 4. Cảnh báo Học tập

- Cảnh báo GPA thấp: Tự động cảnh báo sinh viên có GPA dưới ngưỡng
- Cảnh báo tín chỉ: Cảnh báo sinh viên thiếu tín chỉ
- Danh sách cảnh báo: Quản lý danh sách sinh viên cần cảnh báo

### 5. Thống kê và Báo cáo

- Dashboard theo vai trò: Mỗi vai trò có dashboard riêng
- Thống kê tổng quan: Số lượng sinh viên, lớp học, điểm số
- Báo cáo theo khoa: Trưởng khoa xem báo cáo khoa mình
- Export PDF/Excel: Xuất báo cáo ra file

### 6. Quản lý Người dùng

- Quản lý sinh viên: Root quản lý toàn bộ sinh viên
- Quản lý giảng viên: Root quản lý giảng viên
- Reset mật khẩu: Reset mật khẩu hàng loạt hoặc cá nhân
- Phân quyền: Gán vai trò và quyền cho người dùng

---

## 💻 Yêu cầu hệ thống

### Server Requirements

- **PHP**: 7.4 trở lên (khuyến nghị PHP 8.0+)
- **MySQL**: 5.7 trở lên hoặc MariaDB 10.3+
- **Web Server**: Apache với mod_rewrite hoặc Nginx
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - gd (cho PDF export)
  - zip (cho Excel import/export)

### Client Requirements

- Trình duyệt hiện đại: Chrome, Firefox, Edge, Safari (phiên bản mới nhất)
- JavaScript enabled
- Kết nối Internet (nếu sử dụng hosting)

---

## 🚀 Cài đặt

### Bước 1: Tải mã nguồn

```bash
# Clone repository hoặc tải file ZIP
git clone <repository-url>
cd qly-diem
```

### Bước 2: Cấu hình Database

**⚠️ Lưu ý quan trọng**: File `config/database.php` không được commit vào Git (đã được thêm vào `.gitignore` để bảo vệ thông tin nhạy cảm). Bạn cần tạo file này sau khi clone repository.

#### Cách 1: Sử dụng Localhost (XAMPP/WAMP)

1. Tạo database trong phpMyAdmin:

   ```sql
   CREATE DATABASE qly_diem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import file `database.sql`:

   - Mở phpMyAdmin
   - Chọn database `qly_diem`
   - Vào tab "Import"
   - Chọn file `database.sql` và nhấn "Go"

3. Tạo file `config/database.php` (file này không có trong repository):

   ```php
   <?php
   /**
    * Database Configuration
    * InfinityFree compatible
    */

   return [
       'host' => 'localhost',
       'dbname' => 'qly_diem',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4',
       'options' => [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false,
       ]
   ];
   ```

#### Cách 2: Sử dụng InfinityFree

1. Đăng nhập vào InfinityFree Control Panel
2. Tạo database mới hoặc sử dụng database có sẵn
3. Import file `database.sql` vào database
4. Tạo file `config/database.php` với thông tin database của bạn:

   ```php
   <?php
   /**
    * Database Configuration
    * InfinityFree compatible
    */

   return [
       'host' => 'sql100.infinityfree.com', // Thay bằng hostname của bạn
       'dbname' => 'your_database_name',     // Thay bằng tên database của bạn
       'username' => 'your_username',         // Thay bằng username của bạn
       'password' => 'your_password',         // Thay bằng password của bạn
       'charset' => 'utf8mb4',
       'options' => [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::ATTR_EMULATE_PREPARES => false,
       ]
   ];
   ```

**Ví dụ cấu hình InfinityFree** (tham khảo):

- **Host**: `sqlxxx.infinityfree.com`
- **Database**: `your_database_name`
- **Username**: `your_username`
- **Password**: `your_password`

### Bước 3: Cấu hình Web Server

#### Apache (.htaccess)

Đảm bảo file `.htaccess` có nội dung:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx

Thêm vào cấu hình Nginx:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Bước 4: Phân quyền thư mục (nếu cần)

```bash
# Linux/Mac
chmod -R 755 .
chmod -R 777 uploads/  # Nếu có thư mục upload

# Windows: Đảm bảo IIS_IUSRS có quyền đọc/ghi
```

### Bước 5: Truy cập ứng dụng

- **Localhost**: `http://localhost/qly-diem`
- **InfinityFree**: `https://your-domain.infinityfreeapp.com`

---

## ⚙️ Cấu hình

### File cấu hình chính

#### `config/app.php`

Cấu hình ứng dụng chính:

```php
return [
    'name' => 'Hệ thống Quản lý Điểm Đại học',
    'version' => '1.0.0',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'base_url' => '/',
    'session_lifetime' => 7200, // 2 giờ
    // ...
];
```

#### `config/database.php`

Cấu hình kết nối database

**⚠️ Lưu ý**: File này **KHÔNG** được commit vào Git (đã có trong `.gitignore`). Bạn cần tạo file này sau khi clone repository với thông tin database của bạn. Xem phần [Cài đặt](#cài-đặt) để biết cách tạo file này.

#### `config/roles.php`

Cấu hình vai trò và quyền hạn

#### `config/scholarship.php`

Cấu hình quy tắc học bổng theo khoa

### Công thức tính điểm

Mặc định trong `config/app.php`:

- **X (Điểm tư cách)**: `(X1 + X2 + X3) / 3` - Trọng số: 20%
- **CC (Chuyên cần)**: Trọng số: 10%
- **Y (Cuối kỳ)**: Trọng số: 70%
- **Z (Tổng kết)**: `X * 0.2 + CC * 0.1 + Y * 0.7`

### Thang điểm GPA

- **A** (Xuất sắc): 8.5 - 10.0 → GPA 4.0
- **B** (Giỏi): 7.0 - 8.4 → GPA 3.0
- **C** (Khá): 5.5 - 6.9 → GPA 2.0
- **D** (Trung bình): 0 - 5.4 → GPA 1.0

---

## 🔒 Git và Version Control

### .gitignore

Dự án đã được cấu hình `.gitignore` để bảo vệ thông tin nhạy cảm và loại trừ các file không cần thiết khỏi repository.

#### Các file/thư mục được ignore:

- **File cấu hình nhạy cảm**:

  - `config/database.php` - Thông tin database (quan trọng!)
  - `.env` files - Biến môi trường

- **File log và debug**:

  - `*.log` - Tất cả file log
  - `logs/` - Thư mục log

- **File tạm và cache**:

  - `*.tmp`, `*.temp`, `*.cache`
  - `cache/`, `tmp/`, `temp/`

- **File upload**:

  - `uploads/`, `files/`, `storage/`
  - `*.xlsx`, `*.xls`, `*.pdf` (trừ file mẫu trong libs)

- **Dependencies**:

  - `vendor/` - Composer packages
  - `node_modules/` - Node.js packages

- **File backup**:

  - `*.sql`, `*.bak`, `*.backup` (trừ `database.sql` và `insert_scores_attendances.txt`)

- **File hệ điều hành**:

  - Windows: `Thumbs.db`, `Desktop.ini`
  - macOS: `.DS_Store`
  - Linux: file tạm

- **IDE và Editor**:
  - `.vscode/`, `.idea/`, `*.sublime-*`, etc.

### Bảo mật

**Quan trọng**:

- ❌ **KHÔNG** commit file `config/database.php` chứa thông tin database thực tế
- ❌ **KHÔNG** commit file `.env` hoặc file chứa mật khẩu
- ✅ Luôn tạo file `config/database.php` từ template sau khi clone
- ✅ Sử dụng `.gitignore` để tự động loại trừ file nhạy cảm

### Clone và Setup

```bash
# 1. Clone repository
git clone <repository-url>
cd qly-diem

# 2. Tạo file cấu hình database (quan trọng!)
# Copy template và điền thông tin database của bạn
cp config/database.php.example config/database.php
# Hoặc tạo mới theo hướng dẫn ở phần Cài đặt

# 3. Import database
# Sử dụng phpMyAdmin hoặc MySQL client để import database.sql

# 4. (Tùy chọn) Import dữ liệu mẫu
# Import file insert_scores_attendances.txt nếu muốn có dữ liệu mẫu
```

---

## 📁 Cấu trúc dự án

```
qly-diem/
│
├── config/                          # Thư mục cấu hình
│   ├── app.php                      # Cấu hình ứng dụng (timezone, session, công thức điểm)
│   ├── database.php                 # Cấu hình kết nối database
│   ├── roles.php                    # Mapping vai trò và quyền hạn
│   └── scholarship.php              # Cấu hình quy tắc học bổng theo khoa
│
├── core/                            # Core classes (Framework tự xây dựng)
│   ├── Auth.php                     # Xử lý authentication (login, logout, session)
│   ├── Controller.php               # Base Controller class
│   ├── Database.php                 # Database connection singleton (PDO)
│   ├── Model.php                    # Base Model class với CRUD operations
│   └── Router.php                   # Router đơn giản (GET, POST routes)
│
├── middleware/                      # Middleware classes
│   ├── AuthMiddleware.php           # Kiểm tra đăng nhập
│   ├── RoleMiddleware.php           # Kiểm tra quyền truy cập theo vai trò
│   └── ScoreLockMiddleware.php      # Kiểm tra trạng thái chốt điểm
│
├── controllers/                     # Controllers xử lý logic
│   ├── auth/                        # Authentication controllers
│   │   ├── LoginController.php      # Xử lý đăng nhập
│   │   ├── LogoutController.php     # Xử lý đăng xuất
│   │   └── ResetPasswordController.php  # Reset mật khẩu (forgot password)
│   │
│   ├── root/                        # Root admin controllers
│   │   ├── RootDashboardController.php    # Dashboard tổng quan
│   │   ├── StudentManagementController.php # Quản lý sinh viên
│   │   ├── LecturerManagementController.php # Quản lý giảng viên
│   │   └── SystemController.php     # Quản lý hệ thống (reset password)
│   │
│   ├── dean/                        # Trưởng khoa controllers
│   │   ├── DeanDashboardController.php    # Dashboard khoa
│   │   ├── FacultyReportController.php    # Báo cáo khoa (export PDF/Excel)
│   │   └── ScholarshipController.php      # Quản lý học bổng khoa
│   │
│   ├── academicAffairs/             # Giáo vụ controllers
│   │   ├── AcademicDashboardController.php    # Dashboard giáo vụ
│   │   ├── AcademicStudentController.php      # Quản lý sinh viên
│   │   ├── ScoreSummaryController.php         # Tổng hợp điểm (chốt điểm)
│   │   ├── WarningController.php             # Cảnh báo học tập
│   │   └── AcademicScholarshipController.php  # Quản lý học bổng
│   │
│   ├── lecturer/                    # Giảng viên controllers
│   │   ├── ClassController.php      # Quản lý lớp học
│   │   ├── ScoreInputController.php # Nhập điểm
│   │   ├── AttendanceController.php # Điểm danh/chuyên cần
│   │   └── ImportScoreController.php # Import điểm từ Excel
│   │
│   ├── student/                     # Sinh viên controllers
│   │   ├── StudentDashboardController.php    # Dashboard sinh viên
│   │   └── StudentScoreController.php        # Xem điểm số
│   │
│   └── DashboardController.php      # Controller điều hướng dashboard theo role
│
├── models/                          # Model classes (tương tác với database)
│   ├── User.php                     # Model người dùng
│   ├── Faculty.php                  # Model khoa
│   ├── Major.php                    # Model ngành học
│   ├── Department.php               # Model bộ môn
│   ├── Subject.php                  # Model môn học
│   ├── ClassRoom.php                # Model lớp học
│   ├── Enrollment.php              # Model đăng ký lớp (sinh viên - lớp)
│   ├── Attendance.php              # Model điểm danh/chuyên cần
│   ├── Score.php                   # Model điểm số
│   ├── ScoreLock.php               # Model trạng thái chốt điểm
│   └── ScholarshipRule.php         # Model quy tắc học bổng
│
├── views/                           # View files (HTML/PHP)
│   ├── layouts/                     # Layout chung
│   │   ├── header.php               # Header (navbar, CSS)
│   │   ├── sidebar.php              # Sidebar menu theo role
│   │   └── footer.php               # Footer (JavaScript)
│   │
│   ├── auth/                        # Views authentication
│   │   ├── login.php                # Trang đăng nhập
│   │   ├── forgot-password.php      # Quên mật khẩu
│   │   ├── reset-password.php       # Đặt lại mật khẩu
│   │   └── verify-email.php         # Xác thực email
│   │
│   ├── root/                        # Views Root admin
│   │   ├── dashboard.php            # Dashboard tổng quan
│   │   ├── student_management.php   # Quản lý sinh viên
│   │   ├── student_detail.php       # Chi tiết sinh viên
│   │   ├── lecturer_management.php  # Quản lý giảng viên
│   │   └── system.php               # Quản lý hệ thống
│   │
│   ├── dean/                        # Views Trưởng khoa
│   │   ├── dashboard.php            # Dashboard khoa
│   │   ├── report.php               # Báo cáo khoa
│   │   └── scholarship.php          # Học bổng khoa
│   │
│   ├── academicAffairs/             # Views Giáo vụ
│   │   ├── dashboard.php            # Dashboard giáo vụ
│   │   ├── student_list.php         # Danh sách sinh viên
│   │   ├── student_detail.php       # Chi tiết sinh viên
│   │   ├── score-summary.php        # Tổng hợp điểm (chốt điểm)
│   │   ├── warning.php              # Cảnh báo học tập
│   │   └── scholarship.php          # Học bổng
│   │
│   ├── lecturer/                    # Views Giảng viên
│   │   ├── dashboard.php            # Dashboard giảng viên
│   │   ├── classes.php              # Danh sách lớp học
│   │   ├── class-detail.php         # Chi tiết lớp học
│   │   ├── score-input.php          # Nhập điểm
│   │   ├── attendance.php           # Điểm danh
│   │   └── import-score.php         # Import điểm Excel
│   │
│   └── student/                     # Views Sinh viên
│       ├── dashboard.php            # Dashboard sinh viên
│       └── scores.php               # Xem điểm số
│
├── services/                        # Business logic services
│   ├── ScoreCalculator.php          # Tính toán điểm (Z, GPA, Letter)
│   ├── GPAService.php               # Tính toán GPA tổng hợp
│   ├── ScholarshipService.php       # Xét học bổng
│   └── StatisticService.php         # Thống kê và báo cáo
│
├── helpers/                         # Helper functions
│   ├── url_helper.php               # URL helper functions
│   ├── auth_helper.php              # Authentication helpers
│   ├── score_helper.php             # Score calculation helpers
│   └── attendance_helper.php        # Attendance helpers
│
├── import/                          # Import modules
│   └── excel/
│       └── ScoreImportXlsx.php      # Import điểm từ Excel (SimpleXLSX)
│
├── export/                          # Export modules
│   └── excel/
│       └── ScoreExportXlsx.php      # Export điểm ra Excel (SimpleXLSXGen)
│
├── libs/                            # Thư viện bên thứ ba
│   ├── simplexlsx/                  # Đọc file Excel
│   │   └── src/
│   │       ├── SimpleXLSX.php
│   │       └── SimpleXLSXEx.php
│   │
│   ├── simplexlsxgen/               # Ghi file Excel
│   │   └── src/
│   │       └── SimpleXLSXGen.php
│   │
│   └── TCPDF/                       # Tạo file PDF
│       └── tcpdf.php
│
├── routes/                          # Route definitions
│   └── web.php                      # Tất cả routes của ứng dụng
│
├── database.sql                     # Database schema và dữ liệu mẫu
├── insert_scores_attendances.txt    # Script INSERT điểm và điểm danh mẫu
├── index.php                        # Entry point của ứng dụng
├── .htaccess                        # Apache rewrite rules
├── .gitignore                       # Git ignore rules (bảo vệ file nhạy cảm)
└── README.md                        # File này
```

### Mô tả các thư mục chính

#### `config/`

Chứa tất cả các file cấu hình: database, ứng dụng, vai trò, học bổng.

#### `core/`

Framework tự xây dựng với các class cốt lõi:

- **Database**: Singleton pattern cho PDO connection
- **Model**: Base class cho tất cả models với CRUD operations
- **Controller**: Base class cho controllers
- **Auth**: Xử lý authentication và session
- **Router**: Router đơn giản hỗ trợ GET/POST

#### `middleware/`

Middleware kiểm tra trước khi xử lý request:

- **AuthMiddleware**: Kiểm tra đăng nhập
- **RoleMiddleware**: Kiểm tra quyền theo vai trò
- **ScoreLockMiddleware**: Kiểm tra trạng thái chốt điểm

#### `controllers/`

Tổ chức theo vai trò, mỗi controller xử lý logic cho một nhóm chức năng.

#### `models/`

Mỗi model tương ứng với một bảng database, kế thừa từ `Model` base class.

#### `views/`

Tổ chức theo vai trò, sử dụng Bootstrap 5 với theme màu tím.

#### `services/`

Business logic phức tạp được tách ra thành services:

- Tính toán điểm, GPA
- Xét học bổng
- Thống kê

#### `helpers/`

Các hàm helper dùng chung trong toàn bộ ứng dụng.

---

## 🗄️ Cấu trúc Database

### Các bảng chính

1. **users** - Người dùng (Root, Trưởng khoa, Giáo vụ, Giảng viên, Sinh viên)
2. **faculties** - Khoa (CNTT, Ngoại Ngữ, Kinh tế)
3. **majors** - Ngành học
4. **departments** - Bộ môn
5. **subjects** - Môn học
6. **class_rooms** - Lớp học
7. **enrollments** - Đăng ký lớp (sinh viên - lớp)
8. **attendances** - Điểm danh/chuyên cần
9. **scores** - Điểm số (X1, X2, X3, CC, Y, Z, GPA, Letter)
10. **score_locks** - Trạng thái chốt điểm
11. **scholarship_rules** - Quy tắc học bổng

### Quan hệ giữa các bảng

```
users → faculties (dean, academic_affairs)
users → departments (lecturer)
users → student_code (student)

faculties → majors
departments → faculties
subjects → departments
class_rooms → subjects, users (lecturer)

enrollments → class_rooms, users (student)
scores → enrollments
attendances → enrollments

score_locks → class_rooms
scholarship_rules → faculties
```

### Import Database

```bash
# Sử dụng MySQL command line
mysql -u username -p database_name < database.sql

# Hoặc sử dụng phpMyAdmin
# 1. Chọn database
# 2. Tab "Import"
# 3. Chọn file database.sql
# 4. Click "Go"
```

### Import Dữ liệu mẫu (Tùy chọn)

Sau khi import `database.sql`, bạn có thể import thêm dữ liệu mẫu từ file `insert_scores_attendances.txt`:

```bash
# Sử dụng MySQL command line
mysql -u username -p database_name < insert_scores_attendances.txt

# Hoặc sử dụng phpMyAdmin
# 1. Chọn database
# 2. Tab "Import"
# 3. Chọn file insert_scores_attendances.txt
# 4. Click "Go"
```

**Lưu ý**: File này chứa dữ liệu mẫu cho bảng `scores` và `attendances`. Chỉ import nếu bạn muốn có dữ liệu mẫu để test.

---

## 👥 Vai trò và Quyền

### 1. Root (Quản trị viên hệ thống)

**Quyền hạn:**

- Xem thống kê toàn hệ thống
- Quản lý tất cả sinh viên
- Quản lý tất cả giảng viên
- Reset mật khẩu hàng loạt
- Mở khóa điểm đã chốt
- Xem tất cả báo cáo

**Tài khoản mặc định:**

- Username: `root`
- Password: `123456`

### 2. Dean (Trưởng khoa)

**Quyền hạn:**

- Xem thống kê khoa mình
- Xem báo cáo khoa (export PDF/Excel)
- Xem điểm số sinh viên trong khoa
- Quản lý học bổng khoa
- Xem danh sách sinh viên khoa

**Tài khoản mặc định:**

- Username: `DEAN_CNTT` (hoặc `DEAN_NN`, `DEAN_KT`)
- Password: `123456`

### 3. Academic Affairs (Giáo vụ)

**Quyền hạn:**

- Xem thống kê khoa
- Quản lý sinh viên trong khoa
- Xem và tổng hợp điểm số
- **Chốt điểm** (quan trọng!)
- Quản lý cảnh báo học tập
- Quản lý học bổng
- Export báo cáo

**Tài khoản mặc định:**

- Username: `GV_CNTT` (hoặc `GV_NN`, `GV_KT`)
- Password: `123456`

### 4. Lecturer (Giảng viên)

**Quyền hạn:**

- Xem danh sách lớp mình dạy
- Nhập điểm (X1, X2, X3, CC, Y)
- Điểm danh sinh viên
- Import điểm từ Excel
- Xem điểm số lớp mình

**Tài khoản mặc định:**

- Username: `GV001`, `GV002`, ...
- Password: `123456`

### 5. Student (Sinh viên)

**Quyền hạn:**

- Xem điểm số của mình
- Xem GPA tổng hợp
- Xem lịch sử học tập
- Xem thông báo học bổng/cảnh báo

**Tài khoản mặc định:**

- Username: `SV001`, `SV002`, ...
- Password: `123456`

---

## 📖 Hướng dẫn sử dụng

### Đăng nhập

1. Truy cập: `http://your-domain/login`
2. Nhập username và password
3. Hệ thống sẽ tự động điều hướng đến dashboard theo vai trò

### Quy trình nhập điểm (Giảng viên)

1. Đăng nhập với tài khoản giảng viên
2. Vào **"Lớp học"** → Chọn lớp cần nhập điểm
3. Vào **"Nhập điểm"** → Chọn lớp và môn học
4. Nhập điểm:
   - X1, X2, X3 (điểm tư cách)
   - CC (chuyên cần)
   - Y (cuối kỳ)
5. Hệ thống tự động tính Z, GPA, Letter
6. Lưu điểm

### Quy trình chốt điểm (Giáo vụ)

1. Đăng nhập với tài khoản giáo vụ
2. Vào **"Tổng hợp điểm"**
3. Chọn lớp và học kỳ
4. Xem danh sách điểm
5. Nhấn **"Chốt điểm"** để khóa điểm (không thể sửa)
6. Chỉ Root mới có thể mở khóa

### Import điểm từ Excel

1. Vào **"Import điểm"**
2. Tải template Excel
3. Điền thông tin điểm vào template
4. Upload file Excel
5. Hệ thống sẽ tự động import và tính toán điểm

### Export báo cáo

1. Vào trang báo cáo (tùy vai trò)
2. Chọn học kỳ/khoa
3. Nhấn **"Export Excel"** hoặc **"Export PDF"**
4. File sẽ được tải về

---

## 🔌 API Routes

### Authentication Routes

```
GET  /                    → Redirect to dashboard or login
GET  /login              → Hiển thị form đăng nhập
POST /login              → Xử lý đăng nhập
GET  /logout             → Đăng xuất
GET  /forgot-password    → Form quên mật khẩu
POST /forgot-password    → Gửi email reset password
GET  /reset-password     → Form đặt lại mật khẩu
POST /reset-password     → Xử lý đặt lại mật khẩu
```

### Root Routes

```
GET  /root/dashboard           → Dashboard tổng quan
GET  /root/api/stats           → API thống kê
GET  /root/api/locks           → API danh sách chốt điểm
POST /root/api/unlock          → Mở khóa điểm
GET  /root/students            → Quản lý sinh viên
GET  /root/students/detail     → Chi tiết sinh viên
POST /root/students/api        → API danh sách sinh viên
GET  /root/lecturers           → Quản lý giảng viên
POST /root/lecturers/api       → API danh sách giảng viên
GET  /root/system              → Quản lý hệ thống
POST /root/system              → Reset mật khẩu
```

### Dean Routes

```
GET  /dean/dashboard      → Dashboard khoa
GET  /dean/report         → Báo cáo khoa
GET  /dean/report/export  → Export báo cáo
GET  /dean/scholarship    → Học bổng khoa
```

### Academic Affairs Routes

```
GET  /academic/dashboard              → Dashboard giáo vụ
GET  /academic/api/stats             → API thống kê
GET  /academic/students               → Danh sách sinh viên
GET  /academic/students/detail        → Chi tiết sinh viên
POST /academic/students/api          → API danh sách sinh viên
GET  /academic/scores                 → Tổng hợp điểm
POST /academic/scores/api/classes     → API danh sách lớp
GET  /academic/scores/api/detail      → API chi tiết điểm lớp
GET  /academic/scores/export          → Export điểm
POST /academic/scores/lock            → Chốt điểm
GET  /academic/warning                → Cảnh báo học tập
POST /academic/warning/api            → API danh sách cảnh báo
GET  /academic/scholarship            → Học bổng
```

### Lecturer Routes

```
GET  /lecturer/dashboard                    → Dashboard giảng viên
GET  /lecturer/classes                      → Danh sách lớp
GET  /lecturer/class                         → Chi tiết lớp
GET  /lecturer/scores                        → Nhập điểm
POST /lecturer/scores/save                   → Lưu điểm
GET  /lecturer/attendance                    → Điểm danh
POST /lecturer/attendance/save               → Lưu điểm danh
GET  /lecturer/import-score                  → Import điểm
GET  /lecturer/import-score/template         → Tải template Excel
POST /lecturer/import-score                  → Xử lý import
```

### Student Routes

```
GET  /student/dashboard  → Dashboard sinh viên
GET  /student/scores     → Xem điểm số
```

---

## 🛠️ Công nghệ sử dụng

### Backend

- **PHP**: 7.4+ (Pure PHP, không dùng framework)
- **MySQL**: Database
- **PDO**: Database abstraction layer
- **Session**: Quản lý session

### Frontend

- **Bootstrap 5**: CSS framework
- **jQuery**: JavaScript library (nếu có)
- **DataTables**: Bảng dữ liệu (nếu có)
- **Chart.js**: Biểu đồ (nếu có)

### Libraries

- **SimpleXLSX**: Đọc file Excel
- **SimpleXLSXGen**: Ghi file Excel
- **TCPDF**: Tạo file PDF

### Architecture

- **MVC Pattern**: Model-View-Controller
- **Singleton Pattern**: Database connection
- **Middleware Pattern**: Auth, Role, ScoreLock
- **Service Layer**: Business logic

---

## 🔧 Troubleshooting

### Lỗi kết nối database

**Triệu chứng**: "Database connection failed"

**Giải pháp**:

1. Kiểm tra thông tin trong `config/database.php`
2. Đảm bảo database đã được tạo
3. Kiểm tra username/password
4. Kiểm tra hostname (localhost hoặc remote host)
5. Kiểm tra firewall nếu dùng remote database

### Lỗi 404 Not Found

**Triệu chứng**: Trang không tìm thấy

**Giải pháp**:

1. Kiểm tra `.htaccess` có tồn tại
2. Đảm bảo Apache mod_rewrite đã bật
3. Kiểm tra `base_url` trong `config/app.php`
4. Kiểm tra quyền thư mục

### Lỗi session

**Triệu chứng**: Không đăng nhập được, session mất

**Giải pháp**:

1. Kiểm tra quyền ghi thư mục session
2. Kiểm tra `session_lifetime` trong `config/app.php`
3. Xóa cookies và thử lại
4. Kiểm tra timezone trong `config/app.php`

### Lỗi import Excel

**Triệu chứng**: Không import được file Excel

**Giải pháp**:

1. Kiểm tra định dạng file (phải là .xlsx)
2. Kiểm tra template có đúng format không
3. Kiểm tra quyền ghi thư mục (nếu có upload)
4. Kiểm tra PHP extension `zip` đã bật

### Lỗi tính toán điểm sai

**Triệu chứng**: Điểm Z, GPA tính sai

**Giải pháp**:

1. Kiểm tra công thức trong `config/app.php`
2. Kiểm tra `ScoreCalculator.php`
3. Đảm bảo điểm nhập vào đúng format (0-10)
4. Xem log lỗi PHP

### Lỗi trên InfinityFree

**Triệu chứng**: Không kết nối được database

**Giải pháp**:

1. Đảm bảo đã import `database.sql`
2. Kiểm tra database name có đúng prefix không
3. Kiểm tra hostname: `sql100.infinityfree.com`
4. Đảm bảo database đã được kích hoạt trong Control Panel

---

## 📝 Ghi chú

- Tất cả mật khẩu mặc định là `123456` - **Nên đổi ngay sau khi cài đặt!**
- Hệ thống sử dụng SHA2-256 để hash mật khẩu
- Session timeout mặc định: 2 giờ
- Hệ thống hỗ trợ UTF-8 (tiếng Việt)
- Database charset: `utf8mb4`
- **File `config/database.php` không được commit vào Git** - Tạo file này sau khi clone repository
- File `.gitignore` đã được cấu hình để bảo vệ thông tin nhạy cảm

---

## 📄 License

Dự án này được phát triển cho mục đích giáo dục và quản lý điểm số đại học.

---

## 👨‍💻 Tác giả

Phát triển bởi: **QDev26**

---

## 📞 Hỗ trợ

Nếu gặp vấn đề, vui lòng:

1. Kiểm tra phần Troubleshooting
2. Xem log lỗi PHP
3. Kiểm tra database connection
4. Liên hệ developer

---

**Chúc bạn sử dụng hệ thống thành công! 🎉**
