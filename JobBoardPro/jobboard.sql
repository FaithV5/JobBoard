CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    name VARCHAR(100),
    email VARCHAR(100),
    role ENUM('admin', 'employee') DEFAULT 'employee'
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    title VARCHAR(100),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    position_id INT,
    phone VARCHAR(20),
    address TEXT,
    birthday DATE,
    age INT,
    gender VARCHAR(10),
    cover_letter TEXT,
    resume VARCHAR(255),
    other_docs VARCHAR(255),
    status ENUM('pending', 'interview', 'hired', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (position_id) REFERENCES positions(id)
);
