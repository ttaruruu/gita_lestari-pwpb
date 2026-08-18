CREATE DATABASE IF NOT EXISTS `kelasin` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kelasin`;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nisn VARCHAR(20) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(120) DEFAULT NULL,
  alamat TEXT DEFAULT NULL,
  sekolah VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE classes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kelas VARCHAR(80) NOT NULL,
  wali_kelas VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  class_id INT UNSIGNED NOT NULL,
  nis VARCHAR(30) DEFAULT NULL,
  nama VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_students_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  tanggal DATE NOT NULL,
  status ENUM('Hadir','Izin','Sakit','Alpa') NOT NULL DEFAULT 'Hadir',
  catatan VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_student_date (student_id, tanggal),
  CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (nisn,password,nama,email,alamat,sekolah)
VALUES ('1234567890','$2y$12$FKSIQqTS9afFNPTsWf44gOSgeKPYgsD/7ypLWYzEQbl/ecS6.YCC2','Gita Lestari','gita@email.com','','SMKN 1 Katapang');

INSERT INTO classes (nama_kelas,wali_kelas) VALUES
('XI - RPL 1','Wali Kelas RPL 1'),
('XI - RPL 2','Wali Kelas RPL 2');

INSERT INTO students (class_id,nis,nama) VALUES
(1,'RPL101','Alya'),
(1,'RPL102','Nadia'),
(1,'RPL103','Salsa'),
(2,'RPL201','Gita'),
(2,'RPL202','Nabila'),
(2,'RPL203','Rani'),
(2,'RPL204','Sinta');

INSERT INTO attendance (student_id,tanggal,status,catatan) VALUES
(4,CURDATE(),'Hadir',''),
(5,CURDATE(),'Izin','Keperluan keluarga'),
(6,CURDATE(),'Hadir',''),
(7,CURDATE(),'Sakit','');
