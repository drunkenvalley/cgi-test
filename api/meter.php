<?php
require_once("db.php");
// Todo: user.php for managing login, etc. 
require_once("meter-class.php");

/*ADD METER/READINGS*/
if(isset($_GET['add']))
{
    //Todo: Verify user
    //Only an actual meter should be able to report values,
    //but for test purposes (and the lack of user management)
    //we need to be able to insert to verify other behavior.
    
    $meter = new meter();
    echo $meter->set();
}

/*GET METER*/
if(isset($_GET['get']) && !empty($_GET['meter_id']) && !empty($_GET['from']) && !empty($_GET['to']))
{
    //Todo: Verify user
    //Only the meter's owner should be able to retrieve this,
    //for privacy's sake, but that's another layer of complexity.
    
    $meter = new meter();
    echo $meter->get($_GET['meter_id'], $_GET['from'], $_GET['to']);
}

/*GET TOTAL*/
if(isset($_GET['total']) && !empty($_GET['meter_id']) && !empty($_GET['from']) && !empty($_GET['to']))
{
    //Todo: Verify user
    //Only the meter's owner should be able to retrieve this,
    //for privacy's sake, but that's another layer of complexity.
    
    $meter = new meter();
    echo $meter->total($_GET['meter_id'], $_GET['from'], $_GET['to']);
}

/*GET CUSTOMER'S METER IDS*/
if(isset($_GET['meters']) && !empty($_GET['customer'])) {
    //Todo: Verify user
    //Only the meter's owner should be able to retrieve this,
    //for privacy's sake, but that's another layer of complexity.
    
    global $pdo;
    $customer_id = $_GET['customer'];
        
    $get_meters_sql = "SELECT meter_id AS meter FROM `meter-registrar` WHERE customer_id = ?";
    $get_meters = $pdo->prepare($get_meters_sql);
    $get_meters->execute([$customer_id]);
    $rows = $get_meters->fetchAll(PDO::FETCH_ASSOC);
    
    $meters = array();
    foreach($rows as $row) {
        array_push($meters, $row['meter']);
    }
    echo json_encode($meters);
}

?>
