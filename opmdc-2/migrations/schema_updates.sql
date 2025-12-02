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

-- Performance indexes for authentication (speeds up username/email lookups)
ALTER TABLE `users`
  ADD INDEX `idx_users_username` (`username`),
  ADD INDEX `idx_users_email` (`email`);

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

-- Additional generated recommendations (10 CLUP, 10 CDP, 10 AIP) added on 2025-11-14
INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('CLUP','Coastal Setback & Mangrove Buffer Implementation','Formalize setback lines and mangrove buffer zones for shoreline barangays','Details: adopt setback distances based on slope and littoral profile, map existing mangrove stands, support community-led mangrove replanting and monitoring; include enforcement checklist and incentives for compliance.',0.940,'AutoGen Mabini 2025'),
('CLUP','Tourism-Compatible Zoning for Dive & Beach Areas','Zoning adjustments to protect dive sites and beach tourism assets','Details: restrict heavy commercialisation near dive points, require environmental impact screening for coastal developments, create designated low-impact tourism zones and visitor capacity limits.',0.925,'AutoGen Mabini 2025'),
('CLUP','Agricultural Protection Overlay','Protect prime agricultural land from urban conversion','Details: map prime agricultural soils, apply conversion controls, and require mitigation/compensation for any rezoning; integrate with local agribusiness support programs.',0.915,'AutoGen Mabini 2025'),
('CLUP','Upland Watershed & Ridge Conservation','Protect headwaters and steep slopes in upland barangays','Details: define conservation corridors, ban high-impact land uses on steep slopes, support reforestation and contour farming, and include landslide mitigation measures.',0.935,'AutoGen Mabini 2025'),
('CLUP','Green-Blue Network & Public Space Strategy','Connect parks, mangroves and riparian buffers into a resilient network','Details: identify corridors, prioritize acquisitions or easements, create design guidelines for pocket parks and waterfront access with environmental safeguards.',0.900,'AutoGen Mabini 2025'),
('CLUP','Affordable Housing Allocation in Mixed-Use Nodes','Designate affordable housing set-asides in mixed-use zones near services','Details: require percentage set-aside for affordable units in new mixed-use developments, offer density incentives and fast-track permits for compliant projects.',0.880,'AutoGen Mabini 2025'),
('CLUP','Port and Coastal Access Management','Protect public access to beaches and manage small boat ports','Details: map and reserve shoreline access corridors, set rules for port expansions, integrate small craft harbor design with environmental safeguards.',0.885,'AutoGen Mabini 2025'),
('CLUP','Hazard-Aware Land Use Restrictions','Integrate hazard maps into allowable land-use tables','Details: restrict housing and critical facilities in mapped high-risk flood/tsunami/landslide zones; define permissible temporary uses and relocation triggers.',0.945,'AutoGen Mabini 2025'),
('CLUP','Scenic and Cultural Heritage Overlay','Protect viewpoints, heritage sites and traditional coastal settlements','Details: designate overlays protecting historic streetscapes and coastal vistas; include signage, design guidelines and heritage tourism management.',0.870,'AutoGen Mabini 2025'),
('CLUP','Transport-Oriented Development Around Market Hubs','Plan mixed-use development around municipal markets and transport nodes','Details: prioritize pedestrian access, market logistics, vendor stalls, micro-parking and shared facilities to concentrate growth and reduce sprawl.',0.905,'AutoGen Mabini 2025');

INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('CDP','Coastal Livelihood Diversification Initiative','Support alternative livelihoods for fishing communities','Details: training, seed grants, and market linkages for seaweed farming, sustainable aquaculture, and fisheries-based value chains; includes monitoring and resource-use rules.',0.925,'AutoGen Mabini 2025'),
('CDP','Mobile Health & Preventive Services Program','Regular mobile clinics and community health outreaches','Details: schedule mobile clinics targeting remote upland and coastal barangays, include vaccination, maternal health, NCD screening and teleconsultation; define staffing and equipment needs.',0.940,'AutoGen Mabini 2025'),
('CDP','Coastal Ecotourism Capacity Building','Train local service providers for sustainable tourism','Details: workshops for homestays, guides, waste management, safety standards and visitor codes of conduct; support product packaging and marketing with DOT linkups.',0.900,'AutoGen Mabini 2025'),
('CDP','Upland Agroforestry & Soil Conservation Program','Combine tree crops and sustainable farming in mountain barangays','Details: introduce contour planting, agroforestry species, farmer cooperatives and nursery development; include erosion control and water retention measures.',0.915,'AutoGen Mabini 2025'),
('CDP','Barangay Solid Waste & Coastal Cleanup Campaign','Regular waste segregation, collection and coastal cleanups','Details: provide barangays with segregation bins, community collection schedules, litter traps at drains and tourism-area waste management plans; include education campaigns.',0.930,'AutoGen Mabini 2025'),
('CDP','Market Access & Value Chain Strengthening','Improve post-harvest handling and market linkages','Details: training on sorting/packing, cold storage partnerships, day-market scheduling and cooperative branding for seafood and agricultural produce.',0.905,'AutoGen Mabini 2025'),
('CDP','Small Tourism Infrastructure Grants','Grant fund for viewpoint decks, signage and sanitation at tourist spots','Details: micro-grants for barangays to build safe viewing platforms, trail rehabilitation, wayfinding and public toilets with maintenance plans.',0.885,'AutoGen Mabini 2025'),
('CDP','Barangay Emergency Preparedness & Equipment Provision','Equip barangays with basic rescue and response gear','Details: allocate funding for rescue boats, flotation devices, pumps, radios and community-first-aid kits paired with training sessions.',0.935,'AutoGen Mabini 2025'),
('CDP','Youth Skills & Entrepreneurship for Tourism & Agri','Local skills programs linking youth to tourism and processing jobs','Details: TESDA-accredited short courses, mentorship, startup microgrants and linkages to local investors and markets.',0.890,'AutoGen Mabini 2025'),
('CDP','Health & Sanitation Infrastructure Upgrades','Upgrade barangay clinics and water/sanitation facilities','Details: refurbish barangay health stations, provide cold chain for vaccines, upgrade water filtration and toilets in public areas.',0.945,'AutoGen Mabini 2025');

INSERT INTO `plan_recommendations` (`category`, `title`, `summary`, `details`, `relevance`, `source`) VALUES
('AIP','Construct Barangay Health Station Upgrades','Fund upgrades for basic health services and telemedicine','Details: retrofit existing barangay health stations with basic lab capacity, cold chain, and telemedicine corner; procure essential equipment and train staff.',0.950,'AutoGen Mabini 2025'),
('AIP','Coastal Protection Pilot: Living Shorelines & Mangrove Rehabilitation','Implement nature-based shoreline protection projects','Details: plant mangroves, install brushwood fences and living shoreline measures in priority erosion hotspots; include community stewardship and monitoring.',0.940,'AutoGen Mabini 2025'),
('AIP','Barangay Tourism Micro-Projects Fund','Small capital works to improve visitor safety & amenities','Details: fund boardwalks, interpretation signs, trail markers, restrooms and waste points in community-managed tourist sites.',0.900,'AutoGen Mabini 2025'),
('AIP','Access Road Upgrades & Slope Stabilization','Upgrade critical barangay access roads and stabilize slopes','Details: paving, drainage improvements and rock/soil stabilization works for priority upland routes to improve access and reduce landslide risk.',0.945,'AutoGen Mabini 2025'),
('AIP','Community Water System Rehabilitation Projects','Rehabilitate springs, storage, and distribution in water-stressed barangays','Details: spring box repair, storage tank rehabilitation, piped networks and community-based O&M arrangements.',0.935,'AutoGen Mabini 2025'),
('AIP','Waste Management & Coastal Litter Traps','Install transfer stations and litter traps to reduce marine debris','Details: construct barangay transfer points, install trap systems at major drains and fund collection logistics for tourism zones.',0.925,'AutoGen Mabini 2025'),
('AIP','Micro-Processing & Cold Storage for Fish & Farm Produce','Establish small-scale cold storage and processing hubs','Details: install community cold rooms, basic processing equipment and co-op management training to extend shelf-life and increase value.',0.915,'AutoGen Mabini 2025'),
('AIP','Barangay Disaster Equipment & Rapid Response Fund','Procure boats, pumps and equipment for immediate use','Details: allocate barangay-level rapid response kits including boats, pumps, ropes and radios paired with training and maintenance funds.',0.940,'AutoGen Mabini 2025'),
('AIP','Eco-Trail Development & Visitor Management Works','Build safe access and visitor facilities at waterfalls and viewpoints','Details: construct stairs, handrails, signage and small shelter/interpretive elements; include trail erosion control and local guide training.',0.905,'AutoGen Mabini 2025'),
('AIP','Pilot Solar Microgrids & Street Lighting','Deploy solar lighting and microgrids for remote pockets','Details: pilot solar streetlights and small community microgrids to power cold storage and public lighting; include maintenance training and payback models.',0.920,'AutoGen Mabini 2025');
