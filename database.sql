-- database.sql
CREATE DATABASE IF NOT EXISTS environmental_reports CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE environmental_reports;

-- جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('data_entry','emergency','em_manager','gm') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول البلاغات
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    hijri_date VARCHAR(20),
    gregorian_date DATE,
    reporter_name VARCHAR(100),
    reporter_phone VARCHAR(20),
    reporter_id VARCHAR(20),
    category ENUM('incident','violation','other') NOT NULL,
    subcategory VARCHAR(100),
    location_name VARCHAR(150),
    location_details TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    observation_time ENUM('morning','evening'),
    source_type VARCHAR(100),
    source_name VARCHAR(150),
    receipt_method ENUM('fax','email','phone','voice','letter','988') NOT NULL,
    receiver_name VARCHAR(100),
    receipt_time DATETIME,
    priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    
    -- الحالة الحالية للبلاغ
    status ENUM('pending_em','pending_em_manager','pending_gm','approved','rejected','closed') 
           DEFAULT 'pending_em',
    
    -- ملاحظات إدارة الطوارئ
    em_notes TEXT,
    em_actions TEXT,
    em_decision ENUM('approve','reject'),
    em_reject_reason TEXT,
    
    -- ملاحظات مدير الطوارئ
    em_manager_notes TEXT,
    em_manager_decision ENUM('approve','reject'),
    em_manager_reject_reason TEXT,
    
    -- ملاحظات المدير العام
    gm_notes TEXT,
    gm_decision ENUM('approve','reject','close'),
    gm_reject_reason TEXT,
    
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول المرفقات
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image','pdf','word','video') NOT NULL,
    uploaded_by INT,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE reports 
ADD COLUMN em_name VARCHAR(100) NULL AFTER em_notes,
ADD COLUMN em_decision_at DATETIME NULL AFTER em_notes;

ALTER TABLE reports 
ADD COLUMN updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE reports 
ADD COLUMN closed_at DATETIME NULL;


-- جدول التصنيفات الأساسية
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- جدول التصنيفات الفرعية
CREATE TABLE subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- جدول مصادر التلوث/الحادث
CREATE TABLE sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- جدول وسائل الاستلام
CREATE TABLE receipt_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- إضافة البيانات الافتراضية
INSERT INTO categories (name) VALUES ('حادث'), ('تجاوز بيئي'), ('أخرى');

INSERT INTO subcategories (category_id, name) VALUES 
(1, 'تلوث بري'), (1, 'أدخنة'), (1, 'ضوضاء'), (1, 'تصادم');

INSERT INTO sources (name) VALUES ('مصنع') ;
INSERT INTO receipt_methods (name) VALUES  ('فاكس');