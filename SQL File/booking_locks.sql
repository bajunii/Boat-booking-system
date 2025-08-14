-- Additional table for booking locks to prevent race conditions
-- Add this to your existing bbsdb database

CREATE TABLE `tblbooking_locks` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `boat_id` int(5) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `boat_id` (`boat_id`),
  KEY `session_id` (`session_id`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`boat_id`) REFERENCES `tblboat`(`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add booking status field if not exists (for pending payments)
ALTER TABLE `tblbookings` 
ADD COLUMN IF NOT EXISTS `BookingStatus` varchar(50) DEFAULT 'Pending' AFTER `AdminRemark`;

-- Create index for better performance on availability queries
CREATE INDEX IF NOT EXISTS `idx_booking_dates` ON `tblbookings` (`BoatID`, `BookingDateFrom`, `BookingDateTo`, `BookingStatus`);
CREATE INDEX IF NOT EXISTS `idx_booking_status` ON `tblbookings` (`BookingStatus`);
CREATE INDEX IF NOT EXISTS `idx_booking_time` ON `tblbookings` (`BookingTime`);
