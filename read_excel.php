<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$files = [
    'C:\Users\JAHRET\Downloads\CORREO GLOBAL 16-02-2026.xlsx',
    'C:\Users\JAHRET\Downloads\CORREO RODRIGUEZ SEPTIEMBRE (2).xlsx'
];

foreach ($files as $file) {
    echo "====================================\n";
    echo "Reading: " . basename($file) . "\n";
    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $validData = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); 
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            
            // Find an email and password in this row
            $email = null;
            $password = null;
            
            foreach ($rowData as $val) {
                if (is_string($val) && filter_var(trim($val), FILTER_VALIDATE_EMAIL)) {
                    $email = trim($val);
                } elseif (is_string($val) && strlen($val) > 4 && strlen($val) < 25 && preg_match('/[A-Za-z0-9]/', $val) && !filter_var(trim($val), FILTER_VALIDATE_URL) && !str_contains($val, '://')) {
                    // Just guess it might be password if it's not a URL and we haven't found a password yet.
                    // Actually, let's just find the index of the email and the password is the next cell.
                }
            }
            
            // Better heuristic: find the cell with email. The next non-empty cell is likely the password.
            $emailIdx = -1;
            foreach ($rowData as $idx => $val) {
                if (is_string($val) && filter_var(trim($val), FILTER_VALIDATE_EMAIL)) {
                    $emailIdx = $idx;
                    break;
                }
            }
            if ($emailIdx !== -1) {
                $pwd = $rowData[$emailIdx + 1] ?? null;
                $validData[] = ['email' => trim($rowData[$emailIdx]), 'password' => $pwd];
            }
        }
        
        echo json_encode(['count' => count($validData), 'sample' => array_slice($validData, 0, 3)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } catch (Exception $e) {
        echo "Error reading file: " . $e->getMessage() . "\n";
    }
}
