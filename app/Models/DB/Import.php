<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;
//use Maatwebsite\Excel\Concerns\RegistersEventListeners;
//use Maatwebsite\Excel\Events\AfterSheet;
//use Maatwebsite\Excel\Facades\Excel;
//use App\Exports\DBExport;
//use PHPExcel;
//use PHPExcel_IOFactory;

class Import extends DB
{
    use HasFactory;

    public function import()
    {
//        $export = new DBExport([
//            [7, 2, 3],
//            [4, 5, 6]
//        ]);
//
//        Excel::store($export, 'invoices.xlsx', 'public');
//
//        $event = new AfterSheet();
//        $sheet = array(
//            array(
//                'a1 data',
//                'b1 data',
//                'c1 data',
//                'd1 data',
//            )
//        );
//        $doc = new PHPExcel();
//        $doc->setActiveSheetIndex(0);
//        dd();
//        $doc->getActiveSheet()->fromArray($sheet, null, 'A1');
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="your_name.xls"');
//        header('Cache-Control: max-age=0');
//
//        // Do your stuff here
//        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
//
//        $writer->save(Storage::path('public'));
        $get = new Get($this->name);
        $this->addHeader($get);
        $this->addRows($get);
        $this->addCloseTag($get);
    }

    private function addHeader(Get $get):void {
        $columns = $get->getColumns();
        $html = '<table border="1"><tr>';
        foreach ($columns as $column) {
            $html .= "<th>$column</th>";
        }
        $html .= '</tr>';
        Storage::put('public/' . $this->name . '/import/' . $this->name . '.html', $html);
    }

    private function addRows(Get $get) {
        $filepath = Storage::path($get->getFilePath($this->name));
        $handle = @fopen($filepath, "r");
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $html = '<tr>';
                $buffer = json_decode($json, true);
                if ($buffer) {
                    foreach ($buffer['data'] as $value) {
                        $html .= "<td>$value</td>";
                    }
                    $html .= '</tr>';
                    Storage::append('public/' . $this->name . '/import/' . $this->name . '.html', $html);
                }
            }
            fclose($handle);
        }
    }

    private function addCloseTag(Get $get) {
        Storage::append('public/' . $this->name . '/import/' . $this->name . '.html', '</table>');
    }

    public function getImportFilePath() {
        return '/storage/' . $this->name . '/import/' . $this->name . '.html';
    }

//    public static function afterSheet(AfterSheet $event){
//        $event->sheet->appendRows(array(
//            array('test1', 'test2'),
//            array('test3', 'test4'),
//            //....
//        ), $event);
//    }
}
