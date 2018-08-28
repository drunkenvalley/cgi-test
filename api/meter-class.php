<?php
require_once("db.php");
// Todo: user.php for managing login, etc. 

class meter {
    private $customer_id;
    private $meter_id;
    private $from;
    private $to;
    private $values = array();
    
    public function meter() {
        //Intentionally left blank
    }
    
    //Interactions towards JSON.
    public function set() { //Fetch meter from JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        //I'd use meter(), but php doesn't support overloaded functions.
        //Since we want to load from JSON or SQL, meter() is left blank.
        $this->customer_id = $data['customer_id'];
        $this->meter_id = $data['meter_id'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->values = $data['values'];
        
        if($this->insert()) {
            return $this->json();
        }
        else {
            return "Error.";
        }
    }
    
    public function get($meter_id, $from, $to) { //Return current meter
        if(
            $meter_id != $this->meter_id ||
            $from != $this->from ||
            $to != $this->to
            ) {
            $this->load($meter_id, $from, $to);
        }
        return $this->json();
    }
    
    private function json() {
        return json_encode(get_object_vars($this));
    }
    
    //Interactions towards database
    private function insert() {
        global $pdo;    //Tells PHP we're looking at the $pdo 
                        //outside of this object's local scope
        
        //Insert, update or ignore who owns the meter
        $registrar_sql = "INSERT INTO `meter-registrar` (customer_id, meter_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE customer_id=?";
        $registrar_run = $pdo->prepare($registrar_sql);
        $registrar_run->execute([
            $this->customer_id,
            $this->meter_id,
            $this->customer_id
            ]);
        
        $rows = $registrar_run->rowCount();
        if($rows == 1) {
            //Maybe fire an email that a new meter is registered.
        }
        if($rows == 2) {
            //Maybe fire an email that a meter changed customer.
        }
        
        //Insert readings
        //1. Payload dates need to be fixed into SQL compatible dates.
        $from = substr_replace(substr_replace($this->from,' ',10,1),'',19,1);
        $to =   substr_replace(substr_replace($this->to,  ' ',10,1),'',19,1);
        
        //2. SQL
        $readings_sql = "INSERT INTO `meter-readings` (meter, start, end, value_time, value) VALUES (?,?,?,?,?)";
        $readings_run = $pdo->prepare($readings_sql);
        
        //3. Execute for each reading 
        //(Definitely not performance-friendly, better way out there?)
        foreach($this->values as $key => $reading) {
            $time = substr_replace(substr_replace($key,' ',10,1),'',19,1);
            //Since the payload didn't match an SQL timestamp.
            //Just replaces 'T' with ' ' and removes the Z.
            
            $readings_run->execute([
                $this->meter_id,
                $from,
                $to,
                $time,
                $reading
            ]);
        }
        
        //Notes: I don't check for duplicate readings because
        //I'm not sure whether duplicates 'should' be allowed.
        
        return true;
    }
    
    private function load($meter, $f /*from*/, $t /*to*/) {
        global $pdo; //Just putting PDO in scope.
        $from = substr_replace(substr_replace($f,' ',10,1),'',19,1);
        $to =   substr_replace(substr_replace($t,' ',10,1),'',19,1);
        
        $get_user_sql = "SELECT customer_id FROM `meter-registrar` WHERE meter_id = ?";
        $get_user = $pdo->prepare($get_user_sql);
        $get_user->execute([$meter]);
        $customer = $get_user->fetch(PDO::FETCH_ASSOC)['customer_id'];
        
        $load_sql = "SELECT * FROM `meter-readings` WHERE meter = ? AND (start BETWEEN ? AND ?) AND (end BETWEEN ? AND ?) ORDER BY start, end, value_time";
        $load_run = $pdo->prepare($load_sql);
        $load_run->execute([$meter, $from, $to, $from, $to]);
        
        
        $rows = $load_run->fetchAll(PDO::FETCH_ASSOC);
        
        $this->customer_id  = $customer;
        $this->meter_id     = current($rows)['meter'];
        $this->from         = current($rows)['start'];
        $this->to           = end($rows)['end'];
        $this->values       = array();
        
        foreach($rows as $row) {
            $key    = $row['value_time'];
            $value  = $row['value'];
            $this->values[$key] = $value;
        }
        
        return;
    }
    
    public function total($meter, $f, $t) {
        global $pdo; //Just putting PDO in scope.
        $from = substr_replace(substr_replace($f,' ',10,1),'',19,1);
        $to =   substr_replace(substr_replace($t,' ',10,1),'',19,1);
        
        $total_sql = "SELECT SUM(value) AS kw FROM `meter-readings` WHERE meter = ? AND (start BETWEEN ? AND ?) AND (end BETWEEN ? AND ?)";
        $total_run = $pdo->prepare($total_sql);
        $total_run->execute([$meter,$from,$to,$from,$to]);
        $kw = $total_run->fetch(PDO::FETCH_ASSOC)["kw"];
        
        return '{"kw":'.$kw.'}';
    }
}

?>
