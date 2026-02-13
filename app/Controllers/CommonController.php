<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Enums\Unit;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use stdClass;

class CommonController extends BaseController
{
    use ResponseTrait;

    protected $helpers = ['form'];
    const LABEL_HEADER = array(
        'Sr No',
        'Crate Design',
        'Label',
        'Height',
        'Width',
        'Depth',
        'Units',
        'Weight (kg/lbs)',
        'Footprint (kg CO2e)'
    );
    
    const LABEL_HEADER_WIDTH = array(
        '5%',
        '20%',
        '10%',
        '10%',
        '10%',
        '10%',
        '10%',
        '10%',
        '15%'
    );

    private function getUserId() {
        $userId = 0;

        if(auth()->loggedIn()) {
            $userId = auth()->id();
        }

        return $userId;
    }

    /** Get User Id from Remember Token (Cookie Value) */
    private function getUserIdFromCookieValue() : int {

        $this->setUserAuthOrToken();

        $userId = 0;

        $userModelInstance = model('UserModel');

        if(auth()->loggedIn()) {
            $userId = auth()->id();
        } else {
            $getUserId = $userModelInstance->where('remember_token', (isset($_COOKIE[COOKIE_NAME]) ? $_COOKIE[COOKIE_NAME] : 0))->findColumn('id');
            if($getUserId) {
                $userId = $getUserId[0];
            }
        }

        return $userId;
    }

    /** Get Last Cell (Column Address) */
    private function getEndCellAddress(int $end) {
        return chr(64+$end);
    }

    /** Auto size column in Excel */
    private function setStyleFormatAutoSizeFormatExcel(&$sheet) {
        // Get the highest column and row numbers
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Convert column letters to numbers (A = 1, B = 2, ...)
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Iterate over each column
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

            // Check each cell in the column to see if it contains numeric data
            for ($row = 2; $row <= $highestRow; $row++) { // Start from row 2 assuming row 1 has headers
                $cellValue = $sheet->getCell($columnLetter . $row)->getValue();
                if (is_float($cellValue)) {
                    // If it's a float, apply a number format with 3 decimal places
                    $sheet->getStyle($columnLetter . $row)->getNumberFormat()->setFormatCode('0.000');
                } else if (is_numeric($cellValue)) {
                    // Apply number format to display the full number
                    $sheet->getStyle($columnLetter . $row)->getNumberFormat()->setFormatCode('###');
                }
            }

            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
    }

    /** Export Excel Data */
    public function exportExcel(&$resultDataList)
    {
        try
        {   
            // $spreadsheet = require APPPATH . 'sample.php';
            $iNum = 1;

            $header = [self::LABEL_HEADER];    
            
            $endCellAddress = $this->getEndCellAddress(count($header[0]));

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->setActiveSheetIndex(0);
            $sheet->fromArray($header, NULL, 'A1');
            // Optionally, set a style for the merged cells
            $sheet->getStyle("A$iNum:".$endCellAddress.$iNum."")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("A$iNum:".$endCellAddress.$iNum."")->getFont()->setBold(true);
            // $sheet->getStyle("A$iNum:".$endCellAddress.$iNum."")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('a70c35'); // Theme color

            // Set background color and text color for A1
            $sheet->getStyle("A$iNum:".$endCellAddress.$iNum."")->applyFromArray([
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'], // White text color
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'a70c35'], // Green background color
                ]
            ]);

            $rowData = [];

            $resultDataList
            ->where('data_lists.created_by', $this->getUserId())
            ->orderBy('id', 'DESC')
            ->chunk(100, static function ($data) use(&$iNum, &$rowData) {
                $rowData[] = [
                    'sr_no' => $iNum++,
                    'crate_type_name' => $data['crate_type_name'],
                    'label_name' => $data['label_name'],
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'depth' => $data['depth'],
                    'measurement_unit_name' => $data['measurement_unit_name'],
                    'weight_in_kg' => number_format($data['weight_in_kg'], 3).' '.Unit::getUnitLabel((int)$data['measurement_unit_id']),
                    'carbon_footprint' => number_format($data['carbon_footprint'], 3).' '.Unit::getUnitLabel(Unit::METRIC->value),
                ];
                // do something.
                // $data is a single row of data.
            });
           
            $sheet->fromArray($rowData, null, 'A2');
            $SecondLasttCellAddress = $this->getEndCellAddress(count($header[0])-1);
            $sheet->getStyle("$SecondLasttCellAddress:".$SecondLasttCellAddress."")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("$endCellAddress:".$endCellAddress."")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            /* $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Terms and conditions');
            $drawing->setDescription('Terms and conditions');
            $drawing->setPath(ROOTPATH.'other/termsconditions.jpg');
            $drawing->setCoordinates("A$iNum");
            $drawing->setWorksheet($spreadsheet->getActiveSheet()); */

            $sheet->getStyle("A1:".$endCellAddress.$iNum."")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], // Black border
                    ],
                ],
            ]);
            
            $spreadsheet->setActiveSheetIndex(0);

            self::setStyleFormatAutoSizeFormatExcel($sheet);

            

            $iNum+=5;
            
            if (isset($_POST['image'])) {
                // Get the base64 image data from the POST request
                $imageData = $_POST['image'];
                
                // Remove the "data:image/png;base64," part of the string
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = base64_decode($imageData);

                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing();
                $drawing->setName('Flot Chart');
                $drawing->setDescription('Flot Chart');
                $drawing->setImageResource(imagecreatefromstring($imageData)); // Use the decoded image data
                $drawing->setCoordinates("A$iNum"); // Position where the image will appear
                $drawing->setWorksheet($sheet);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            
            // Set headers for Excel output
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="invoice.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch(\Exception $e){
            exit;
        } 
    }

    /** Export PDF Data */
    public function exportPDF(&$resultDataList)
    {
        $pdf = new \TCPDF();

        $pdf->SetLeftMargin(5); // Set left margin to 10mm
        $pdf->SetRightMargin(5); // Set right margin to 20mm
        $pdf->AddPage();

        $header = self::LABEL_HEADER;
        // Example data
        $html = '';
        
        $html .= '<table border="1" cellspacing="0" cellpadding="5" style="font-size:10px;">';
        $html .= '<thead>';
            $html .= '<tr>';
            
                foreach($header as $key => $headerRow) {
                    $html .= '<th width="'.self::LABEL_HEADER_WIDTH[$key].'" bgcolor="#a70c35" color="white" align="center" style="font-weight: bold;">'.$headerRow.'</th>';
                }

            $html .= '</tr>';
        $html .= '</thead><tbody>';

        $iNum = 1;
        
        $resultDataList
        ->where('data_lists.created_by', $this->getUserId())
        ->orderBy('id', 'DESC')
        ->chunk(100, static function ($data) use(&$iNum, &$html) {
            $html .= '<tr>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[0].'"  align="center">'.($iNum++).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[1].'" >'.($data['crate_type_name']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[2].'" >'.($data['label_name']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[3].'"  align="right">'.($data['height']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[4].'"  align="right">'.($data['width']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[5].'"  align="right">'.($data['depth']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[6].'" >'.($data['measurement_unit_name']).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[7].'"  align="right">'.(number_format($data['weight_in_kg'], 3).' '.Unit::getUnitLabel((int)$data['measurement_unit_id'])).'</td>';
            $html .= '<td width="'.self::LABEL_HEADER_WIDTH[8].'"  align="right">'.(number_format($data['carbon_footprint'], 3).' '.Unit::getUnitLabel(Unit::METRIC->value)).'</td>';
            $html .= '</tr>';
        });
        
        $html .= '</tbody></table>';

        // Write content
        $pdf->writeHTML($html);

        if (isset($_POST['image'])) {
            // Get the base64 image data from the POST request
            $imageData = $_POST['image'];
            
            // Remove the "data:image/png;base64," part of the string
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = base64_decode($imageData);

            // The '@' character is used to indicate that follows an image data stream and not an image file name
            $pdf->Image('@'.$imageData, $pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth(), 0, '', '', '', false, 600, 'center', false, false, 1, 'CM');
        }

            

        // Output file
        $pdf->Output('data.pdf', 'D');
        exit;
    }

    public function getUserLists() {
        $user = model('UserModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $user->getPagination($length, $start, $search);
        
        $result = [
            "draw" => $draw,
            "recordsTotal" => $data ['recordsTotal'],
            "recordsFiltered" => $data ['recordsFiltered'],
            "data" => array_map(function($obj) {
                $newData = [];
                $newData["DT_RowId"] = $obj["DT_RowId"];
                $newData["users"] = [
                    "email" => $obj["email"],
                    "username" => $obj["username"],
                    "password" => null,
                    "first_name" => $obj["first_name"],
                    "last_name" => $obj["last_name"],
                    "created_at" => date('d-M-Y H:i:s A',strtotime($obj["created_at"]))
                ];
                
                return $newData;
            }, $data['data'])
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }

    public function deleteUser($id) {
        if ($this->request->isAJAX()) {

            if(false == ($this->request->is('delete'))) {
                return $this->respond("Failed to delete", 400);
            }

            // Delete record
            $DataListModelInstance = model('UserModel');
            $DataListModelInstance->where("HEX(AES_ENCRYPT(`auth_users`.`id`, '".ENCRYPTION_KEY."'))", $id)->delete();
            $DataListModelInstance->purgeDeleted();
            
            return $this->respond([]);  
        }
    }

    public function updateOrCreateUser() {

        $invalidFieldArr = [
            [
                "name" =>  'users.username',
                "status" => 'Invalid Request'
            ],
            [
                "name" =>  'users.email',
                "status" => 'Invalid Request'
            ],
            [
                "name" =>  'users.password',
                "status" => 'Invalid Request'
            ]
        ];

        $requestFailText = 'invalid request';
        
        if($this->request->is('patch')) {
            $requestFailText = 'failed to create';
        }

        if($this->request->is('put')) {
            $requestFailText = 'failed to update';
        } 
        
        if($this->request->is('delete')) {
            $requestFailText = 'failed to delete';
        }

        $invalidFieldKeyArr = [
            'users.username' => ["<li>Username ".$requestFailText."</li>"],
            'users.email' => ["<li>Invalid ".$requestFailText."</li>"],
            'users.password' => ["<li>Invalid ".$requestFailText."</li>"]
        ];
        
        $requestedData = $this->request->getRawInput();
        
        if(!$requestedData) {
            return $this->respond([
                "errors" => ($invalidFieldKeyArr)
            ]);
        }

        $fieldArr = [
            [
                "field_key" => 'username',
                "name" =>  'users.username',
                "status" => 'Update failed'
            ],
            [
                "field_key" => 'email',
                "name" =>  'users.email',
                "status" => 'Update failed'
            ],
            [
                "field_key" => 'password',
                "name" =>  'users.password',
                "status" => 'Update failed'
            ]
        ];

        if ($this->request->isAJAX()) {

            if(false == ($this->request->is('patch') || $this->request->is('put') || $this->request->is('delete'))) {
                return $this->respond([
                    "errors" => ($invalidFieldKeyArr)
                ], 400);
            }

            // Update record
            if ($this->request->is('delete')) {
                $requestedData = $this->request->getRawInput();

                if($requestedData) {
                    if(count($requestedData) > 0) {
                        $ids = array_map(fn($obj) => $requestedData[$obj]['DT_RowId'], array_keys($requestedData));
                        return $this->respond([
                            "errors" => $ids
                        ], 400);
                        /* $DataListModelInstance = model('UserModel');
                        $DataListModelInstance->whereIn("HEX(AES_ENCRYPT(`auth_users`.`id`, '".ENCRYPTION_KEY."'))", $ids)->delete();
                        $DataListModelInstance->purgeDeleted(); */
                    }
                }
                
                return $this->respond([]);  
            } else if($this->request->is('put')) {

                try
                {                    
                    $user_id = 0;
                    $userModalInstance = model('UserModel');

                    $id = ($requestedData['DT_RowId']);
                    $firstName = isset($requestedData['users_first_name']) ? $requestedData['users_first_name'] : null;
                    $lastName = isset($requestedData['users_last_name']) ? $requestedData['users_last_name'] : null;

                    $userId= $userModalInstance->where("HEX(AES_ENCRYPT(`auth_users`.`id`, '".ENCRYPTION_KEY."'))", $id)->findColumn("id");
                    
                    if($userId) {
                        $user_id = $userId[0];
                        
                        $rulesValidation = new \CodeIgniter\Shield\Validation\ValidationRules();
                    
                        $passwordRules            = $rulesValidation->getPasswordRules();
                        $passwordRules['rules'][] = 'strong_password[]';
                    
                        $rules = [
                            'username' => "required|is_unique[auth_users.username,id,{$user_id}]",
                            'email' => "required|max_length[254]|valid_email|is_unique[auth_identities.secret,user_id,{$user_id}]",
                            'password'         => $passwordRules,
                            'password_confirm' => $rulesValidation->getPasswordConfirmRules(),
                            'first_name' =>'required|max_length[100]|alpha_numeric_space',
                            'last_name' =>'required|max_length[100]|alpha_numeric_space',
                        ];
                        
                        $data = [
                            'username' => $requestedData['users_username'],
                            'email' => $requestedData['users_email'],
                            'password' => $requestedData['users_password'],
                            'password_confirm' => $requestedData['users_password'],
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                        ];
                
                        if (! $this->validateData($data, $rules)) {
                            $errors = $this->validator->getErrors();
                            $filteredArr = array_filter($fieldArr, fn($obj) => isset($errors[$obj['field_key']]));
                            $mappedArr = array_map(function($d) use($errors) {
                                return [$d['field_key'] = ["<li>".$errors[$d['field_key']]."</li>"]];
                            }, $filteredArr);
                            
                            return $this->respond([
                                "errors" => $mappedArr
                            ], 400);
                        }

                        // If you want to get the validated data.
                        $validData = $this->validator->getValidated();

                        $users = auth()->getProvider();
        
                        $user = $users->findById($user_id);

                        if($user) {    
                            $user->fill([
                                'username' => $validData['username'],
                                'email' => $validData['email'],
                                'password' => $validData['password'],
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                            ]);
                            
                            $users->save($user);
    
                            $userRecord = $userModalInstance->getRecordByEncryptedId($id);
                            $newData = new stdClass;
                            $newData->DT_RowId = $userRecord["DT_RowId"];
                            $newData->users = [
                                "email" => $userRecord["email"],
                                "username" => $userRecord["username"],
                                "password" => null,
                                "first_name" => $userRecord["first_name"],
                                "last_name" => $userRecord["last_name"],
                                "created_at" => date('d-M-Y H:i:s A',strtotime($userRecord["created_at"])),
                            ];

                            return $this->respond($newData);
                        } else {
                            return $this->respond([
                                "errors" => $invalidFieldKeyArr
                            ], 400);
                        }

                    }
                } catch (\Exception $e) {
                    return $this->respond([
                        "errors" => $invalidFieldKeyArr
                    ], 400);
                }
            } else if($this->request->is('patch')) {
                /** Create New Record */
                try
                {
                    $firstName = isset($requestedData['users_first_name']) ? $requestedData['users_first_name'] : null;
                    $lastName = isset($requestedData['users_last_name']) ? $requestedData['users_last_name'] : null;

                    $data = [
                        'username' => $requestedData['users_username'],
                        'email' => $requestedData['users_email'],
                        'password' => $requestedData['users_password'],
                        'password_confirm' => $requestedData['users_password'],
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ];
                    
                    $rulesValidation = new \CodeIgniter\Shield\Validation\ValidationRules();
                    
                    $rules = $rulesValidation->getRegistrationRules();
                    
                    if (! $this->validateData($data, [...$rules, 
                            'first_name' => [
                                'rules' => 'required|max_length[100]|alpha_numeric_space',
                            ],
                            'last_name' => [
                                'rules' => 'required|max_length[100]|alpha_numeric_space',
                            ]
                        ], [])) {
                        $errors = $this->validator->getErrors();
                        $filteredArr = array_filter($fieldArr, fn($obj) => isset($errors[$obj['field_key']]));
                        $mappedArr = array_map(function($d) use($errors) {
                            return [$d['field_key'] = ["<li>".$errors[$d['field_key']]."</li>"]];
                        }, $filteredArr);
                        
                        return $this->respond([
                            "errors" => $mappedArr
                        ], 400);
                    }
                    
                    // If you want to get the validated data.
                    $validData = $this->validator->getValidated();
                    
                    $user = new \CodeIgniter\Shield\Entities\User([
                        'username' => $validData['username'],
                        'email' => $validData['email'],
                        'password' => $validData['password'],
                        'password_confirm' => $validData['password'],
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]);

                    $users = auth()->getProvider();
            
                    $users->save($user);

                    $userRecord = $users->findById($users->getInsertID());

                    return $this->respond($userRecord);
                } catch (\Exception $e) {
                    return $this->respond([
                        "errors" => array_values($invalidFieldArr),
                        "extra" => array_values($requestedData),
                        "message" => $e->getMessage()
                    ], 400);
                }
            }
        }

        return $this->respond([
            "errors" => array_values($invalidFieldArr)
        ]);
        
    }
    
    // public function updateOrCreateUserEditor() {

    //     $invalidFieldArr = [
    //         [
    //             "name" =>  'users.username',
    //             "status" => 'Invalid Request'
    //         ],
    //         [
    //             "name" =>  'users.email',
    //             "status" => 'Invalid Request'
    //         ],
    //         [
    //             "name" =>  'users.password',
    //             "status" => 'Invalid Request'
    //         ]
    //     ];

        
    //     $requestedData = $this->request->getRawInput();

    //     if(!$requestedData) {
    //         return $this->respond([
    //             "fieldErrors" => array_values($invalidFieldArr)
    //         ]);
    //     }
        
    //     if (false == (isset($requestedData['data']) && isset($requestedData['action']))) {
    //         return $this->respond([
    //             "fieldErrors" => array_values($invalidFieldArr),
    //         ]);
    //     }

    //     $fieldArr = [
    //         [
    //             "field_key" => 'username',
    //             "name" =>  'users.username',
    //             "status" => 'Update failed'
    //         ],
    //         [
    //             "field_key" => 'email',
    //             "name" =>  'users.email',
    //             "status" => 'Update failed'
    //         ],
    //         [
    //             "field_key" => 'password',
    //             "name" =>  'users.password',
    //             "status" => 'Update failed'
    //         ]
    //     ];

    //     if ($this->request->isAJAX()) {

    //         if(false == ($this->request->is('patch') || $this->request->is('put') || $this->request->is('delete'))) {
    //             return $this->respond([
    //                 "fieldErrors" => array_values($invalidFieldArr)
    //             ]);
    //         }

    //         // Update record
    //         if ($this->request->is('delete')) {
    //             $requestedData = $this->request->getRawInput();

    //             if($requestedData) {
    //                 if(isset($requestedData['data']) && isset($requestedData['action'])) {
    //                     if(count($requestedData['data']) > 0) {
    //                         $ids = array_map(fn($obj) => $requestedData['data'][$obj]['DT_RowId'], array_keys($requestedData['data']));
                            
    //                         $DataListModelInstance = model('UserModel');
    //                         $DataListModelInstance->whereIn("HEX(AES_ENCRYPT(`auth_users`.`id`, '".ENCRYPTION_KEY."'))", $ids)->delete();
    //                         $DataListModelInstance->purgeDeleted();
    //                     }
    //                 }
    //             }
                
    //             return $this->respond(['data' => []]);  
    //         } else if($this->request->is('put')) {

    //             try
    //             {                    
    //                 $user_id = 0;
    //                 $userModalInstance = model('UserModel');

    //                 $id = key($requestedData['data']);

    //                 $userId= $userModalInstance->where("HEX(AES_ENCRYPT(`auth_users`.`id`, '".ENCRYPTION_KEY."'))", $id)->findColumn("id");
                    
    //                 if($userId) {
    //                     $user_id = $userId[0];
    //                     $rules = [
    //                         'username' => "required|is_unique[auth_users.username,id,{$user_id}]",
    //                         'email' => "required|max_length[254]|valid_email|is_unique[auth_identities.secret,user_id,{$user_id}]",
    //                         'password' => 'required|min_length[10]',
    //                     ];
                        
    //                     $data = array_intersect_key(array_values($requestedData['data'][$id])[0], $rules);
                
    //                     if (! $this->validateData($data, $rules)) {
    //                         $errors = $this->validator->getErrors();
    //                         $filteredArr = array_filter($fieldArr, fn($obj) => isset($errors[$obj['field_key']]));
    //                         $mappedArr = array_map(function($d) use($errors, $user_id) {
    //                             $d['status'] = $errors[$d['field_key']];
    //                             $d['user_id'] = $user_id;
    //                             return $d;
    //                         }, $filteredArr);
                            
    //                         return $this->respond([
    //                             "fieldErrors" => array_values($mappedArr),
    //                         ]);
    //                     }

    //                     // If you want to get the validated data.
    //                     $validData = $this->validator->getValidated();

    //                     $users = auth()->getProvider();
        
    //                     $user = $users->findById($user_id);

    //                     $user->fill([
    //                         'username' => $validData['username'],
    //                         'email' => $validData['email'],
    //                         'password' => $validData['password']
    //                     ]);

    //                     $users->save($user);

    //                     $userRecord = $userModalInstance->getRecordByEncryptedId($id);
                        
    //                     return $this->respond([
    //                         "data" => array_map(function($obj) {
    //                             $newData = [];
    //                             $newData["DT_RowId"] = $obj["DT_RowId"];
    //                             $newData["users"] = [
    //                                 "email" => $obj["email"],
    //                                 "username" => $obj["username"],
    //                                 "password" => null,
    //                                 "created_at" => date('d-M-Y H:i:s A',strtotime($obj["created_at"]))
    //                             ];
                                
    //                             return $newData;
    //                         }, $userRecord)
                             
    //                     ]);
    //                 }
    //             } catch (\Exception $e) {
    //             }
    //         } else if($this->request->is('patch')) {
    //             /** Create New Record */
    //             try
    //             {
    //                 $rules = [
    //                     'username' => 'required|is_unique[auth_users.username]',
    //                     'email' => 'required|max_length[254]|valid_email|is_unique[auth_identities.secret]',
    //                     'password' => 'required|min_length[10]',
    //                 ];
            
    //                 $data = array_intersect_key(array_values($requestedData['data'][0])[0], $rules);
            
    //                 if (! $this->validateData($data, $rules)) {
    //                     $errors = $this->validator->getErrors();
    //                     $filteredArr = array_filter($fieldArr, fn($obj) => isset($errors[$obj['field_key']]));
    //                     $mappedArr = array_map(function($d) use($errors) {
    //                         $d['status'] = $errors[$d['field_key']];
    //                         return $d;
    //                     }, $filteredArr);
                        
    //                     return $this->respond([
    //                         "fieldErrors" => array_values($mappedArr),
    //                     ]);
    //                 }
    
    //                 $userModalInstance = model('UserModel');

    //                 // If you want to get the validated data.
    //                 $validData = $this->validator->getValidated();

    //                 $users = auth()->getProvider();
            
    //                 $user = new \CodeIgniter\Shield\Entities\User([
    //                     'username' => $validData['username'],
    //                     'email' => $validData['email'],
    //                     'password' => $validData['password']
    //                 ]);
                    
    //                 $users->save($user);

    //                 $userRecord = $userModalInstance->getRecordById($users->getInsertID());
                    
    //                 return $this->respond([
    //                     "data" => array_map(function($obj) {
    //                         $newData = [];
    //                         $newData["DT_RowId"] = $obj["DT_RowId"];
    //                         $newData["users"] = [
    //                             "email" => $obj["email"],
    //                             "username" => $obj["username"],
    //                             "password" => null,
    //                             "created_at" => date('d-M-Y H:i:s A',strtotime($obj["created_at"]))
    //                         ];
                            
    //                         return $newData;
    //                     }, $userRecord)
                            
    //                 ]);
    //             } catch (\Exception $e) {
    //                 return $this->respond([
    //                     "fieldErrors" => array_values($invalidFieldArr),
    //                     "extra" => array_values($requestedData['data'][0]),
    //                     "message" => $e->getMessage()
    //                 ]);
    //             }
    //         }
    //     }

    //     return $this->respond([
    //         "fieldErrors" => array_values($invalidFieldArr)
    //     ]);
    // }

    public function getModelOptions() {
        $modelOptions = model('ModelOptionModel')->select('id, name as text')->findAll();
        return $this->respond($modelOptions, 200);
    }

    public function getCaretDesigns() {
        $crateDesigns = model('CrateTypeModel')->select('id, name as text')->findAll();
        return $this->respond($crateDesigns, 200);
    }

    private function getValuesFromMaterialQuantityByCrateDesign($crateDesignId) : array 
    {
        $weightOfCrate = 0;
        $embodiedCarbonFactorInKg = 0;

        $materialByQuantityIds = model('MaterialByCrateDesignModel')->where('crate_type_id', $crateDesignId)->findColumn('material_by_quantity_id');
                
        if($materialByQuantityIds) {

            $materialByQuantityWeight = model('MaterialByQuantityModel')->whereIn('crate_design_value', $materialByQuantityIds)->selectSum('weight_in_kg')->first();
            
            if($materialByQuantityWeight) {
                $weightOfCrate = $materialByQuantityWeight['weight_in_kg'];
            }

            $materialByQuantityEmbodiedCarbonFactorInKg = model('MaterialByQuantityModel')->whereIn('crate_design_value', $materialByQuantityIds)->selectSum('embodied_carbon_factor_in_kg')->first();
            
            if($materialByQuantityEmbodiedCarbonFactorInKg) {
                $embodiedCarbonFactorInKg = $materialByQuantityEmbodiedCarbonFactorInKg['embodied_carbon_factor_in_kg'];
            }
        }

        return [$weightOfCrate, $embodiedCarbonFactorInKg];
    }

    private function getValuesFromCrateDesginByCrateDesign($crateDesignId) : array 
    {
        $weightOfCrate = 0;
        $embodiedCarbonFactorInKg = 0;

        $materialByCrateDesignWeight = model('MaterialByCrateDesignModel')->where('crate_type_id', $crateDesignId)->selectSum('weight_in_kg')->first();

        if($materialByCrateDesignWeight) {
            $weightOfCrate = $materialByCrateDesignWeight['weight_in_kg'];
        }

        $materialByCrateDesignEmbodiedCarbonFactorInKg = model('MaterialByCrateDesignModel')->where('crate_type_id', $crateDesignId)->selectSum('embodied_carbon_factor_in_kg')->first();

        if($materialByCrateDesignWeight) {
            $embodiedCarbonFactorInKg = $materialByCrateDesignEmbodiedCarbonFactorInKg['embodied_carbon_factor_in_kg'];
        }

        return [$weightOfCrate, $embodiedCarbonFactorInKg];
    }

    public function getCaretWeight($modalOptionId, $crateDesignId) {
        $weightOfCrate = 0;
        $embodiedCarbonFactorInKg = 0;

        $crateDesignIds = model('CrateTypeModel')->findColumn('id');

        if($crateDesignIds) {

            if($modalOptionId == "1" && in_array($crateDesignId, $crateDesignIds)) {
                list($weightOfCrate, $embodiedCarbonFactorInKg) = $this->getValuesFromMaterialQuantityByCrateDesign($crateDesignId);
            }
    
            if($modalOptionId == "2" && in_array($crateDesignId, $crateDesignIds)) {
                list($weightOfCrate, $embodiedCarbonFactorInKg) = $this->getValuesFromCrateDesginByCrateDesign($crateDesignId);
            }
        }

        return $this->respond([
            'weight_in_kg' => round($weightOfCrate), 
            'embodied_carbon_factor_in_kg' => round($embodiedCarbonFactorInKg)
        ], 200);
    }

    public function getValuesByCrateType() 
    { 
        $weightOfCrate = 0.00;
        $embodiedCarbonFactorInKg = 0.00;

        $crateTypeId = (int)$this->request->getPost('crate_type_id');
        $unitId = (int)$this->request->getPost('measurement_unit_id');
        $heightValue = (float)$this->request->getPost('height');
        $widthValue = (float)$this->request->getPost('width');
        $depthValue = (float)$this->request->getPost('depth');
        
        $crateTypeIds = model('CrateTypeModel')->findColumn('id');

        if($crateTypeIds) {
    
            if(in_array($crateTypeId, $crateTypeIds)) {
                list($weightOfCrate, $embodiedCarbonFactorInKg) = $this->getCalculatedValues($crateTypeId, $unitId, $heightValue, $widthValue, $depthValue);
            }
        }

        return $this->respond([
            'weight_in_kg' => round($weightOfCrate), 
            'embodied_carbon_factor_in_kg' => round($embodiedCarbonFactorInKg)
        ], 200);
    }

    public function carbonFootPrintValue($airPassenger, $airFreight, $roadFreight, $seaFreight, $weightOfCrate) {

        $carbonFootPrintPerOneWayTrip = 0;
        $transportationEmissionFactors = model('TransportationEmissionFactorModel')->select('shipment,carbon_emission')->findAll();

        if($transportationEmissionFactors) {
            foreach($transportationEmissionFactors as $transportationEmissionFactor) {
                if($transportationEmissionFactor['shipment'] == "Air-passenger") {
                    $carbonFootPrintPerOneWayTrip += $airPassenger*$transportationEmissionFactor['carbon_emission'];
                } else if($transportationEmissionFactor['shipment'] == "Air-freight") {
                    $carbonFootPrintPerOneWayTrip += $weightOfCrate*$airFreight*$transportationEmissionFactor['carbon_emission'];
                } else if($transportationEmissionFactor['shipment'] == "Road-freight") {
                    $carbonFootPrintPerOneWayTrip += ($weightOfCrate*$roadFreight*$transportationEmissionFactor['carbon_emission']+($roadFreight>0 ? $weightOfCrate*0.05 : 0));
                } else if($transportationEmissionFactor['shipment'] == "Sea-freight") {
                    $carbonFootPrintPerOneWayTrip += ($weightOfCrate*$seaFreight*$transportationEmissionFactor['carbon_emission']+($seaFreight>0 ? $weightOfCrate*0.05 : 0));
                } else {
                    $carbonFootPrintPerOneWayTrip += 0;
                }
            }
        }

        return $this->respond([
            'carbon_foot_print_value' => round($carbonFootPrintPerOneWayTrip/1000)
        ]);
    }

    public function saveCalculations() {

        $response = [
            'success' => false,
            'message' => 'Failed to save data'
        ];

        if($this->request->isAJAX()) {
            $crateTypeName = '';
            $modelName = '';
            
            $modelOptionName = model('ModelOptionModel')->where('id', $this->request->getPost('model_option_id'))->findColumn('name');

            if($modelOptionName) {
                $modelName = $modelOptionName[0];
            }

            $crateDesignName = model('CrateTypeModel')->where('id', $this->request->getPost('crate_type_id'))->findColumn('name');

            if($crateDesignName) {
                $crateTypeName = $crateDesignName[0];
            }

            $data = [
                'carbon_footer_print' => $this->request->getPost('carbon_footer_print'),
                'crate_type_id' => $this->request->getPost('crate_type_id'),
                'crate_type_name' => $crateTypeName,
                'crate_weight' => $this->request->getPost('crate_weight'),
                'embodied_carbon_factor_in_kg' => $this->request->getPost('embodied_carbon_factor_in_kg'),
                'model_option_id' => $this->request->getPost('model_option_id'),
                'model_option_name' => $modelName,
                'number_of_oneway_trips' => $this->request->getPost('number_of_oneway_trips'),
                'per_oneway_trip' => $this->request->getPost('per_oneway_trip'),
                'shipment_air_passenger' => $this->request->getPost('shipment')[0],
                'shipment_air_freight' => $this->request->getPost('shipment')[1],
                'shipment_road_freight' => $this->request->getPost('shipment')[2],
                'shipment_sea_freight' => $this->request->getPost('shipment')[3],
                'total_carbon_foot_print' => $this->request->getPost('total_carbon_foot_print'),
                'created_by' => session()->get('user_id')
            ];
            
            $carbonFootPrintModelInstance = model('CarbonFootPrint');
            $carbonFootPrintModelInstance->insert($data);

            $response = [
                'success' => true,
                'message' => 'Data saved successfully'
            ];
            
        }

        return $this->respond($response, 200);
    }
    
    public function saveNewCalculations() {

        $response = [
            'success' => false,
            'message' => 'Failed to save data'
        ];

        if($this->request->isAJAX()) {

            // return $this->respond(['data' => []]);
            $fieldArr = [
                [
                    "name" =>  'measurement_unit_id',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'height',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'width',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'depth',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'crate_type_id',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'label_name',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'weight_in_kg',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'embodied_carbon_factor_in_kg',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'number_of_oneway_trips',
                    "status" => 'Update failed'
                ],
                [
                    "name" =>  'per_one_way_trip',
                    "status" => 'Update failed'
                ],
            ];
            
            $rules = [
                'measurement_unit_id' => 'required',
                'height' => 'required',
                'width' => 'required',
                'depth' => 'required',
                'crate_type_id' => 'required',
                'label_name' => 'required',
                'weight_in_kg' => 'required',
                'embodied_carbon_factor_in_kg' => 'required',
                'number_of_oneway_trips' => 'required',
                'per_one_way_trip' => 'required',
            ];
    
            $data = array_intersect_key(array_values($this->request->getPost('data'))[0], $rules);
    
            if (! $this->validateData($data, $rules)) {
                $errors = $this->validator->getErrors();
                $filteredArr = array_filter($fieldArr, fn($obj) => isset($errors[$obj['name']]));
                
                return $this->respond([
                    "fieldErrors" => array_values($filteredArr),
                ]);
            }
            
            $userId = $this->getUserId();

            if(!$userId) {
                return $this->respond([
                    "fieldErrors" => array_values($fieldArr),
                ]);
            }

            // If you want to get the validated data.
            $validData = $this->validator->getValidated();

            $crateTypeName = '';

            $crateDesignName = model('CrateTypeModel')->where('id', $validData['crate_type_id'])->findColumn('name');

            if($crateDesignName) {
                $crateTypeName = $crateDesignName[0];
            }

            $data = [
                'crate_type_id' => $validData['crate_type_id'],
                'crate_type_name' => $crateTypeName,
                'label_name' => $validData['label_name'],
                'height' => $validData['height'],
                'width' => $validData['width'],
                'depth' => $validData['depth'],
                'measurement_unit_id' => $validData['measurement_unit_id'],
                'measurement_unit_name' => Unit::tryFrom($validData['measurement_unit_id'])->name,
                'weight_in_kg' => $validData['weight_in_kg'],
                'carbon_footprint' => $validData['per_one_way_trip'],
                'created_by' => $userId
            ];
            
            $dataListInstance = model('DataList');
            $dataListInstance->insert($data);

            $response = [
                'success' => true,
                'message' => 'Data saved successfully'
            ];

            return $this->respond($response);
        }

        return $this->respond($response, 200);
    }

    /** List all data of user */
    public function allDataList() 
    {
        $resultDataList = model('DataList');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        $isExport = $this->request->getPost('is_export');

        if($isExport == 1) {
            return $this->exportExcel($resultDataList);
            exit;
        }

        if($isExport == 2) {
            echo $this->exportPDF($resultDataList);
            exit;
        }
        

        // Fetch the data
        $data = $resultDataList->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            "totalSum" => array_sum(array_column($data['data'], 'carbon_footprint')),
            "ECONOMY" => array_sum(array_column(array_filter($data['data'], fn($obj) => $obj['crate_type_name'] == "ECONOMY"), 'carbon_footprint')),
            "MID-RANGE" => array_sum(array_column(array_filter($data['data'], fn($obj) => $obj['crate_type_name'] == "MID-RANGE"), 'carbon_footprint')),
            "MUSEUM" => array_sum(array_column(array_filter($data['data'], fn($obj) => $obj['crate_type_name'] == "MUSEUM"), 'carbon_footprint')),
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }

    /** Remove By Crate */
    public function deleteDataListRecord() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(isset($requestedData['deleteIds'])) {
                if(count($requestedData['deleteIds']) > 0) {
                    $ids = array_values($requestedData['deleteIds']);
                    
                    $DataListModelInstance = model('DataList');
                    $DataListModelInstance->whereIn("HEX(AES_ENCRYPT(`data_lists`.`id`, '".ENCRYPTION_KEY."'))", $ids)
                    ->where('created_by', $this->getUserId())->delete();
                    $DataListModelInstance->purgeDeleted();
                }
            }
        }
        
        return $this->respond(['data' => []]);  
    }
    
    /** Clear List*/
    public function deleteAllDataListRecord() 
    {
        $DataListModelInstance = model('DataList');
        $DataListModelInstance->where('data_lists.created_by', $this->getUserId())->delete();
        $DataListModelInstance->purgeDeleted();
        
        return $this->respond(['data' => []]);  
    }

    public function calculationListData() 
    {
        $carbonFootPrints = model('CarbonFootPrint');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $carbonFootPrints->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
    
    public function calculationListDataDetails($id) 
    {
        $carbonFootPrints = model('CarbonFootPrint');

        // Fetch detailed data for a specific user
        $details = $carbonFootPrints->getDetails($id);

        return $this->response->setJSON($details);  // Return details as JSON
    }
    
    public function materialQuantityListData() 
    {
        $materialByQuantities = model('MaterialByQuantityModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $materialByQuantities->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
    
    public function materialQuantityListDataDetails($id) 
    {
        $materialByQuantities = model('MaterialByQuantityModel');
        
        // Fetch detailed data for a specific user
        $details = $materialByQuantities->getDetails($id);
        
        return $this->response->setJSON($details);  // Return details as JSON
    }

    public function crateDesignListData() 
    {
        $materialByCrateDesign = model('MaterialByCrateDesignModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $materialByCrateDesign->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
   
    public function designRegressionListData() 
    {
        $materialByCrateDesign = model('DesignRegressionModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $materialByCrateDesign->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
    
    public function tranportationEmissionFactorListData() 
    {
        $tranportationEmissionFactor = model('TransportationEmissionFactorModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $tranportationEmissionFactor->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
    
    public function crateTypeListData() 
    {
        $crateType = model('CrateTypeModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $crateType->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }
    
    public function modelOptionListData() 
    {
        $modelOption = model('ModelOptionModel');
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('searchVal');

        // Fetch the data
        $data = $modelOption->getPagination($length, $start, $search);

        $result = [
            "draw" => $draw,
            ...$data
        ];

        return $this->response->setJSON($result);  // Return data in JSON format for DataTables
    }

    public function crateTypePrimarySave() 
    {
        $response = [
            'success' => false,
            'message' => 'Failed to save data'
        ];

        if($this->request->isAJAX()) {
            $crateTypeModelInstance = model('CrateTypeModel');
            $crateTypeModelInstance->where('1=1')->set(['is_primary' => 0])->update();
            model('CrateTypeModel')->update($this->request->getPost('crate_type_id'), ['is_primary' => 1]);

            $response = [
                'data' => $this->request->getPost('crate_type_id'),
                'success' => true,
                'message' => 'Data saved successfully'
            ];
        }

        return $this->respond($response, 200);
    }

    public function materialQuantityUpdateData() 
    {
        
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(count($requestedData) > 0) {
                $id = ($requestedData['DT_RowId']);
                $updateData = [
                    'weight_in_kg' => $requestedData['weight_in_kg'],
                    'embodied_carbon_factor_unit_per_kg' => $requestedData['embodied_carbon_factor_unit_per_kg'],
                    'embodied_carbon_factor_in_kg' => round((float)($requestedData['weight_in_kg']) * (float)($requestedData['embodied_carbon_factor_unit_per_kg']), 3),
                    'data_source' => $requestedData['data_source'],
                    'site_link' => $requestedData['site_link'],
                    'notes' => $requestedData['notes']
                ];

                $MaterialByQuantityModelInstance = model('MaterialByQuantityModel');

                if($MaterialByQuantityModelInstance->where("HEX(AES_ENCRYPT(`material_by_quantities`.`id`, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                    return $this->respond($MaterialByQuantityModelInstance->getRecordByEncryptedId($id));
                }
                
            }
        }

        return $this->respond([
            "errors" => [
                "weight_in_kg" => ["<li>Weight update fail</li>"],
                "embodied_carbon_factor_unit_per_kg" => ["<li>Embodied Carbon Factor Unit Per Kg update fail</li>"],
                "embodied_carbon_factor_in_kg" => ["<li>Embodied Carbon Factor In Kg update fail</li>"]
            ]
        ], 400);
    }
    
    public function materialQuantityUpdateDataEditor() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(isset($requestedData['data']) && isset($requestedData['action'])) {
                if(count($requestedData['data']) == 1) {
                    $id = key($requestedData['data']);
                    $updateData = [
                        'weight_in_kg' => $requestedData['data'][$id]['weight_in_kg'],
                        'embodied_carbon_factor_unit_per_kg' => $requestedData['data'][$id]['embodied_carbon_factor_unit_per_kg'],
                        'embodied_carbon_factor_in_kg' => round((float)($requestedData['data'][$id]['weight_in_kg']) * (float)($requestedData['data'][$id]['embodied_carbon_factor_unit_per_kg']), 3),
                        'data_source' => $requestedData['data'][$id]['data_source'],
                        'site_link' => $requestedData['data'][$id]['site_link'],
                        'notes' => $requestedData['data'][$id]['notes']
                    ];

                    $MaterialByQuantityModelInstance = model('MaterialByQuantityModel');

                    if($MaterialByQuantityModelInstance->where("HEX(AES_ENCRYPT(`material_by_quantities`.`id`, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                        return $this->respond(['data' => $MaterialByQuantityModelInstance->getRecordByEncryptedId($id)]);
                    }
                    return $this->respond([
                        "fieldErrors" => [
                            [
                                "name" =>   "weight_in_kg",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "embodied_carbon_factor_unit_per_kg",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "embodied_carbon_factor_in_kg",
                                "status" => "update fail"
                            ]
                        ]
                    ]);
                }
            }
        }

        return $this->respond(['data' => [], 'request' => $requestedData]);    
    }
    
    public function designRegressionUpdateData() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(count($requestedData) > 0) {
                $id = ($requestedData['DT_RowId']);
                $updateData = [
                    'calculate_m' => $requestedData['calculate_m'],
                    'calculate_b' => $requestedData['calculate_b']
                ];

                $DesignRegressionModelInstance = model('DesignRegressionModel');

                if($DesignRegressionModelInstance->where("HEX(AES_ENCRYPT(design_regressions.id, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                    return $this->respond($DesignRegressionModelInstance->getRecordByEncryptedId($id));
                }
            }
        }

        return $this->respond([
            "errors" => [
                "calculate_m" => ["<li>M value update fail</li>"],
                "calculate_b" => ["<li>B value update fail</li>"]
            ]
        ], 400);
    }
    
    public function designRegressionUpdateDataEditor() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(isset($requestedData['data']) && isset($requestedData['action'])) {
                if(count($requestedData['data']) == 1) {
                    $id = key($requestedData['data']);
                    $updateData = [
                        'calculate_m' => $requestedData['data'][$id]['calculate_m'],
                        'calculate_b' => $requestedData['data'][$id]['calculate_b']
                    ];

                    $DesignRegressionModelInstance = model('DesignRegressionModel');

                    if($DesignRegressionModelInstance->where("HEX(AES_ENCRYPT(design_regressions.id, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                        return $this->respond(['data' => $DesignRegressionModelInstance->getRecordByEncryptedId($id)]);
                    }
                    return $this->respond([
                        "fieldErrors" => [
                            [
                                "name" =>   "calculate_m",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "calculate_b",
                                "status" => "update fail"
                            ]
                        ]
                    ]);
                }
            }
        }

        return $this->respond(['data' => []]);      
    }
    
    public function tranportationEmissionFactorUpdateData() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(count($requestedData) > 0) {
                $id = ($requestedData['DT_RowId']);
                $updateData = [
                    'carbon_emission' => $requestedData['carbon_emission'],
                    'data_source' => $requestedData['data_source'],
                    'site_link' => $requestedData['site_link'],
                    'notes' => $requestedData['notes']
                ];

                $TransportationEmissionFactorModelInstance = model('TransportationEmissionFactorModel');

                if($TransportationEmissionFactorModelInstance->where("HEX(AES_ENCRYPT(transportation_emission_factors.id, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                    return $this->respond($TransportationEmissionFactorModelInstance->getRecordByEncryptedId($id));
                }
            }

            return $this->respond([
                "errors" => [
                    "carbon_emission" => ["<li>Carbon Emission value update fail</li>"],
                    "data_source" => ["<li>Data Source value update fail</li>"],
                    "site_link" => ["<li>Site Link value update fail</li>"],
                    "notes" => ["<li>Notes value update fail</li>"]
                ]
            ], 400);
        }
        
        return $this->respond(['data' => []]);  
    }
    
    public function tranportationEmissionFactorUpdateDataEditor() 
    {
        $requestedData = $this->request->getRawInput();

        if($requestedData) {
            if(isset($requestedData['data']) && isset($requestedData['action'])) {
                if(count($requestedData['data']) == 1) {
                    $id = key($requestedData['data']);
                    $updateData = [
                        'carbon_emission' => $requestedData['data'][$id]['carbon_emission'],
                        'data_source' => $requestedData['data'][$id]['data_source'],
                        'site_link' => $requestedData['data'][$id]['site_link'],
                        'notes' => $requestedData['data'][$id]['notes']
                    ];

                    $TransportationEmissionFactorModelInstance = model('TransportationEmissionFactorModel');

                    if($TransportationEmissionFactorModelInstance->where("HEX(AES_ENCRYPT(transportation_emission_factors.id, '".ENCRYPTION_KEY."'))", $id)->set($updateData)->update()) {
                        return $this->respond(['data' => $TransportationEmissionFactorModelInstance->getRecordByEncryptedId($id)]);
                    }
                    return $this->respond([
                        "fieldErrors" => [
                            [
                                "name" =>   "carbon_emission",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "data_source",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "site_link",
                                "status" => "update fail"
                            ],
                            [
                                "name" =>   "notes",
                                "status" => "update fail"
                            ]
                        ]
                    ]);
                }
            }
        }
        
        return $this->respond(['data' => []]);  
    }

    public function getDimensionValues(int $createTypeId, int $unitId, int|float $heightValue, int|float $widthValue, int|float $depthValue) : array 
    {
        $objectHeight = ($unitId == Unit::METRIC->value) ? ($heightValue/100) : ($heightValue*0.0254);
        $objectWidth = ($unitId == Unit::METRIC->value) ? ($widthValue/100) : ($widthValue*0.0254);
        $objectDepth = ($unitId == Unit::METRIC->value) ? ($depthValue/100) : ($depthValue*0.0254);
        $objectVolume = 0.00;
        $objectSurfaceArea = 0.00;
        
        $dimensionNames = [
            'Object volume (m3)' => 0.00,
            'Object surface area (m2)' => 0.00,
            'Inner package height (m)' => 0.00,
            'Inner package width (m)' => 0.00,
            'Inner package depth (m)' => 0.00,
            'Package front area (m2)' => 0.00,
            'Package perimeter area (m2)' => 0.00,
            'Crate outer dimension height (m)' => 0.00,
            'Crate outer dimension width (m)' => 0.00,
            'Crate outer dimension depth (m)' => 0.00,
            'Crate max dimension (m)' => 0.00,
            'Crate surface area (m2)' => 0.00,
            'Crate footprint area (m2)' => 0.00,
            'Crate front area (m2)' => 0.00,
            'Crate volume (m3)' => 0.00,
        ];

        $objectVolume = $objectHeight * $objectWidth * $objectDepth;

        $objectSurfaceArea = 2 * ($objectHeight * $objectWidth + $objectHeight * $objectDepth + $objectWidth * $objectDepth);

        $crateType = model('CrateTypeModel')->find($createTypeId);

        if ($crateType) 
        {
            $innerPackageHeight = 0.00;
            $innerPackageWidth = 0.00;
            $innerPackageDepth = 0.00;
            $packageFrontArea = 0.00;
            $packagePerimeterArea = 0.00;
            $crateOuterDimensionHeight = 0.00;
            $crateOuterDimensionWidth = 0.00;
            $crateOuterDimensionDepth = 0.00;
            $CrateOuterMaxDimension = 0.00;
            $CrateSurfaceArea = 0.00;
            $CrateFootprintArea = 0.00;
            $CrateFrontArea = 0.00;
            $CrateVolume = 0.00;   

            $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel')->whereIn('x_var_text', [
                'Inner package height (m)',
                'Inner package width (m)',
                'Inner package depth (m)',
                'Crate outer dimension height (m)',
                'Crate outer dimension width (m)',
                'Crate outer dimension depth (m)'
            ])->findAll();

            if($modelDesignRegressionVariableInstance) {
                foreach($modelDesignRegressionVariableInstance as $modelDesignRegressionVariable) {

                    $modelDesignRegressionInstance = model('DesignRegressionModel')->where('crate_type_id', (int)$crateType['id'])
                    ->where('design_regression_variable_id', $modelDesignRegressionVariable['id'])->findAll();
                    
                    if($modelDesignRegressionInstance) {

                        foreach($modelDesignRegressionInstance as $key => $designRegression) {
                            
                            $calculateM = (float)$designRegression['calculate_m'];
                            $calculateB = (float)$designRegression['calculate_b'];
    
                            if($modelDesignRegressionVariable['x_var_text'] == 'Inner package height (m)') {
                                $innerPackageHeight = ($objectHeight*$calculateM)+$calculateB;
                            }
                            
                            if($modelDesignRegressionVariable['x_var_text'] == 'Inner package width (m)') {
                                $innerPackageWidth = ($objectWidth*$calculateM)+$calculateB;
                            }
    
                            if($modelDesignRegressionVariable['x_var_text'] == 'Inner package depth (m)') {
                                $innerPackageDepth = ($objectDepth*$calculateM)+$calculateB;
                            }
    
                            if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension height (m)') {
                                $crateOuterDimensionHeight = ($objectHeight*$calculateM)+$calculateB;
                            }
                            
                            if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension width (m)') {
                                $crateOuterDimensionWidth = ($objectWidth*$calculateM)+$calculateB;
                            }
    
                            if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension depth (m)') {
                                $crateOuterDimensionDepth = ($objectDepth*$calculateM)+$calculateB;
                            }
                        }
                    }
                }
            }

            $packageFrontArea = $innerPackageHeight * $innerPackageWidth;
            $packagePerimeterArea = 2 * ($innerPackageHeight * $innerPackageDepth + $innerPackageWidth * $innerPackageDepth);
            $CrateOuterMaxDimension =  max([
                $crateOuterDimensionHeight,
                $crateOuterDimensionWidth,
                $crateOuterDimensionDepth,
            ]);
            $CrateSurfaceArea = 2 * ($crateOuterDimensionHeight * $crateOuterDimensionWidth + $crateOuterDimensionWidth * $crateOuterDimensionDepth + $crateOuterDimensionHeight * $crateOuterDimensionDepth);

            $CrateFootprintArea = ($crateOuterDimensionWidth * $crateOuterDimensionDepth);
            $CrateFrontArea = ($crateOuterDimensionHeight * $crateOuterDimensionWidth);
            $CrateVolume = $crateOuterDimensionHeight * $crateOuterDimensionWidth * $crateOuterDimensionDepth;

            $dimensionNames = [
                'Object volume (m3)' => $objectVolume,
                'Object surface area (m2)' => $objectSurfaceArea,
                'Inner package height (m)' => $innerPackageHeight,
                'Inner package width (m)' => $innerPackageWidth,
                'Inner package depth (m)' => $innerPackageDepth,
                'Package front area (m2)' => $packageFrontArea,
                'Package perimeter area (m2)' => $packagePerimeterArea,
                'Crate outer dimension height (m)' => $crateOuterDimensionHeight,
                'Crate outer dimension width (m)' => $crateOuterDimensionWidth,
                'Crate outer dimension depth (m)' => $crateOuterDimensionDepth,
                'Crate max dimension (m)' => $CrateOuterMaxDimension,
                'Crate surface area (m2)' => $CrateSurfaceArea,
                'Crate footprint area (m2)' => $CrateFootprintArea,
                'Crate front area (m2)' => $CrateFrontArea,
                'Crate volume (m3)' => $CrateVolume,
            ];
        }

        return $dimensionNames;
    }

    /** Get Calculated Values */
    public function getCalculatedValues(int $createTypeId, int $unitId, int|float $heightValue, int|float $widthValue, int|float $depthValue) : array
    {
        $dimensionNames = $this->getDimensionValues($createTypeId, $unitId, $heightValue, $widthValue, $depthValue);
        
        $weightOfCrate = 0.00;
        $embodiedCarbonFactorInKg = 0.00;

        $crateTypeValues = [
            'ECONOMY' => [
                30,
                16,
                43,
                22,
                22,
                24,
                40,
                43,
                10,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51,
            ],
            'MID-RANGE' => [
                30,
                16,
                43,
                22,
                22,
                24,
                40,
                43,
                10,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51                
            ],
            'MUSEUM' => [
                30,
                19,
                43,
                22,
                24,
                24,
                40,
                43,
                12,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51
            ]
        ];

        $mainTypes = [
            'Object Wrap',
            '2D Tray Panel',
            '2D Tray Panel Edge Tape',
            '2D Tray Panel Foam Perimeter',
            'Foam Cushion Pads',
            'Foam Insulation',
            'Lid Gasket',
            'Lid Gasket Liner Tape',
            'Plywood Crate Walls',
            'Crate Wall Battens',
            'Oversized Crate Wall Seams',
            'Corner Battens',
            'Handles',
            'Skids',
            'Skid Spacers',
            'Lid Closure Hardware',
            'Exterior Seal Paint',
            'Wood Assembly Adhesive',
            'Wood Assembly Staples',
            'Wood Assembly Screws'
        ];

        $crateType = model('CrateTypeModel')->find($createTypeId);

        if ($crateType) 
        {
            $materialTypeGroupByMainType = [];

            $materialByQuantySyncId = $crateTypeValues[$crateType["name"]];
            
            $materialTypes = [
                'PE film',
                ($crateType["name"]  == "MUSEUM" ? "Foamcore" : "Cardboard"),
                'EVA Tape',
                'Polyethylene foam',
                ($crateType["name"]  == "MUSEUM" ? "Polyurethane foam" : "Polyethylene foam"),
                'Polyurethane foam',
                'Neoprene',
                'EVA Tape',
                ($crateType["name"]  == "MUSEUM" ? "MDO Plywood" : "AC Plywood"),
                'Pine lumber',
                'Plywood',
                'Pine lumber',
                'Pine lumber',
                'Fir lumber',
                'Plywood',
                'Stainless steel',
                'Water based polyurethane',
                'Wood glue',
                'Steel',
                'Steel'
            ];
            
            foreach($mainTypes as $key => $mainType) {
                $materialTypeGroupByMainType[$mainType] = [
                    'material_type' => $materialTypes[$key],
                    'material_qty'  => $materialByQuantySyncId[$key]
                ];
            }

            $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel')->whereIn('x_var_text', $mainTypes)->findAll();

            if($modelDesignRegressionVariableInstance) {
                foreach($modelDesignRegressionVariableInstance as $modelDesignRegressionVariable) {

                    $objectSurfaceArea = 0.00;
                    $packageFrontArea = 0.00;
                    $packagePerimeterArea = 0.00;
                    $crateSurfaceArea = 0.00;
                    $crateFrontArea = 0.00;
                    $innerPackageHeight = 0.00;
                    $innerPackageWidth = 0.00;
                    $innerPackageDepth = 0.00;
                    $crateMaxDimension = 0.00;
                    $crateVolume = 0.00;
                    $crateFooterPrintArea = 0.00;

                    if($dimensionNames) {
                        foreach($dimensionNames as $keyName =>$modelDimensionValue) {
                            if($keyName == 'Object surface area (m2)') {
                                $objectSurfaceArea = (float)$modelDimensionValue;
                            } else if($keyName == 'Package front area (m2)') {
                                $packageFrontArea = (float)$modelDimensionValue;
                            } else if($keyName == 'Package perimeter area (m2)') {
                                $packagePerimeterArea = (float)$modelDimensionValue;
                            } else if($keyName == 'Crate surface area (m2)') {
                                $crateSurfaceArea = (float)$modelDimensionValue;
                            } else if($keyName == 'Crate front area (m2)') {
                                $crateFrontArea = (float)$modelDimensionValue;
                            } else if($keyName == 'Inner package height (m)') {
                                $innerPackageHeight = (float)$modelDimensionValue;
                            } else if($keyName == 'Inner package width (m)') {
                                $innerPackageWidth = (float)$modelDimensionValue;
                            } else if($keyName == 'Inner package depth (m)') {
                                $innerPackageDepth = (float)$modelDimensionValue;
                            } else if($keyName == 'Crate max dimension (m)') {
                                $crateMaxDimension = (float)$modelDimensionValue;
                            } else if($keyName == 'Crate volume (m3)') {
                                $crateVolume = (float)$modelDimensionValue;
                            } else if($keyName == 'Crate footprint area (m2)') {
                                $crateFooterPrintArea = (float)$modelDimensionValue;
                            }
                        }
                    }

                    $modelDesignRegressionInstance = model('DesignRegressionModel')->where('crate_type_id', (int)$crateType['id'])
                    ->where('design_regression_variable_id', $modelDesignRegressionVariable['id'])->findAll();
                    
                    if($modelDesignRegressionInstance) {
                    
                        foreach($modelDesignRegressionInstance as $designRegression) {

                            $calculateM = (float)$designRegression['calculate_m'];
                            $calculateB = (float)$designRegression['calculate_b'];
                            $setValue = false;
                            $materialTypeGroup = [];
                            $emobodiedCarbonFactorUnitPerKg = 0.00;
                            $weightInKg = 0.00;

                            if($modelDesignRegressionVariable['x_var_text'] == 'Object Wrap') {
                                $weightInKg = ($calculateM*$objectSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel') {
                                $weightInKg = ($calculateM*$packageFrontArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel Edge Tape') {
                                $weightInKg = ($calculateM*$packageFrontArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel Foam Perimeter') {
                                $weightInKg = ($calculateM*$packagePerimeterArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Foam Cushion Pads') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Foam Insulation') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Gasket') {
                                $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Gasket Liner Tape') {
                                $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Plywood Crate Walls') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Crate Wall Battens') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Oversized Crate Wall Seams') {
                                $countableOutput = 0;

                                if($innerPackageHeight > 1.22) {
                                    $countableOutput++;
                                }

                                if($innerPackageWidth > 1.22) {
                                    $countableOutput++;
                                }

                                if($innerPackageDepth > 1.22) {
                                    $countableOutput++;
                                }

                                if($crateMaxDimension > 2.44 || $countableOutput > 1) {
                                    $weightInKg = ($calculateM*$crateMaxDimension)+$calculateB;
                                }

                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Corner Battens') {
                                $weightInKg = ($calculateM*$crateVolume)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Handles') {
                                $weightInKg = ($calculateM*$crateVolume)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Skids') {
                                $weightInKg = ($calculateM*$crateFooterPrintArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Skid Spacers') {
                                $weightInKg = ($calculateM*$crateFooterPrintArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Closure Hardware') {
                                $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Exterior Seal Paint') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Adhesive') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Staples') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Screws') {
                                $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                $setValue = true;
                            }

                            if($setValue === true) {
                                $materialTypeGroup = $materialTypeGroupByMainType[$modelDesignRegressionVariable['x_var_text']];
                                $emobodiedCarbonFactorUnitPerKg = $this->getEmobodiedCarbonFactorUnitPerKg($materialTypeGroup['material_qty']);
                                $weightOfCrate+=max(0.00, $weightInKg);
                                $embodiedCarbonFactorInKg+=$weightInKg*$emobodiedCarbonFactorUnitPerKg;
                            }
                        }
                    }
                }
            }
        }

        return [$weightOfCrate, $embodiedCarbonFactorInKg];
    }

    /** Get Emobodied Carbon Factor Unit (Kg) Value */
    public function getEmobodiedCarbonFactorUnitPerKg($id) : float {
        $modelMaterialByQuantityInstance = model('MaterialByQuantityModel')->where('crate_design_value', $id)->first();
        
        if($modelMaterialByQuantityInstance) {
            return (float)$modelMaterialByQuantityInstance['embodied_carbon_factor_unit_per_kg'];
        }

        return 0.00;
    }

    private function setUserAuthOrToken() : void
    {
        if(!auth()->loggedIn()) {
            if(count($_COOKIE) == 0) {
                $cookie_value = bin2hex(random_bytes(16));
                $this->setExternalCookie(COOKIE_NAME, $cookie_value, time() + (86400 * 30), "/", '', false, true);
                /* if(!setcookie(COOKIE_NAME, $cookie_value, time() + (86400 * 30), "/")) { // 86400 = 1 day
                    $response->setBody(view('404'));
                    exit;
                } */
            } else {
                $this->setExternalCookie(COOKIE_NAME, $_COOKIE[COOKIE_NAME], time() + (86400 * 30), "/", '', false, true);
            }
        
            if (!$this->isTokenValid($_COOKIE[COOKIE_NAME])) {
                $this->storeTokenInDatabase($_COOKIE[COOKIE_NAME]);
            }
        }
    }

    // Method to set cookies, reusable across all controllers
    public function setExternalCookie($name, $value, $expire = 60*60*24*30, $path = '/', $domain = '', $secure = false, $httpOnly = true, $prefix = '')
    {
        $this->response->setCookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httpOnly);
    }


    private function storeTokenInDatabase($token)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $builder->insert([
            'username' => $token,
            'password' => 11111111,
            'email' => $token.'@test.com',
            'remember_token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function isTokenValid($token)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        return $builder->where('remember_token', $token)->countAllResults() > 0;
    }

    public function setDefaultResponse() {
        $primaryCrateType = model('CrateTypeModel')->getSelectedCrateType();
        $newObj = new stdClass;
        $newObj->DT_RowId = 1;
        $newObj->measurement_unit_id = 0;
        $newObj->height = 100;
        $newObj->width = 100;
        $newObj->depth = 5;
        $newObj->crate_type_id = $primaryCrateType ?? 0;
        $newObj->label_name = '';
        $newObj->weight_in_kg = 0;
        $newObj->embodied_carbon_factor_in_kg = 0;
        $newObj->number_of_oneway_trips = 0;
        $newObj->per_one_way_trip = 0;

        return $this->response->setJSON(
            [$newObj]
        );
    }
}
