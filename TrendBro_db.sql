CREATE DATABASE trendbro_db;

USE trendbro_db;

-- Main categories table
CREATE TABLE main_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Subcategories table, linked to main categories
CREATE TABLE sub_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    main_category_id INT,
    FOREIGN KEY (main_category_id) REFERENCES main_category(id) ON DELETE CASCADE
);
ALTER TABLE sub_category ADD COLUMN link VARCHAR(255);


-- Products table, now with three image columns, linked to subcategories
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image1 VARCHAR(255) NOT NULL,  -- First image
    image2 VARCHAR(255),           -- Second image
    image3 VARCHAR(255),           -- Third image
    sub_category_id INT,
    FOREIGN KEY (sub_category_id) REFERENCES sub_category(id) ON DELETE CASCADE
);
ALTER TABLE products
DROP COLUMN description,
ADD COLUMN size VARCHAR(50),
ADD COLUMN brand VARCHAR(100);
ALTER TABLE products ADD COLUMN stock INT NOT NULL DEFAULT 0;


-- Featured products table, linked to products
CREATE TABLE featured_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    link VARCHAR(255) NOT NULL
);


-- Users table with only name, phone number, and password
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Admin table with username and password for admin users
CREATE TABLE admin (
    phone VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


-- Cart table, linked to users and products
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

ALTER TABLE cart
ADD COLUMN image1 VARCHAR(255);




-- Wishlist table, linked to users and products
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table, linked to users
-- Updated Orders table with new fields
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,     -- Full name of the customer
    email VARCHAR(100) NOT NULL,          -- Customer's email address
    phone_number VARCHAR(15) NOT NULL,    -- Customer's phone number
    delivery_address TEXT,                 -- Address for delivery
    state VARCHAR(100),                    -- State for delivery
    total_price DECIMAL(10, 2),
    payment_status VARCHAR(50) DEFAULT 'Pending',   -- Payment status
    delivery_status VARCHAR(50) DEFAULT 'Processing', -- Delivery status
    payment_method VARCHAR(50),             -- Payment method
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE orders
ADD COLUMN image1 VARCHAR(255);



-- Order items table, linked to orders and products
-- Orders table, linked to users, now with additional fields


-- Order items table, linked to orders and products, with more details
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10, 2),         -- Price per item at the time of purchase
    total_price DECIMAL(10, 2),   -- Total price for the quantity ordered
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

ALTER TABLE trendbro_db.order_items
ADD COLUMN image1 VARCHAR(255);




INSERT INTO `trendbro_db`.`main_category` (`id`, `name`) VALUES ('1', 'TopWear');
INSERT INTO `trendbro_db`.`main_category` (`id`, `name`) VALUES ('2', 'BottomWear');
INSERT INTO `trendbro_db`.`main_category` (`id`, `name`) VALUES ('3', 'FestiveWear');
INSERT INTO `trendbro_db`.`main_category` (`id`, `name`) VALUES ('4', 'FootWear');
INSERT INTO `trendbro_db`.`main_category` (`id`, `name`) VALUES ('5', 'Accessories');

INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('1', 'Watches', '5', 'Watchs.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('2', 'Braclet', '5', 'Braclet.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('3', 'Perfumes', '5', 'Perfumes.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('4', 'T-shirts', '1', 'T-shirts.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('5', 'CasualShirts', '1', 'CasualShirts.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('6', 'FormalShirts', '1', 'FormalShirts.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('7', 'SweatShirts', '1', 'SweatShirts.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('8', 'Suit', '1', 'Suit.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('9', 'LeatherJacket', '1', 'LeatherJacket.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('10', 'Sneakers', '4', 'Sneakers.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('11', 'FormalShoes', '4', 'FormalShoes.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('12', 'CasualShoes', '4', 'CasualShoes.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('13', 'Jeans', '2', 'Jeans.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('14', 'FormalTrouser', '2', 'FormalTrouser.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('15', 'CasualTrouser', '2', 'CasualTrouser.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('16', 'Kurtis & Kurta Sets', '3', 'Kurtis&KurtaSets.php');
INSERT INTO `trendbro_db`.`sub_category` (`id`, `name`, `main_category_id`, `link`) VALUES ('17', 'Sherwanis', '3', 'Sherwanis.php');















INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('3', 'Highlander Men Olive Regular Fit Solid Casual Trousers Chinos', '719', 'images/casualTrouser1.jpg', 'images/casualTrouser1_1.jpg', 'images/casualTrouser1.jpg', '15', 'M', 'HIGHLANDER');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('4', 'Men Grey Pure Cotton Trousers', '1125', 'images/casualTrouser2.jpg', 'images/casualTrouser2_1.jpg', 'images/casualTrouser2.jpg', '15', 'M', 'Roadster');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('5', 'Frontier White Geometric Shirt', '999', 'images/casualShirt4.jpg', 'images/casualShirt4_1.jpg', 'images/casualShirt4.jpg', '5', 'S', 'Snitch');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('6', 'Men Casual Shirt', '874', 'images/casualShirt2.jpg', 'images/casualShirt2.jpg', 'images/casualShirt2.jpg', '5', 'M', 'Mast & Harbour');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('7', 'Park Avenue Textured Slim Fit Formal Shirt', '1079', 'images/FormalShirts1.jpg', 'images/FormalShirts1_1.jpg', 'images/FormalShirts1.jpg', '6', 'M', 'Park Avenue');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('8', 'VAN HEUSEN Micro Print Shirt with Patch Pocket', '1274', 'images/FormalShirts2.jpg', 'images/FormalShirts2_1.jpg', 'images/FormalShirts2.jpg', '6', 'S', 'Van Heusen');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('9', 'Men Grey Pure Cotton Trousers', '2491', 'images/formalTrouser1.jpg', 'images/formalTrouser1_1.jpg', 'images/formalTrouser1.jpg', '14', 'M', 'Van Heusen');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('10', 'Bata 8216 BOSS-Grip Black Formal Derby Shoes for Men', '743', 'images/FormalShoes1.jpg', 'images/FormalShoes1_1.jpg', 'images/FormalShoes1.jpg', '11', 'M', 'Bata');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('11', 'Lather Jacket For Men Biker', '1999', 'images/leatherJacket1.jpg', 'images/leatherJacket1_1.jpg', 'images/leatherJacket1.jpg', '9', 'S', 'Wrogn');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('12', 'Highlander Men Blue Skinny Fit Clean Look Jeans', '549', 'images/Jeans1.jpg', 'images/Jeans1_2.jpg', 'images/Jeans1.jpg', '13', 'L', 'HIGHLANDER');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`) VALUES ('13', 'Majestic Man Men Cotton Mandarin Collar Ethnic Motifs Printed Long Regular Kurta', '699', 'images/Kurtis&KurtaSets1.jpg', 'images/Kurtis&KurtaSets1_1.jpg', 'images/Kurtis&KurtaSets1.jpg', '16', 'M');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `brand`) VALUES ('14', 'Park Avenue Euphoria', '321', 'images/Perfumes1.jpg', 'images/Perfumes1_1.jpg', 'images/Perfumes1.jpg', '3', 'Park Avenue');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`) VALUES ('15', 'Mens Indo-Western Sherwani, White Silk Blend', '5000', 'images/Sherwanis1.jpg', 'images/Sherwanis1_1.jpg', 'images/Sherwanis1.jpg', '17', 'M');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('16', 'Red Tape Men Memory Foam Insole Sneakers', '1679', 'images/Sneakers1.jpg', 'images/Sneakers1_2.jpg', 'images/Sneakers1.jpg', '10', 'S', 'Red Tape');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('17', 'Peter England Men Single -2 button solid formal suit', '7999', 'images/suit1.jpg', 'images/suit1_1.jpg', 'images/suit1.jpg', '8', 'L', 'Peter England');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('18', 'Loose Fit Sweatshirt', '799', 'images/SweatShirts1.jpg', 'images/SweatShirts1_1.jpg', 'images/SweatShirts1.jpg', '7', 'M', 'H&M');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `size`, `brand`) VALUES ('19', 'Fila Mens Regular Fit T-Shirt', '419', 'images/T-shirts1.jpg', 'images/T-shirts1_1.jpg', 'images/T-shirts1.jpg', '4', 'M', 'FILA');
INSERT INTO `trendbro_db`.`products` (`id`, `name`, `price`, `image1`, `image2`, `image3`, `sub_category_id`, `brand`) VALUES ('20', 'Fastrack Men Leather Straps Analogue Watch', '1017', 'images/Watchs1.jpg', 'images/Watchs1_1.jpg', 'images/Watchs1.jpg', '1', 'Fastrack');







INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('3', 'suit', 'images/suit3.jpg', 'suit', 'suit.php');
INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('4', 'Tishirts', 'images/T-shirts1.jpg', 'tshirts', 'T-shirts.php');
INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('5', 'Watches', 'images/Watchs2.jpg', 'watch', 'Watchs.php');
INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('6', 'sneakers', 'images/Sneakers2.jpg', 'sneakers', 'Sneakers.php');
INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('7', 'sherwanis', 'images/Sherwanis4.jpg', 'sherwanis', 'Sherwanis.php');
INSERT INTO `trendbro_db`.`featured_products` (`id`, `name`, `image_url`, `alt_text`, `link`) VALUES ('8', 'perfumes', 'images/Perfumes3.jpg', 'perfumes', 'Perfumes.php');
