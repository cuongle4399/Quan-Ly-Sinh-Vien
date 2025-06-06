-- Xóa database nếu đã tồn tại để bắt đầu từ đầu
DROP DATABASE IF EXISTS QuanLySinhVien;
CREATE DATABASE QuanLySinhVien;
USE QuanLySinhVien;

-- Tạo bảng NguoiDung: Lưu thông tin tài khoản sinh viên
CREATE TABLE NguoiDung (
    MaSinhVien VARCHAR(30) PRIMARY KEY,
    MatKhau VARCHAR(30),
    VaiTro INT, -- 0: Sinh viên, 1: Quản trị viên, v.v.
    Email VARCHAR(50)
);

-- Tạo bảng Nganh: Lưu thông tin các ngành học
CREATE TABLE Nganh (
    MaNganh VARCHAR(30) PRIMARY KEY,
    TenNganh VARCHAR(50),
    GiaCua1TinChi INT
);

-- Tạo bảng ThongTinCaNhan: Lưu thông tin cá nhân của sinh viên
CREATE TABLE ThongTinCaNhan (
    STT INT AUTO_INCREMENT PRIMARY KEY,
    MaSinhVien VARCHAR(30) UNIQUE NOT NULL,
	LinkAvatar NVARCHAR(100),
    HoTen VARCHAR(50),
    GioiTinh VARCHAR(50),
    SoDienThoai VARCHAR(50),
    NgaySinh DATE,
    DanToc VARCHAR(50),
    SoCCCD BIGINT,
    TinhThanhPho VARCHAR(50),
    QuanHuyen VARCHAR(50),
    QuocGia VARCHAR(50),
    DiaChiThuongTru VARCHAR(50),
    TrinhDoNgoaiNgu VARCHAR(50),
    TinhTrangHoc VARCHAR(50),
    MaNganh VARCHAR(30),
    NienKhoa VARCHAR(50),
    LoaiHinhDaoTao VARCHAR(50),
    LopSinhVien VARCHAR(50),
    ChucVu VARCHAR(50),
    CoVanHocTap VARCHAR(50),
    LH_CVHT VARCHAR(50),
    TenNganHang VARCHAR(50),
    SoTaiKhoan VARCHAR(50),
    EmailNganHang VARCHAR(50),
    TenNguoiThan VARCHAR(50),
    QuanHe VARCHAR(50),
    SDTNguoiThan VARCHAR(30),
    DiaChiNguoiThan VARCHAR(50),
    FOREIGN KEY (MaSinhVien) REFERENCES NguoiDung(MaSinhVien),
    FOREIGN KEY (MaNganh) REFERENCES Nganh(MaNganh)
);

-- Tạo bảng ChuongTrinhDaoTao: Lưu thông tin học phần
CREATE TABLE ChuongTrinhDaoTao (
    MaHocPhan VARCHAR(30) PRIMARY KEY,
    TenHocPhan VARCHAR(50),
    SoTinChi INT,
    LyThuyet INT,
    ThucHanh INT,
    TuLuan INT,
    ThucTap INT,
    HocPhanHocTruoc VARCHAR(50),
    HocPhanThayThe VARCHAR(50),
    MaNganh VARCHAR(30),
    HocKy INT,
    FOREIGN KEY (MaNganh) REFERENCES Nganh(MaNganh)
);

-- Tạo bảng DangKyHocPhan: Lưu thông tin lớp học phần
CREATE TABLE DangKyHocPhan (
    MaLopHocPhan VARCHAR(30) PRIMARY KEY,
    MaHocPhan VARCHAR(30),
    NgayBatDau DATETIME,
    NgayKetThuc DATETIME,
    GiangVien VARCHAR(50),
    LichHoc DATETIME,
    TenLopHocPhan VARCHAR(50),
    FOREIGN KEY (MaHocPhan) REFERENCES ChuongTrinhDaoTao(MaHocPhan)
);

-- Tạo bảng KetQuaDangKyHocPhan: Lưu kết quả đăng ký học phần của sinh viên
CREATE TABLE KetQuaDangKyHocPhan (
    STT INT AUTO_INCREMENT PRIMARY KEY,
    MaLopHocPhan VARCHAR(30),
    NgayDangKy DATETIME,
    TenHocPhan VARCHAR(50),
    MaSinhVien VARCHAR(30),
    FOREIGN KEY (MaLopHocPhan) REFERENCES DangKyHocPhan(MaLopHocPhan),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien)
);

-- Tạo bảng Diem: Lưu điểm của sinh viên cho từng lớp học phần
CREATE TABLE Diem (
    MaLopHocPhan VARCHAR(30),
    SoTinChi INT,
    DiemCC FLOAT,
    DiemCk FLOAT,
    MaSinhVien VARCHAR(30),
    PRIMARY KEY (MaSinhVien, MaLopHocPhan),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES DangKyHocPhan(MaLopHocPhan)
);

CREATE TABLE DiemRenLuyen (
    MaSinhVien VARCHAR(30),
    NamHoc VARCHAR(9), -- Format: 'YYYY-YYYY' (e.g., '2023-2024')
    HocKy INT,
    DiemRenLuyen INT,
    PRIMARY KEY (MaSinhVien, NamHoc, HocKy),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien)
);

-- Tạo bảng LichThi: Lưu lịch thi của sinh viên
CREATE TABLE LichThi (
    STT INT AUTO_INCREMENT PRIMARY KEY,
    TenHocPhan VARCHAR(50),
    NgayThi DATE,
    GioThi TIME,
    ThoiLuong INT,
    PhongThi VARCHAR(50),
    LinkPhongThi VARCHAR(50),
    LinkNopBai VARCHAR(50),
    DiaDiem VARCHAR(50),
    GhiChu VARCHAR(50),
    MaLopHocPhan VARCHAR(30),
    MaSinhVien VARCHAR(30),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES DangKyHocPhan(MaLopHocPhan)
);

-- Tạo bảng ChungChi: Lưu thông tin chứng chỉ của sinh viên
CREATE TABLE ChungChi (
    STT INT AUTO_INCREMENT PRIMARY KEY,
    MaSinhVien VARCHAR(30),
    ChuongTrinhDaoTao VARCHAR(50),
    TenChungChi VARCHAR(50),
    SoHieuBang VARCHAR(50),
    SoVaoSo VARCHAR(50),
    SoQuyetDinh VARCHAR(50),
    NgayCap DATE,
    NoiCap VARCHAR(50),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien)
);

-- Tạo bảng KetQuaXetTotNghiep: Lưu kết quả xét tốt nghiệp
CREATE TABLE KetQuaXetTotNghiep (
    STT INT AUTO_INCREMENT PRIMARY KEY,
    TenDot VARCHAR(50),
    TenChuongTrinhDaoTao VARCHAR(50),
    NgayXet DATETIME,
    KetQua VARCHAR(50),
    GhiChu VARCHAR(50),
    TBCTichLuy INT,
    XepLoai VARCHAR(50),
    MaLopHocPhan VARCHAR(30),
    MaSinhVien VARCHAR(30),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES DangKyHocPhan(MaLopHocPhan)
);

-- Tạo bảng ChiTietHocPhi: Lưu thông tin học phí
CREATE TABLE ChiTietHocPhi (
    MaPhi VARCHAR(30),
    MaLopHocPhan VARCHAR(30),
    MaSinhVien VARCHAR(30),
    TrangThai TINYINT(1), -- 1: Đã đóng, 0: Chưa đóng
    PRIMARY KEY (MaPhi, MaLopHocPhan, MaSinhVien),
    FOREIGN KEY (MaLopHocPhan) REFERENCES DangKyHocPhan(MaLopHocPhan),
    FOREIGN KEY (MaSinhVien) REFERENCES ThongTinCaNhan(MaSinhVien)
);
CREATE TABLE TinTuc (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255) NOT NULL,
    NoiDung LONGTEXT NOT NULL,
    NgayDang DATE DEFAULT (CURRENT_DATE)
);
CREATE TABLE QuyDinh (
    MaQuyDinh INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255) NOT NULL,            
    MoTa TEXT,                                
    LoaiQuyDinh VARCHAR(100),                
    MucDoViPham VARCHAR(100),                
    HinhThucXuLy VARCHAR(255),               
    GhiChu VARCHAR(255)
);

