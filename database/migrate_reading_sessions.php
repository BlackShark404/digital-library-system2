<?php

// Include database configuration
require_once __DIR__ . '/../config/Database.php';

/**
 * Run the reading sessions migration
 */
function runMigration() {
    echo "Running reading sessions migration...\n";
    
    try {
        // Get the database connection
        $db = \Config\Database::getInstance()->getConnection();
        
        // Read the migration SQL file
        $migrationFile = __DIR__ . '/migration_reading_sessions.sql';
        $sql = file_get_contents($migrationFile);
        
        if (!$sql) {
            echo "Error: Could not read migration file.\n";
            return false;
        }
        
        // Execute the SQL
        $result = $db->exec($sql);
        
        echo "Migration completed successfully!\n";
        return true;
    } catch (\PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run the migration
runMigration(); 