-- OPMDC database schema (users table)
-- Import with: mysql -u root -p < opmdc_db.sql

CREATE DATABASE IF NOT EXISTS opmdc DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE opmdc;

-- users table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  role ENUM('Barangay Official','OPMDC Staff','OPMDC Head','Admin') NOT NULL DEFAULT 'OPMDC Staff',
  barangayName VARCHAR(255) DEFAULT NULL,
  status ENUM('pending','approved','active','disabled') NOT NULL DEFAULT 'approved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- requests table: stores requests submitted by barangays for staff/head review
DROP TABLE IF EXISTS requests;
CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barangay VARCHAR(255) NOT NULL,
  request_type VARCHAR(255) NOT NULL,
  urgency VARCHAR(50) DEFAULT 'Medium',
  location VARCHAR(255) DEFAULT NULL,
  description TEXT,
  email VARCHAR(255) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  attachment VARCHAR(255) DEFAULT NULL,

  -- workflow fields: staff review then head approval
  -- overall status reflects position in the workflow
  status ENUM('Pending Review','Under Review','For Approval','Approved','Declined') NOT NULL DEFAULT 'Pending Review',

  -- staff review details
  review_status ENUM('Pending','Reviewed','More Info Requested') NOT NULL DEFAULT 'Pending',
  reviewed_by VARCHAR(255) DEFAULT NULL,
  review_notes TEXT DEFAULT NULL,
  reviewed_at TIMESTAMP NULL DEFAULT NULL,

  -- head approval details
  approval_status ENUM('Pending','Approved','Declined') NOT NULL DEFAULT 'Pending',
  approved_by VARCHAR(255) DEFAULT NULL,
  approval_notes TEXT DEFAULT NULL,
  approved_at TIMESTAMP NULL DEFAULT NULL,

  -- optional assignment to a user id (references `users.id` when available)
  assigned_to_user_id INT DEFAULT NULL,
  assigned_to_role VARCHAR(64) DEFAULT NULL,

  history JSON DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
)
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- notifications table: role- and user-targeted alerts streamed via SSE
DROP TABLE IF EXISTS notifications;
CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  request_id BIGINT UNSIGNED DEFAULT NULL,
  target_role VARCHAR(64) DEFAULT NULL,
  target_user_id BIGINT UNSIGNED DEFAULT NULL,
  created_by BIGINT UNSIGNED DEFAULT NULL,
  created_by_role VARCHAR(64) DEFAULT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_request_id (request_id),
  KEY idx_target_role (target_role),
  KEY idx_target_user_id (target_user_id),
  KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Helpful indexes for requests (support workflow queries)
ALTER TABLE requests
  ADD KEY idx_requests_status (status),
  ADD KEY idx_requests_review_status (review_status),
  ADD KEY idx_requests_approval_status (approval_status),
  ADD KEY idx_requests_assigned_to (assigned_to_user_id),
  ADD KEY idx_requests_created_at (created_at),
  ADD KEY idx_requests_updated_at (updated_at),
  ADD KEY idx_requests_barangay (barangay);

-- Project proposals master table (used by barangay submission flow)
DROP TABLE IF EXISTS project_proposals;
CREATE TABLE project_proposals (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  project_type VARCHAR(32) NOT NULL,
  barangay VARCHAR(255) NOT NULL,
  request_type VARCHAR(128) DEFAULT NULL,
  urgency VARCHAR(32) DEFAULT NULL,
  location VARCHAR(255) DEFAULT NULL,
  budget DECIMAL(15,2) DEFAULT NULL,
  description TEXT NOT NULL,
  attachment VARCHAR(512) DEFAULT NULL,
  thumbnail VARCHAR(512) DEFAULT NULL,
  status VARCHAR(64) DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_proposals_status (status),
  KEY idx_proposals_barangay (barangay),
  KEY idx_proposals_project_type (project_type),
  KEY idx_proposals_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Per-type routing tables (kept for backwards compatibility with existing code)
DROP TABLE IF EXISTS proposals_clup;
CREATE TABLE proposals_clup LIKE project_proposals;
DROP TABLE IF EXISTS proposals_cdp;
CREATE TABLE proposals_cdp LIKE project_proposals;
DROP TABLE IF EXISTS proposals_aip;
CREATE TABLE proposals_aip LIKE project_proposals;

-- History for proposal lifecycle events
DROP TABLE IF EXISTS proposal_history;
CREATE TABLE proposal_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proposal_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(128) NOT NULL,
  remarks TEXT DEFAULT NULL,
  user_id BIGINT UNSIGNED DEFAULT NULL,
  user_role VARCHAR(64) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_proposal_id (proposal_id),
  KEY idx_proposal_history_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Simple analytics table for proposals (counters, derived metrics)
DROP TABLE IF EXISTS proposal_analytics;
CREATE TABLE proposal_analytics (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proposal_id BIGINT UNSIGNED NOT NULL,
  metric_key VARCHAR(128) NOT NULL,
  metric_value JSON DEFAULT NULL,
  note TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_pa_proposal_id (proposal_id),
  KEY idx_pa_metric_key (metric_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Container/grouping support: define containers and mapping to proposals
DROP TABLE IF EXISTS proposal_containers;
CREATE TABLE proposal_containers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS proposal_container_map;
CREATE TABLE proposal_container_map (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proposal_id BIGINT UNSIGNED NOT NULL,
  container_id INT NOT NULL,
  assigned_by BIGINT UNSIGNED DEFAULT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_pcm_proposal_id (proposal_id),
  KEY idx_pcm_container_id (container_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

