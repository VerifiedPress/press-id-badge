<?php
$ksiPath = __DIR__ . '/../../fedramp/audit-artifacts/ksi-validation.json';
$jsonPath = __DIR__ . '/../../fedramp/monitoring/vuln-scan-results/dependency-check-report.json';
$today = date('Y-m-d');
$mdPath = __DIR__ . "/../../fedramp/monitoring/vuln-scan-$today.md";

if (!file_exists($jsonPath)) exit("No scan results found\n");

if (!file_exists($mdPath)) {
    file_put_contents($mdPath, "# 🛡 Vulnerability Scan Results - $today\n\n");
}

$data = json_decode(file_get_contents($jsonPath), true);
$timestamp = date('c');
$findings = $data['dependencies'] ?? [];

$counts = ['low' => 0, 'medium' => 0, 'high' => 0];
foreach ($findings as $dep) {
    foreach ($dep['vulnerabilities'] ?? [] as $vuln) {
        $severity = strtolower($vuln['severity']);
        if (isset($counts[$severity])) $counts[$severity]++;
    }
}

$entry = "\n## 📍 $timestamp\n- High: {$counts['high']}\n- Medium: {$counts['medium']}\n- Low: {$counts['low']}\n";
file_put_contents($mdPath, $entry, FILE_APPEND);

// Initialize JSON log
if (!file_exists($ksiPath)) {
    file_put_contents($ksiPath, 
        json_encode([
            "version"=> "25.04",
            "assessment_date"=> $today,
            "validation_summary"=> "Draft submission pending final 3PAO review.",
            "ksi"=> []
        ], JSON_PRETTY_PRINT)
    );
}
$data = json_decode(file_get_contents($ksiPath), true);

// Ensure 'ksi' key exists and is an array
if (!isset($data['ksi']) || !is_array($data['ksi'])) {
    $data['ksi'] = [];
}

// Construct KSI entry
$entry = [
    'id' => 'KSI-' . str_pad(count($data['ksi']), 3, '0', STR_PAD_LEFT),
    'description' => 'OWASP Dependency-Check scan results',
    'status' => 'True',
    'evidence_link' => 'https://github.com/pingleware/press-id-badge/blob/main/fedramp/monitoring/' . basename($mdPath)
];

// Append and save
array_push($data['ksi'], $entry);
file_put_contents($ksiPath, json_encode($data, JSON_PRETTY_PRINT));
?>