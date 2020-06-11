# validate_excel_file-2
Package for validate excel file based on header rules
## Installation
```
composer require kamalludin/validate-excel-file-2 dev-master
```
## How to use
```
Create PHP file (example: test.php)
```
### Copy paste this code
```
<?php

// Autoload files using Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

// load class Validate
use kamalludin\ValidateExcelFile\Validate;

try {
  // create object
  $validate = new Validate();

  // Enter the name of the file in the project directory
  // Example: "Type_A.xlsx"
  $filename = "Type_A.xlsx";

  // call the function and receive the result
  $resultValidate = $validate->validateFile($filename);

  // create script table for show result in table
  $tableContent = "";
  if ($resultValidate['status'] == "success") {
    if (count($resultValidate['result']) > 0) {
      $tableContent = "<tr><th>Row</th><th>Error</th></tr>";
      foreach ($resultValidate['result'] as $rowValidate) {
        $tableContent .= "<tr><td>".$rowValidate['row']."</td><td>".$rowValidate['error']."</td></tr>";
      }
    } else {
      $tableContent = "<tr><th>Row</th><th>Error</th></tr>";
    }
  } else {
    $tableContent = "<tr><th>Error Message</th><th>".$resultValidate['message']."</th></tr>";
  }
  
  // show result of validation
  echo "<table border='1px'>$tableContent</table>";
} catch (Exception $e) { // catch error
  echo 'Caught exception: ',  $e->getMessage();
}
```
