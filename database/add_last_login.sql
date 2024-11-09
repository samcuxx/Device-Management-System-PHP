-- Add last_login column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL 
COMMENT 'Tracks user last login time'
AFTER created_at;

-- Update existing users to have a default last_login value (optional)
UPDATE users 
SET last_login = created_at 
WHERE last_login IS NULL; 