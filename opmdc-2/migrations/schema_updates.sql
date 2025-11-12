-- schema_updates.sql
-- Creates the plan_recommendations table used by the Smart Recommendations module
-- IMPORTANT: Replace or extend the sample data below with your city's actual plan data.
-- NOTE: Sample rows below are written to be applicable to the municipality of Mabini, Batangas
-- and intentionally avoid referencing any specific barangay names.

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `plan_recommendations`;

CREATE TABLE `plan_recommendations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `summary` TEXT NULL,
  `details` TEXT NULL,
  `relevance` DECIMAL(5,3) NOT NULL DEFAULT 0.000,
  `source` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_plan_recommendations_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data: tailored for Mabini, Batangas (no specific barangay names included)
-- Only CLUP / CDP / AIP recommendations are included below. Other categories were removed per configuration.
INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('CLUP','Integrated Land Use Zoning for Urban Expansion','Guidelines and projects aligned with CLUP to manage urban expansion','Details: propose designated mixed-use zones, buffer areas, green corridors, and affordable housing allocation; includes mapping requirements, zoning change procedures, and phased implementation steps.',0.930,'Mabini CLUP 2024'),
('CLUP','CLUP Policy: Mixed-Use Transit-Oriented Development','Policy snippet to guide zoning for transit-oriented mixed-use areas','Details: designate mixed-use corridors along planned municipal transport routes; include floor-area ratio guidance, affordable housing set-asides, and green buffer requirements to reduce sprawl.',0.925,'Mabini CLUP 2024'),
('CLUP','CLUP Policy: Coastal Resource Protection','Policy guidance to manage coastal areas and mitigate erosion','Details: define setback requirements, conservation zones, and permissible low-impact uses; integrate soft-engineering options and community-based mangrove restoration in designated coastal strips.',0.900,'Mabini CLUP 2024'),
('CDP','Primary Health Access Points in Growth Areas','CDP-aligned projects to improve access to primary health services','Details: prioritize new primary health access points and mobile clinics in projected growth corridors identified by the CDP; includes staffing, equipment list, and phased budget for 3 years.',0.890,'Mabini CDP 2023'),
('CDP','CDP Corridor Project: Economic Growth Corridor','Corridor-level project to strengthen economic linkages identified in the CDP','Details: phased investments in access roads, drainage, market nodes and micro-enterprise support along projected growth corridors; includes timeframe, partner agencies, and monitoring indicators.',0.915,'Mabini CDP 2023'),
('CDP','CDP Mobility Corridor: Public Transport Access Enhancement','Project to improve public transport stops and connectivity along CDP-identified corridors','Details: install standardized transport stops with signage, short shelters, pedestrian ramps and safe crossing points; coordinate with transport operators and local market hours.',0.890,'Mabini CDP 2023'),
('AIP','Annual Priority: Barangay Road Maintenance (AIP)','AIP-style short-term funded projects for immediate impact','Details: yearly funded plan for maintenance and resurfacing of priority local roads identified for the fiscal year; includes procurement schedule, cost estimates, and community labor program.',0.870,'Mabini AIP 2025'),
('AIP','Annual Priority: Emergency Road Stabilization','Short-term repairs and stabilization of critical municipal roads after monsoon damage','Details: immediate grading, localized drainage repair, and surface patching for the fiscal year; includes prioritized list of segments, cost estimates, supplier shortlist, and maintenance handover plan.',0.910,'Mabini AIP 2025'),
('AIP','Annual Priority: Market Access & Drainage Works','Yearly-funded small works to improve market access and reduce waterlogging near trade areas','Details: targeted resurfacing of market approach routes, installation of modular drainage channels, vendor reorganization plan and quick procurement schedule.',0.885,'Mabini AIP 2025');

-- Additional recommendations aligned to CLUP / CDP / AIP (added)
INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('CLUP','Integrated Land Use Zoning for Urban Expansion','Guidelines and projects aligned with CLUP to manage urban expansion','Details: propose designated mixed-use zones, buffer areas, green corridors, and affordable housing allocation; includes mapping requirements, zoning change procedures, and phased implementation steps.',0.930,'CLUP 2024'),
('CDP','Primary Health Access Points in Growth Areas','CDP-aligned projects to improve access to primary health services','Details: prioritize new barangay health stations and mobile clinics in projected growth corridors identified by the CDP; includes staffing, equipment list, and phased budget for 3 years.',0.890,'CDP 2023'),
('AIP','Annual Priority: Barangay Road Maintenance (AIP)','AIP-style short-term funded projects for immediate impact','Details: yearly funded plan for maintenance and resurfacing of priority barangay roads identified for the fiscal year; includes procurement schedule, cost estimates, and community labor program.',0.870,'AIP 2025');
SET FOREIGN_KEY_CHECKS=1;

-- Notes:
-- 1) Run this file against your MySQL / MariaDB database to create the `plan_recommendations` table and populate sample rows.
-- 2) To import via command line (PowerShell):
--    mysql -u <user> -p <database_name> < migrations\\schema_updates.sql
-- 3) Edit or add rows to match your city's plans. The frontend filters by `category` exactly as selected by users.
-- 4) Consider normalizing categories to a controlled vocabulary (Infrastructure, Health, Livelihood, Security, Disaster, Environment, Education, etc.) for better matches.

-- Additional Mabini-specific sample entries (AIP priorities, CLUP policy snippets, CDP corridor projects)
INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('AIP','Annual Priority: Emergency Road Stabilization','Short-term repairs and stabilization of critical municipal roads after monsoon damage','Details: immediate grading, localized drainage repair, and surface patching for the fiscal year; includes prioritized list of segments, cost estimates, supplier shortlist, and maintenance handover plan.',0.910,'Mabini AIP 2025'),
('AIP','Annual Priority: Market Access & Drainage Works','Yearly-funded small works to improve market access and reduce waterlogging near trade areas','Details: targeted resurfacing of market approach routes, installation of modular drainage channels, vendor reorganization plan and quick procurement schedule.',0.885,'Mabini AIP 2025'),
('CLUP','CLUP Policy: Mixed-Use Transit-Oriented Development','Policy snippet to guide zoning for transit-oriented mixed-use areas','Details: designate mixed-use corridors along planned municipal transport routes; include floor-area ratio guidance, affordable housing set-asides, and green buffer requirements to reduce sprawl.',0.925,'Mabini CLUP 2024'),
('CLUP','CLUP Policy: Coastal Resource Protection','Policy guidance to manage coastal areas and mitigate erosion','Details: define setback requirements, conservation zones, and permissible low-impact uses; integrate soft-engineering options and community-based mangrove restoration in designated coastal strips.',0.900,'Mabini CLUP 2024'),
('CDP','CDP Corridor Project: Economic Growth Corridor','Corridor-level project to strengthen economic linkages identified in the CDP','Details: phased investments in access roads, drainage, market nodes and micro-enterprise support along projected growth corridors; includes timeframe, partner agencies, and monitoring indicators.',0.915,'Mabini CDP 2023'),
('CDP','CDP Mobility Corridor: Public Transport Access Enhancement','Project to improve public transport stops and connectivity along CDP-identified corridors','Details: install standardized transport stops with signage, short shelters, pedestrian ramps and safe crossing points; coordinate with transport operators and local market hours.',0.890,'Mabini CDP 2023');
