<?php



//require_once __DIR__ . '/../Bootstrap.php';
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit();

$sheet = $spreadsheet->getActiveSheet();
$activesheet = $spreadsheet->setActiveSheetIndex(0);

// Set document properties
$spreadsheet->getProperties()->setCreator('CPI')
    ->setLastModifiedBy('CPI')
    ->setTitle('CPI')
    ->setSubject('Office 2007 XLSX CPI Document')
    ->setDescription('CPI ведомость');

$styleArray = [
    'font' => [
        'bold' => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

$styleborder = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

$sheet->getStyle('A5')->applyFromArray($styleArray);
$sheet->getStyle('B5')->applyFromArray($styleArray);
$sheet->getStyle('C5')->applyFromArray($styleArray);
$sheet->getStyle('D5')->applyFromArray($styleArray);
$sheet->getStyle('E5')->applyFromArray($styleArray);
$sheet->getStyle('F5')->applyFromArray($styleArray);
$sheet->getStyle('G5')->applyFromArray($styleArray);
$sheet->getStyle('H5')->applyFromArray($styleArray);
$sheet->getStyle('I5')->applyFromArray($styleArray);
$sheet->getStyle('J5')->applyFromArray($styleArray);
$sheet->getStyle('K5')->applyFromArray($styleArray);
$sheet->getStyle('L5')->applyFromArray($styleArray);



$sheet->mergeCells('A1:K1');
$sheet->mergeCells('A2:K2');
$sheet->mergeCells('A3:K3');

$sheet->getStyle('A1')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3')
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
//$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);
$sheet->getColumnDimension('J')->setAutoSize(true);
$sheet->getColumnDimension('K')->setAutoSize(true);
$sheet->getColumnDimension('L')->setAutoSize(true);





$sheet
    ->getStyle('C7')
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER );


// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', '№ _______ ведомость')
    ->setCellValue('A2', 'оценивания слушателей, обучившихся на курсах квалификации по образовательной программе "Всемирная история и история Казахстана. (с учетом модерации)')
    ->setCellValue('A3', 'с 9 по 20 августа 2021 года на базе филиалов ЧУ «Центр педагогического мастерства» г.Нур-Султан')
    ->setCellValue('A4', '');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A5', '№')
    ->setCellValue('B5', 'ФИО Слушателей')
    ->setCellValue('C5', 'ИИН')
    ->setCellValue('D5', 'Программа')
    ->setCellValue('E5', 'Язык обучения')
    ->setCellValue('F5', '№ Группы')
    ->setCellValue('G5', 'Период обучения')
    ->setCellValue('H5', 'Итоговая оценка')
    ->setCellValue('I5', 'Плагиат')
    ->setCellValue('J5', 'Результат  Зачет/Незачет/Неявка')
    ->setCellValue('K5', 'Регион')
    ->setCellValue('L5', 'ФИО тренера')
;
$k = 5;
$q = 0;
foreach ($_POST['groups'] as $group){
    $results = $wpdb->get_results($s=$wpdb->prepare("SELECT a.date_reg, b.surname, b.name, b.patronymic, b.iin, c.number_group, d.p_name, e.name_ru lang_name, c.start_date, c.end_date, g.name region_name, h.total, h.decision, h.section_a, h.section_b, i.surname surname_tr, i.name name_tr, i.patronymic patronymic_tr 
FROM p_groups_users a 
LEFT OUTER JOIN p_user_fields b ON b.user_id = a.id_user 
LEFT OUTER JOIN p_groups c ON c.id = a.id_group 
LEFT OUTER JOIN p_programs d ON d.id = c.program_id 
LEFT OUTER JOIN p_lang e ON e.id = c.lang_id 
LEFT OUTER JOIN p_user_fields_listeners f ON f.user_id = a.id_user 
LEFT OUTER JOIN p_region g ON g.id = f.region_id 
LEFT OUTER JOIN p_proforma_user_result h ON h.user_id = a.id_user AND h.group_id = a.id_group 
LEFT OUTER JOIN p_user_fields i ON i.user_id = c.trener_id
WHERE `id_group` = %d", $group));



    foreach ($results as $result){
        $k++;
        $q++;
        $sheet->getStyle('A'.$k )->applyFromArray($styleborder);
        $sheet->duplicateStyle(
            $sheet->getStyle('A'.$k),
            'A'. $k .':L'. $k
        );

        $sheet->getStyle('C'.$k)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER );

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $k, $q)
            ->setCellValue('B' . $k, "{$result->surname} {$result->name} {$result->patronymic}")
            ->setCellValue('C' . $k, $result->iin)
            ->setCellValue('D' . $k, $result->p_name)
            ->setCellValue('E' . $k, $result->lang_name)
            ->setCellValue('F' . $k, $result->number_group)
            ->setCellValue('G' . $k, "{$result->start_date} - {$result->end_date}")
            ->setCellValue('H' . $k, $result->total)
            ->setCellValue('I' . $k, "{$result->section_a} / {$result->section_b}")
            ->setCellValue('J' . $k, $result->decision)
            ->setCellValue('K' . $k, $result->region_name)
            ->setCellValue('L' . $k, "{$result->surname_tr} {$result->name_tr} {$result->patronymic_tr}")
        ;

        $num_group = $result->number_group;
    }
    $t = $k;
}

for($i = 1; $i <= 6; $i++){
    $t++;
    $sheet->mergeCells("B$t:E$t");
}

$one = $k + 1; $two = $k + 2; $three = $k + 3; $four = $k + 4; $five = $k + 5; $six = $k + 6;
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $one, "Вильданова Сауле Аифовна   _________________________________________________");
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $two, "Ф.И.О. заместителя директора (полностью)                         Подпись");
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $three, "Тажибаев Асхат Куатович_____________________________________________________________________________________");
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $four, "Ф.И.О.И.О. начальника отдела (полностью)                               Подпись");
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $five, "Рахимжанова Сауле Канатовна ___________________________________________________________________________________");
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('B' . $six, "Ф.И.О. отв.менеджера (полностью)                                     Подпись");




// Rename worksheet
$sheet->setTitle("Ведомость");

$name = "Ведомость";

//echo "$name"; exit();

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);