<?php

require_once('Api.php');

class ApiWarehouse extends Api {

  private $_method;
  private $_data = [];

  public function __construct($url, $method) {

    $this->_method = $method;

    if (count($url) == 0){
        switch ($method) {
            case 'GET': $this->_data = $this->getListWarehouse();break;
            case 'POST': $this->addWarehouse();
        }


  }     // list of packages - /api/warehouse
    elseif ( ($id = intval($url[0])) !== 0 ){// details one packages - /api/warehouse/{id}
    switch ($method) {
        case 'GET': $this->_data = $this->getWarehouse($id);break;
        case 'DELETE': $this->deleteWarehouse($id);break;
        case 'PATCH': $this->patchWarehouse($id);break;
    }


  }

    echo json_encode( $this->_data, JSON_PRETTY_PRINT );

  }

  public function getListWarehouse(): array  {
    $packages = [];
    if($this->_method != 'GET') $this->catError(405);

    //$this->authentication(['admin']);

    self::$_columns = ['id', 'address', 'volume', 'AvailableVolume'];
    self::$_offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    self::$_limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    self::$_where[] = "active = ?";
    self::$_params[] = "1" ;
    $list = $this->get('WAREHOUSE');

    return $list;
  }

  public function getWarehouse($id): array {

    if($this->_method != 'GET') $this->catError(405);
    //$this->authentication(['admin'], [$id]);
    self::$_columns = ['id', 'address', 'volume', 'AvailableVolume'];
    self::$_where[] = 'id = ?';
    self::$_params[] = $id;
    $warehouse = $this->get('WAREHOUSE');
    if( count($warehouse) == 1 )
      return $warehouse[0];
    else
      return [];
  }

  private function deleteWarehouse($id){
      if($this->_method != 'DELETE') $this->catError(405);

      $data = $this->getJsonArray();
      $allowed = ['active'];
      if( count(array_diff(array_keys($data), $allowed)) > 0 ) {
        http_response_code(400);
        exit(0);
      }

      foreach ($data as $key => $value) {
        self::$_set[] = "$key = ?";
        self::$_params[] = $value;
      }


      $this->patch("WAREHOUSE", $id);
  }


  private function patchWarehouse($id){
    if($this->_method != 'PATCH') $this->catError(405);

    $data = $this->getJsonArray();
    $allowed = ['AvailableVolume'];
    if( count(array_diff(array_keys($data), $allowed)) > 0 ) {
      http_response_code(400);
      exit(0);
    }

    foreach ($data as $key => $value) {
      self::$_set[] = "$key = $key + ?";
      self::$_params[] = $value;
    }


    $this->patch("WAREHOUSE", $id);
   }

   private function addWarehouse(){
     if($this->_method != 'POST') $this->catError(405);

     $data = $this->getJsonArray();
     $allowed = ['address', 'volume'];
     if( count(array_diff(array_keys($data), $allowed)) > 0 ) {
       http_response_code(400);
       exit(0);
     }
     self::$_columns = $allowed;
     self::$_params = array_values($data);

     $this->add("WAREHOUSE", );
    }
}
