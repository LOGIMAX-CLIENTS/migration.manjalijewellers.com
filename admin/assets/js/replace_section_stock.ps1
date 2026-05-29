# Extract Amman's function (L36299-36976) and adapt for etail's date picker
$ammanFile = "c:\xampp\htdocs\amman\admin\assets\js\ret_reports.js"
$etailFile = "c:\xampp\htdocs\etail_development_src\admin\assets\js\ret_reports.js"

# Read both files
$ammanLines = Get-Content $ammanFile
$etailLines = Get-Content $etailFile

# Extract Amman's function + stock_group_by handler (lines 36299-36976, 0-indexed: 36298-36975)
$ammanFunction = $ammanLines[36298..36975]

# Convert to single string for replacements
$ammanCode = $ammanFunction -join "`r`n"

# Adapt date handling: Amman uses dt_range, etail uses FromDt/ToDt
# Replace the date variable declarations
$ammanCode = $ammanCode -replace "var dt_range = \(.*?\)\.split\('-'\);", "// var dt_range - using FromDt/ToDt instead"
$ammanCode = $ammanCode -replace "var from_date = dt_range\[0\];", "var FromDt = `$('#rpt_from_date').html();"
$ammanCode = $ammanCode -replace "var to_date = dt_range\[1\];", "var ToDt = `$('#rpt_to_date').html();"

# Fix title template literal - replace dt_range references with FromDt/ToDt
$ammanCode = $ammanCode -replace "from_date, to_date,", "FromDt, ToDt,"

# Fix AJAX data payload - replace dt_range with FromDt/ToDt
$ammanCode = $ammanCode -replace "'dt_range': \`\$\(""#dt_range""\)\.val\(\),", "'FromDt': FromDt, 'ToDt': ToDt,"

# Fix excel title  
$ammanCode = $ammanCode -replace '\$\{dt_range\[0\]\}', '${FromDt}'
$ammanCode = $ammanCode -replace '\$\{dt_range\[1\]\}', '${ToDt}'

# Build new etail file: everything before L37037 + new function + everything after L38258
$before = $etailLines[0..37035]  # Lines 1-37036 (0-indexed: 0-37035)
$after = $etailLines[38258..($etailLines.Length-1)]  # Lines 38259+ (0-indexed: 38258+)

$newContent = @()
$newContent += $before
$newContent += $ammanCode
$newContent += $after

# Write result
$newContent | Set-Content $etailFile -Encoding UTF8

Write-Host "Done. Replaced etail lines 37037-38258 with Amman's L36299-36976 (adapted for FromDt/ToDt)"
Write-Host "New file has $($newContent.Length) lines (was $($etailLines.Length))"
