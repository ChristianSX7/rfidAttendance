<?php  
// Connect to database
require'connectDB.php';

// Add user
if (isset($_POST['Add'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $Number = $_POST['number'];
    $Email = $_POST['email'];
    // if(isset($_POST['numberplate'])){
    $numberplate = $_POST['numberplate'];
    // };
    $dev_uid = $_POST['dev_uid'];
    
   
    // Check if there is any selected user
    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                if (!empty($Uname) && !empty($Number) && !empty($Email) ) {
                    // Check if there is any user who already has the Serial Number
                    $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND id NOT like ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error";
                        exit();
                    } else {
                        mysqli_stmt_bind_param($result, "di", $Number, $user_id);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {
                            $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $dev_uid);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $dev_name = $row['device_dep'];
                                } else {
                                    $dev_name = "All";
                                }
                            }
                            $sql = "SELECT numberplate FROM users WHERE numberplate = ? Limit 1";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "SQL_Error";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $numberplate_to_check);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (mysqli_num_rows($result) > 0) {
                                    echo "Biển số xe đã tồn tại trong cơ sở dữ liệu.";
                                } else {
                                    echo "Biển số xe chưa tồn tại trong cơ sở dữ liệu.";
                                }
                            }


                            
                            $sql = "UPDATE users SET username=?, serialnumber=?, numberplate=?, email=?, user_date=CURDATE(), device_uid=?, device_dep=?, add_card=1 WHERE id=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_select_Fingerprint";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "sdssssi", $Uname, $Number, $numberplate, $Email, $dev_uid, $dev_name, $user_id );
                                mysqli_stmt_execute($result);
                                echo 1;
                                exit();
                            }
                        } else {
                            echo "The serial number is already taken!";
                            exit();
                        }
                    }
                } else {
                    echo "Empty Fields";
                    exit();
                }
            } else {
                echo "This User already exists";
                exit();
            }
        } else {
            echo "There's no selected Card!";
            exit();
        }
    }
}

// Update an existing user 
if (isset($_POST['Update'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $Number = $_POST['number'];
    $Email = $_POST['email'];
    if (isset($numberplate['numberplate'])){
    $numberplate = $_POST['numberplate'];}; // Kiểm tra và gán giá trị cho biến numberplate
    $dev_uid = $_POST['dev_uid'];
    

    
    // Kiểm tra xem các trường thông tin đã được nhập đủ không
    if (empty($Uname) || empty($Number) || empty($Email)) {
        echo "Empty Fields";
        exit();
    }
    
    // Kiểm tra xem người dùng đã được chọn hay chưa
    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn); // callback function 
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $user_id); // interger 
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                echo "First, You need to add the User!";
                exit();
            } else {
                // Kiểm tra xem có người dùng nào khác đã sử dụng số serial đã nhập hay không
                $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND id NOT like ?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error";
                    exit();
                } else {
                    mysqli_stmt_bind_param($result, "di", $Number, $user_id);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if (!$row = mysqli_fetch_assoc($resultl)) {
                        $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error";
                            exit();
                        } else {
                            mysqli_stmt_bind_param($result, "s", $dev_uid);
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            if ($row = mysqli_fetch_assoc($resultl)) {
                                $dev_name = $row['device_dep'];
                            } else {
                                $dev_name = "All";
                            }
                        }
                        // Thực hiện cập nhật thông tin người dùng
                        $sql = "UPDATE users SET username=?, serialnumber=?, numberplate=?, email=?, device_uid=?, device_dep=? WHERE id=?";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_select_Card";
                            exit();
                        } else {
                            mysqli_stmt_bind_param($result, "sdssssi", $Uname, $Number, $numberplate, $Email, $dev_uid, $dev_name, $user_id );
                            mysqli_stmt_execute($result);
                            echo 1;
                            exit();
                        }
                    } else {
                        echo "The serial number is already taken!";
                        exit();
                    }
                }
            }    
        } else {
            echo "There's no selected User to be updated!";
            exit();
        }
    }
}


// Select fingerprint 
if (isset($_GET['select'])) {
    $card_uid = $_GET['card_uid'];

    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select";
        exit();
    } else {
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        header('Content-Type: application/json');
        $data = array();
        if ($row = mysqli_fetch_assoc($resultl)) {
            foreach ($resultl as $row) {
                $data[] = $row;
            }
        }
        $resultl->close();
        $conn->close();
        print json_encode($data);
    } 
}

// Delete user 
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];

    if (empty($user_id)) {
        echo "There is no selected user to remove";
        exit();
    } else {
        $sql = "DELETE FROM users WHERE id=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_delete";
            exit();
        } else {
            mysqli_stmt_bind_param($result, "i", $user_id);
            mysqli_stmt_execute($result);
            echo 1;
            exit();
        }
    }
}
?>
