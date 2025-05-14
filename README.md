# 📘 Documentación Técnica – Sistema de Gestión "RentaVeloz"

## 📌 Descripción del proyecto
Este sistema permite administrar una empresa de alquiler de autos, con funcionalidades para la gestión de usuarios, vehículos, reservas y mantenimiento. Incluye una **landing page** de inicio y una **interfaz administrativa** protegida para operar el sistema.

---

## 🧱 Pasos para levantar el proyecto

### 1️⃣ Crear la base de datos

Abrir PHPMyAdmin y ejecutar la siguiente sentencia SQL:

```sql
CREATE DATABASE rentaveloz;
```

---

### 2️⃣ Crear la tabla de Usuarios

```sql
CREATE TABLE system_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 3️⃣ Insertar un usuario administrador de prueba

```sql
INSERT INTO system_user (name, email, password, role)
VALUES ('Pedro', 'admin@rentaveloz.com', '1234', 'admin');
```

---

### 4️⃣ Crear la tabla de Autos

```sql
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    title VARCHAR(100) NOT NULL,
    km VARCHAR(50),
    old_price DECIMAL(10, 2),
    new_price DECIMAL(10, 2),
    engine VARCHAR(100),
    transmission VARCHAR(20),
    passengers INT,
    air_conditioning TINYINT(1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 5️⃣ Crear la tabla de Reservas

```sql
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    days INT NOT NULL,
    init_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_amount DECIMAL(10, 2),
    payment_method VARCHAR(50),
    status_code INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES system_user(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);
```

---

### 6️⃣ Crear la tabla de Mantenimientos

```sql
CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    service_type VARCHAR(100) NOT NULL,
    description TEXT,
    cost DECIMAL(10, 2),
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);
```

---

### 7️⃣ Ejecutar la aplicación

Una vez creadas las tablas, abrir el navegador y acceder a la siguiente ruta:

```
http://localhost/RentalCarApp/index.php
```
