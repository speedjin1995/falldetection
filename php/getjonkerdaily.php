<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['exactStartDate'], $_POST['exactEndDate'])){
    $exactStartDate = filter_input(INPUT_POST, 'exactStartDate', FILTER_SANITIZE_STRING);
    $exactEndDate = filter_input(INPUT_POST, 'exactEndDate', FILTER_SANITIZE_STRING);
    $message = array();

    if ($select_stmt3 = $db->prepare("SELECT * FROM pointCloud WHERE Date>=? AND Date<=? ORDER BY Date")) {
        $select_stmt3->bind_param('ss', $exactStartDate, $exactEndDate);
        
        // Execute the prepared query.
        if ($select_stmt3->execute()) {
            $result3 = $select_stmt3->get_result();
            $dateBar = array();
            
            while ($row3 = $result3->fetch_assoc()) {
                if(!in_array(substr($row3['Date'], 10, 9), $dateBar)){
                    $message[] = array( 
                        'Date' => substr($row3['Date'], 10, 9),
                        'x' => array(),
                        'y' => array(),
                        'z' => array()
                    );

                    array_push($dateBar, substr($row3['Date'], 10, 9));

                    $key = array_search(substr($row3['Date'], 10, 9), $dateBar);
                    array_push($message[$key]['x'], (float)$row3['x_point']);
                    array_push($message[$key]['y'], (float)$row3['y_point']);
                    array_push($message[$key]['z'], (float)$row3['z_point']);
                }


                /*$key = array_search(substr($row3['Date'], 10, 9), $dateBar);
                array_push($message[$key]['x'], (float)$row3['x_point']);
                array_push($message[$key]['y'], (float)$row3['y_point']);
                array_push($message[$key]['z'], (float)$row3['z_point']);*/
            } 
            
            $select_stmt3->close();
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status" => "success",
            "message" => $message
        )
    );  
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Parameter"
        )
    ); 
}