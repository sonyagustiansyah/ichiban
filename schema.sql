CREATE TABLE timestamp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    nama_pos VARCHAR(100) NOT NULL,
    tipe ENUM('NOV','AO','NO','NOO','POS') NOT NULL,
    area VARCHAR(100) NOT NULL,
    status ENUM('visit tambahan','visit wajib') NOT NULL,
    tujuan ENUM('visit','visit susulan') NOT NULL,
    `order` VARCHAR(100),
    qty INT,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);