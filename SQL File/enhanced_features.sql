-- Enhanced Features for Boat Booking System
-- Add these tables to your existing bbsdb database

-- Table for User Reviews and Ratings
CREATE TABLE `tblreviews` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BoatID` int(5) NOT NULL,
  `BookingID` int(5) DEFAULT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `CustomerEmail` varchar(255) NOT NULL,
  `Rating` int(1) NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
  `ReviewTitle` varchar(255) DEFAULT NULL,
  `ReviewText` text DEFAULT NULL,
  `ReviewDate` timestamp DEFAULT current_timestamp(),
  `IsApproved` tinyint(1) DEFAULT 0,
  `AdminResponse` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `boat_id` (`BoatID`),
  KEY `booking_id` (`BookingID`),
  FOREIGN KEY (`BoatID`) REFERENCES `tblboat`(`ID`),
  FOREIGN KEY (`BookingID`) REFERENCES `tblbookings`(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for Payment Records
CREATE TABLE `tblpayments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BookingID` int(5) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `PaymentGateway` varchar(50) DEFAULT NULL,
  `TransactionID` varchar(255) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentStatus` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `PaymentDate` timestamp DEFAULT current_timestamp(),
  `GatewayResponse` text DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `booking_id` (`BookingID`),
  FOREIGN KEY (`BookingID`) REFERENCES `tblbookings`(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for Revenue Analytics
CREATE TABLE `tblrevenue` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BookingID` int(5) NOT NULL,
  `BoatID` int(5) NOT NULL,
  `Revenue` decimal(10,2) NOT NULL,
  `Commission` decimal(10,2) DEFAULT 0.00,
  `NetRevenue` decimal(10,2) NOT NULL,
  `RevenueDate` date NOT NULL,
  `CreatedAt` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `booking_id` (`BookingID`),
  KEY `boat_id` (`BoatID`),
  KEY `revenue_date` (`RevenueDate`),
  FOREIGN KEY (`BookingID`) REFERENCES `tblbookings`(`ID`),
  FOREIGN KEY (`BoatID`) REFERENCES `tblboat`(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table for Popular Routes Analytics
CREATE TABLE `tblroute_analytics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Source` varchar(250) NOT NULL,
  `Destination` varchar(250) NOT NULL,
  `BookingCount` int(11) DEFAULT 1,
  `TotalRevenue` decimal(10,2) DEFAULT 0.00,
  `LastBookingDate` timestamp DEFAULT current_timestamp(),
  `CreatedAt` timestamp DEFAULT current_timestamp(),
  `UpdatedAt` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `route_unique` (`Source`, `Destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for testing
INSERT INTO `tblreviews` (`BoatID`, `BookingID`, `CustomerName`, `CustomerEmail`, `Rating`, `ReviewTitle`, `ReviewText`, `IsApproved`) VALUES
(1, 1, 'John Doe', 'john@example.com', 5, 'Excellent Service', 'Great boat ride experience. Highly recommended!', 1),
(2, 2, 'Jane Smith', 'jane@example.com', 4, 'Good Experience', 'Nice boat and friendly staff. Will book again.', 1);

-- Insert sample payment data
INSERT INTO `tblpayments` (`BookingID`, `PaymentMethod`, `PaymentGateway`, `TransactionID`, `Amount`, `PaymentStatus`) VALUES
(1, 'Credit Card', 'Stripe', 'txn_123456789', 110.00, 'completed'),
(2, 'PayPal', 'PayPal', 'pp_987654321', 100.00, 'completed');

-- Insert sample revenue data
INSERT INTO `tblrevenue` (`BookingID`, `BoatID`, `Revenue`, `Commission`, `NetRevenue`, `RevenueDate`) VALUES
(1, 4, 110.00, 11.00, 99.00, '2024-10-15'),
(2, 2, 100.00, 10.00, 90.00, '2024-11-01');

-- Insert sample route analytics
INSERT INTO `tblroute_analytics` (`Source`, `Destination`, `BookingCount`, `TotalRevenue`) VALUES
('Assi Ghat', 'Ramnagar', 5, 500.00),
('Varanasi', 'Allahabad', 3, 330.00),
('Assi Ghat', 'Shivala Ghat', 8, 640.00);
