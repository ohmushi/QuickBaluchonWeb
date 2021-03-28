<?php


require_once('Api.php');

class ApiBill extends Api
{

    private $_method;
    private $_data = [];

    public function __construct($url, $method)
    {

        $this->_method = $method;

        if (count($url) == 0)
            $this->_data = $this->getListBills();     // list of bills - /api/bill


        elseif (($id = intval($url[0])) !== 0)      // details one bills - /api/bill/{id}
            switch ($method) {
                case 'GET':$this->_data = $this->getBill($id);break;
                case 'POST': $this->addBill($url); break;
                case 'PATCH':$this->patchBill($id);break;
                default: $this->catError(405); break;
            }
        else
            $this->catError(400);


        echo json_encode($this->_data, JSON_PRETTY_PRINT);

    }

    public function getListBills(): array {
        if($this->_method != 'GET') $this->catError(405);

        if(isset($_GET['client'])) {
            self::$_where[] = 'client = ?';
            self::$_params[] = intval($_GET['client']);
        }
        if(isset($_GET['paid'])) {
            self::$_where[] = 'paid = ?';
            self::$_params[] = intval($_GET['paid']);
        }

        $columns = ['id', 'client', 'grossAmount', 'netAmount', 'dateBill', 'pdfPath', 'paid'];
        $list = $this->get('MONTHLYBILL', $columns);
        $bills = [];
        if( $list != null ){
            foreach ($list as $bill) {
                $bills[] = $bill;
            }
        }
        return $bills;
    }

    public function getBill($id): array
    {
        //$this->authentication(['admin'], [$id]);
        self::$_where[] = 'id = ?';
        self::$_params[] = $id;
        $columns = ['id', 'client', 'grossAmount', 'netAmount', 'dateBill', 'pdfPath', 'paid'];
        $client = $this->get('MONTHLYBILL', $columns );
        if (count($client) == 1)
            return $client[0];
        else
            return [];
    }

    public function patchBill($id){
        $data = $this->getJsonArray();
        $allowed = ['paid'];

        if (count(array_diff(array_keys($data), $allowed)) > 0) {
            http_response_code(400);
            exit();
        }

        self::$_set[] = "paid = ?" ;
        self::$_params[] = $data['paid'];

        $this->patch('MONTHLYBILL', $id);
    }

    public function addBill ($url)
    {
        if (count($url) != 2 || empty($url[0]) || empty($url[1])) {
            http_response_code(400);
            return;
        }

        require_once('ApiPackage.php');
        $_GET['inner'] = 'PRICELIST, PRICELIST.id, PACKAGE.pricelist' ;
        $_GET['client'] = $url[0] ;
        $_GET['date'] = $url[1] ;

        $packages = new ApiPackage([], 'GET');
        $packages->resetParams();
        $pkgs = $packages->getListPackages(['PACKAGE.id', 'weight', 'volume', 'delay', 'dateDeposit']) ;

        if ($pkgs != null) {
            $total = $this->calculTotal($pkgs);
        }

        self::$_columns = ['grossAmount', 'netAmount', 'tva', 'dateBill', 'paid', 'client'] ;
        self::$_params = [
            $total * 1.2,
            $total,
            20,
            $url[1] . '-01',
            0,
            $url[0]
        ] ;
        $id = $this->add('MONTHLYBILL') ;
        $this->createBillPdf($id, $pkgs, $total) ;
        $this->resetParams();

        die();

        self::$_set[] = 'pdfPath = ?' ;
        self::$_params[] =  "bills/$id.pdf" ;
        $this->patch('MONTHLYBILL', $id) ;
    }

    public function createBillPdf($id, $packages, $total){
        require_once($_SERVER['DOCUMENT_ROOT'] . "/media/fpdf/fpdf.php");
        $this->_billManager = new BillManager();

        $cols = ['Date deposit', "weight", 'volume', 'delay', 'Price'];
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        foreach ($cols as $key) {
            $pdf->Cell(40, 20, "$key");
        }
        $pdf->Ln(10);
        foreach ($packages as $package) {
            if ($package['delay'] == 2)
                $price = $package['ExpressPrice'];
            else
                $price = $package['StandardPrice'];

            $pdf->Cell(40, 20, $package['dateDeposit']);
            $pdf->Cell(40, 20, $package['weight']);
            $pdf->Cell(40, 20, $package['volume']);
            $pdf->Cell(40, 20, $package['delay']);
            $pdf->Cell(40, 20, $price);

            $pdf->Ln(10);
        }

        $pdf->Cell(160, 20, "Total");
        $pdf->Cell(40, 20, "$total");
        $filename = $_SERVER['DOCUMENT_ROOT'] . "/bills/$id.pdf" ;
        $pdf->Output($filename, 'F');
    }

    public function calculTotal($packages){
        $total = 0;
        foreach($packages as $package) {
            if ($package["delay"] == 2) {
                $total += $package["ExpressPrice"];
                $package['price'] = $package["ExpressPrice"];
            } else {
                $total += $package["StandardPrice"];
                $package['price'] = $package['StandardPrice'];
            }
        }
        return $total;
    }
}
