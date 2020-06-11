<?php 

namespace kamalamay\ValidateExcelFile;

class Validate
{
    public function greet($greet = "Kamal")
    {
        return "Hello ".$greet;
    }

    public function validateFile($filename) {
      
        try {

            $ext = ucfirst(pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext != ("Xls" || "Xlsx")) {
                $result = [
                    "status" => "error",
                    "message" => 'File format must be "xls" or "xlsx" !'
                ];
                return $result;
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($ext);
            $reader->setReadDataOnly(true);

            $worksheetData = $reader->listWorksheetInfo($filename);

            foreach ($worksheetData as $worksheet) {
                $sheetName = $worksheet['worksheetName'];
                $reader->setLoadSheetsOnly($sheetName);
                $spreadsheet = $reader->load($filename);
                $worksheet = $spreadsheet->getActiveSheet();
                $sheets[] = $worksheet->toArray();
            }

            $sheet1 = $sheets[0];
            
            $rulesHeader = $this->createRulesHeader($sheet1[0]);

            $resultValidate = [];
            for ($i = 1; $i < count($sheet1); $i++) {
                $error = "";
                for ($j = 0; $j < count($sheet1[$i]); $j++) {
                    switch ($rulesHeader[$j]){
                        case "not-space":
                            if (!$this->notSpace($sheet1[$i][$j])) {
                                $error .= preg_replace('/[^a-zA-Z0-9_.]/', '', $sheet1[0][$j]) . " should not contain any space, ";
                            };
                        break;
                        case "not-empty":
                            if (!$this->notEmpty($sheet1[$i][$j])) {
                                $error .= " Missing value in " . preg_replace('/[^a-zA-Z0-9_.]/', '', $sheet1[0][$j]) . ", ";
                            };
                        break;
                    }
                    // echo preg_replace('/[^a-zA-Z0-9_.]/', '', $sheet1[0][$j])." => ".$error;
                    // echo "<br>";
                }
                if (!empty($error)) {
                    $resultValidate[] = [
                        "row" => $i,
                        "error" => rtrim($error, ", ")
                    ];   
                }
            }

            $result = [
                "status" => "success",
                "result" => $resultValidate
            ];

            return $result;

        } catch (Exception $e) {

            $result = [
                "status" => "error",
                "message" => 'Caught exception: ',  $e->getMessage()
            ];

            return $result;

        }

    }

    private function createRulesHeader($header){
      $rules = [];
      for ($i = 0; $i < count($header); $i++){
          if (substr($header[$i], 0, 1) == "#") {
              $rules[] = "not-space";
          } else if (substr($header[$i], -1) == "*") {
              $rules[] = "not-empty";
          } else {
              $rules[] = "no-rule";
          }
      }
      return $rules;
    }
    
    private function notSpace($value) {
        $findSpace = strrpos($value," ");
        if (!empty($findSpace)){
            return false;
        }
        return true;
    }
    
    private function notEmpty($value) {
        if (empty($value)){
            return false;
        }
        return true;
    }
}