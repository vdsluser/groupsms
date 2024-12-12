<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['data'] ?? '';
    $lines = explode("\n", trim($input));
    $phones = [];
    
    foreach ($lines as $line) {
        if (preg_match('/\d{3}-\d{4}-\d{4}/', $line, $matches)) {
            $phones[] = $matches[0];
        }
    }
    
    $response = [
        'total' => count($phones),
        'phones' => $phones
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>