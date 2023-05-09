<?php

try {
    session_start();
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    //echo "s: ".$_SESSION['sacmbs']."<br>c: ".$_COOKIE['sacmbs']."<br>i: $rid";
    if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1) {
        $_SESSION = array();
        setcookie('sacmbs', $rid, ['expires' => time() - 3600, 'path' => '/', 'domain' => 'sacmarketing.co.za', 'secure' => true, 'httponly' => false, 'samesite' => 'None']);
        session_destroy();
        header("location: /mysac/warranty-form.php");
        exit;
        //<script type='text/javascript'> if(history.replaceState) history.replaceState({}, "", "/"); // show only root url</script>
    } else if (isset($_REQUEST['showform']) && $_REQUEST['showform'] == 1) {
        $showform = 1;
        $ucode = $_COOKIE["sacmuc"];
    } else if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "login") {
        require "includes/login.inc";
    } else if (isset($_COOKIE['sacmbs'])) {
        $rid = $_COOKIE["sacmbs"];
        $_SESSION["sacmbs"] = "$rid";
        //header("location: /mysac/warranty-form.php");
        $showform = 1;
    } else if (isset($_SESSION['sacmbs'])) {
        //header("location: /mysac/warranty-form.php");
        $showform = 1;
    } else {
        if (isset($_GET['notice'])) {
            $msg = $_GET['notice'];
        }
        $thisfile = htmlspecialchars($_SERVER["PHP_SELF"]);
        require '../mysac/loginpage.php';
    }


    if (isset($showform) && $showform === 1) {
        if (isset($_SESSION['sacmbs'])) {
            $today = date('Y-m-d');
            $timenow = date('Y-m-d H:i:s');
            require_once "../globals/dbcon.inc";
            require_once "includes/log.inc"; // provides $ucode $userlevel $userbranch

            //echo "<p>ul: $userlevel</p>";

            if ($userlevel > "10") {
                $allowed = 1;
            }
            if ($allowed === 1) {
?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <script type='text/javascript'>
                        if (history.replaceState) history.replaceState({}, "", "/"); // show only root url
                    </script>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                    <title>My SAC Internal</title>
                    <link rel="stylesheet" type="text/css" href="/v7/includes/sacmarketing07.css">
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
                    <style>
                        body {
                            padding: 16px
                        }
                    </style>
                </head>

                <body>

                    <div class="print fl-l">
                        <div class="print fl-l">
                            <h1>Warranty Claim Form</h1>
                            <p>&nbsp;</p>
                            <?php
                            $qry = mysqli_query($dbcon, "SELECT branch FROM sacstaff WHERE id = '$rid' LIMIT 1;");
                            $userbranch = mysqli_fetch_assoc($qry)['branch'];

                            //$claim_compny = "Trucks"; // adjust per business
                            $claim_compny = "Trucks";
                            $pastyear = date('Y', strtotime('-2 year'));

                            /*
                                $claim_do = $_REQUEST["claim_do"];

                                if ( $claim_do == "request" ){
                                    $claim_nr = $_REQUEST["claim_nr"]; //this one is for branch
                                    $claim_date = $_REQUEST["claim_date"];
                                    $claim_branch = $_REQUEST["claim_branch"];
                                    //$branch_claim_nr = $_REQUEST["branch_claim_nr"];
                                    $claim_salesman = $_REQUEST["claim_salesman"];
                                    $cust_name = $_REQUEST["cust_name"];
                                    $cust_name = mysqli_real_escape_string($dbcon, $cust_name);
                                    $cust_accnr = $_REQUEST["cust_accnr"];
                                    $invoice_nr = $_REQUEST["invoice_nr"];
                                    $invoice_date = $_REQUEST["invoice_date"];
                                    $vehicle_make = $_REQUEST["vehicle_make"];
                                    $vehicle_year = $_REQUEST["vehicle_year"];
                                    $vehicle_model = $_REQUEST["vehicle_model"];
                                    $vehicle_chassis = $_REQUEST["vehicle_chassis"];
                                    $vehicle_engine = $_REQUEST["vehicle_engine"];
                                    $part_qty = $_REQUEST["part_qty"];
                                    $part_nr = $_REQUEST["part_nr"];
                                    $part_desc = $_REQUEST["part_desc"];
                                    $part_desc = mysqli_real_escape_string($dbcon, $part_desc);
                                    $part_return = $_REQUEST["part_return"];
                                    $fitted_date = $_REQUEST["fitted_date"];
                                    $fitted_km = $_REQUEST["fitted_km"];
                                    $failed_date = $_REQUEST["failed_date"];
                                    $failed_km = $_REQUEST["failed_km"];
                                    $customer_fit = $_REQUEST["customer_fit"];
                                    $customer_fit = mysqli_real_escape_string($dbcon, $customer_fit);
                                    $thirdparty_fit = $_REQUEST["3rdparty_fit"];
                                    $thirdparty_fit = mysqli_real_escape_string($dbcon, $thirdparty_fit);
                                    $request_reason = $_REQUEST["request_reason"];
                                    $request_reason = mysqli_real_escape_string($dbcon, $request_reason);
                                    $assess_report = $_REQUEST["assess_report"];
                                    $assess_report = mysqli_real_escape_string($dbcon, $assess_report);	
        
                                    $sql1 = "INSERT INTO warrantyclaims VALUES ('', '$claim_nr', '$claim_date', '$claim_compny', '$claim_branch', '', '$ucode', '$claim_salesman', '$cust_name', '$cust_accnr', '$invoice_nr', '$invoice_date', '$vehicle_make', '$vehicle_year', '$vehicle_model', '$vehicle_chassis', '$vehicle_engine', '$part_qty', '$part_nr', '$part_desc', '$part_return', '$fitted_date', '$fitted_km', '$failed_date', '$failed_km', '$customer_fit', '$thirdparty_fit', '$request_reason', '$assess_report', '$waybill_nr', '', '', '', '', '');";

                                    if  ( mysqli_query($dbcon, $sql1) ){
                                        ?>
                                        <div class="full sb ovfInit fl-l">
                                            <h2>Your Claim Request was submitted successfully</h2>
                                        </div>
                                        <?php
                                        //* Mail Notifications *
                                        $sql5 = "SELECT email FROM sacstaff WHERE ucode = '$ucode';";
                                        $qry5 = mysqli_query($dbcon, $sql5);
                                        $row5 = mysqli_fetch_assoc($qry5);
                                        $salesmanemail = $row5['email'];
        
                                        //$tomail = "dev@sactrucks.co.za";
                                        //$ccmail = "$salesmanemail, dev.sactrucks@gmail.com";
                                        $frmail = "warranty.claims@sactrucks.co.za";
        
                                        $tomail = "idstore@sactrucks.co.za,warranty.claims@sactrucks.co.za";
                                        $ccmail = "$salesmanemail, warrantees@sactrucks.co.za";
                                        $header = "MIME-Version: 1.0" . "\r\n";
                                        $header .= "Content-type: text/html; charset=UTF-8" . "\r\n";
                                        $header .= "From: ".$frname." <".$frmail.">\r\n";
                                        $header .= "Cc: ".$ccmail.PHP_EOL;
                                        //$header .= "Bcc: dev@sactrucks.co.za".PHP_EOL;
        
                                        $content = "
                                        <html>
                                        <head>
                                        <title>New Warranty Claim Submitted</title>
                                        </head>
                                        <body>
                                            <p>A new warranty claim with number, $claim_nr, has just been submitted by $claim_salesman at $claim_branch.</p>
                                            <p>Please log in onto the <a href='https://sacmarketing.co.za'>SAC Internal Marketing</a> platform to view this claim.</p>
                                        </body>
                                        </html>
                                        ";
                                            
                                        $subject = "$claim_nr: New Warranty Claim Submitted";
                                        mail($tomail, $subject, $content, $header);
                                        
                                        //* End mail Notifications *
                                    }
                                    else {
                                        ?>
                                        <div class="full sb ovfInit fl-l">
                                            <h2>Your Claim Request could not be submitted</h2>
                                            <p><?php echo $sql1 ."<br>".mysqli_error($dbcon); ?></p>
                                        </div>
                                        <?php
                                    }
                                }
                            */
                            if ($userbranch == "Head Office") {
                                $preclaimcode = "000";
                            } else if ($userbranch == "Used Parts") {
                                $preclaimcode = "210";
                            }
                            //else if ( $ucode == "em001" || $ucode == "ts001" ){ // change option for Mandie(em001)
                            else if ($ucode == "em001") { // change option for Mandie(em001)
                                $prec = $_GET['prec'];
                                if ($prec == "210") {
                                    $active101 = "";
                                    $active210 = "active";
                                    $preclaimcode = "210";
                                    $userbranch = "MCV & LCV";
                                } else {
                                    $active101 = "active";
                                    $active210 = "";
                                    $preclaimcode = "101";
                                    $userbranch = "Centurion";
                                }
                                echo '<p><a href="/mysac/warranty-form.php?prec=101" class="ib"><button class="btn128grey ' . $active101 . ' ib"><b>101</b></button></a> &emsp; <a href="/mysac/warranty-form.php?prec=210" class="ib"><button class="btn128grey ' . $active210 . ' ib"><b>210</b></button></a></p>';
                                //echo "<p>pre: $prec<br>pcd: $preclaimcode</p>";
                            } else {
                                if ($userbranch == 'CNT Recycling') {
                                    $ub = "Used Parts";
                                } else {
                                    $ub = $userbranch;
                                }
                                $sql4 = "SELECT code FROM codes WHERE name = '$ub' LIMIT 1;";
                                $res4 = mysqli_query($dbcon, $sql4);
                                $row4 = mysqli_fetch_assoc($res4);
                                $preclaimcode = $row4["code"];
                            }

                            $sql2 = "SELECT name, surname, departm FROM sacstaff WHERE ucode = '$ucode' LIMIT 1;";
                            $res2 = mysqli_query($dbcon, $sql2);
                            $row2 = mysqli_fetch_assoc($res2);
                            $fname = $row2["name"];
                            $lname = $row2["surname"];
                            $deptm = $row2["departm"];
                            $userfullname = $fname . " " . $lname;

                            //if ( $claim_compny == "LCV" || $claim_compny == "Trucks" ){
                            //    $claimcpy = "claim_compny = 'LCV' AND claim_branch = '$userbranch' OR claim_compny = 'Trucks' AND claim_branch = '$userbranch'";
                            //} else {
                            $claimcpy = "claim_compny = '$claim_compny' AND claim_branch = '$userbranch'";
                            //}

                            $sql3 = "SELECT claim_nr AS last_code FROM warrantyclaims WHERE $claimcpy ORDER BY claim_nr DESC LIMIT 1;";
                            $res3 = mysqli_query($dbcon, $sql3);
                            $num3 = mysqli_num_rows($res3);
                            if ($num3 > 0) {
                                $row3 = mysqli_fetch_assoc($res3);
                                $last_cd = $row3["last_code"];
                                $claim_nr = $last_cd + 1;
                            } else {
                                $claim_nr = $preclaimcode . "0001";
                            }
                            ?>

                            <form method="post" action="/mysac/warranty-receive.php">
                                <input type="hidden" name="showform" value="1">
                                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                                <div class="full sb ovfInit fl-l noprint">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Branch Claim Nr:</div>
                                        <div class="w210 ib"><input type="hidden" class="input" name="claim_nr" value="<?php echo $claim_nr; ?>"><?php echo $claim_nr; ?></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Claim Date:</div>
                                        <div class="w210 ib"><input type="hidden" class="input" name="claim_date" value="<?php echo $today; ?>"><?php echo $today; ?></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Branch:</div>
                                        <div class="w210 ib"><input type="hidden" class="input" name="claim_branch" value="<?php echo $userbranch; ?>"><?php echo $userbranch; ?></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">HO Claim Nr:</div>
                                        <div class="w210 ib"><!--input type="text" class="input" name="branch_claim_nr" maxlength="16" --></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Salesman:</div>
                                        <div class="w590 ib"><input type="hidden" class="input" name="claim_salesman" maxlength="64" value="<?php echo $userfullname; ?>" required><?php echo $userfullname; ?></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>

                                <div class="full sb ovfInit">
                                    <div class="full h16 ovfInit">&nbsp;</div>
                                    <div class="w800 ib">
                                        <h2>Customer Details</h2>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Customer Name:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="cust_name" maxlength="64" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Customer Acc Nr:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="cust_accnr" maxlength="64" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Invoice Nr:*</div>
                                        <div class="w210 ib"><input type="text" class="input" name="invoice_nr" maxlength="64" required></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Invoice Date:*</div>
                                        <div class="w210 ib"><input type="date" class="input" name="invoice_date" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>

                                <div class="full sb ovfInit">
                                    <div class="full h16 ovfInit">&nbsp;</div>
                                    <div class="w800 ib">
                                        <h2>Vehicle Details</h2>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Make:*</div>
                                        <div class="w210 ib"><input type="text" class="input" name="vehicle_make" maxlength="32" required></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Year:</div>
                                        <div class="w210 ib"><input type="tel" class="input" name="vehicle_year" maxlength="4" pattern="[0-9]{0,4}"></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Model:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="vehicle_model" maxlength="32" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Chassis Number:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="vehicle_chassis" maxlength="18" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Engine Number:</div>
                                        <div class="w590 ib"><input type="text" class="input" name="vehicle_engine" maxlength="18"></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>

                                <div class="full sb ovfInit">
                                    <div class="full h16 ovfInit">&nbsp;</div>
                                    <div class="w800 ib">
                                        <h2>Part Details</h2>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Quantity:*</div>
                                        <div class="w210 ib"><input type="number" class="input" name="part_qty" min="1" max="99" required></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Part Number:*</div>
                                        <div class="w210 ib"><input type="text" class="input" name="part_nr" maxlength="16" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Description:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="part_desc" maxlength="64" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Date Fitted:*</div>
                                        <div class="w210 ib"><input type="date" class="input" name="fitted_date" min="<?php echo $pastyear; ?>-01-01" required></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Km Fitted:*</div>
                                        <div class="w210 ib"><input type="number" class="input" name="fitted_km" min="1" max="9999999" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Return If Declined:*</div>
                                        <div class="w590 ib"><input type="radio" name="part_return" value="Yes" required>Yes &emsp;/ &emsp;<input type="radio" name="part_return" value="No" required> No</div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>

                                <div class="full sb ovfInit">
                                    <div class="full h16 ovfInit">&nbsp;</div>
                                    <div class="w800 ib">
                                        <h2>Warranty Details</h2>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit fl-l">
                                    <div class="w380 ib">
                                        <div class="w130 ib">Date Failed:*</div>
                                        <div class="w210 ib"><input type="date" class="input" name="failed_date" required></div>
                                    </div>
                                    <div class="w380 ib">
                                        <div class="w130 ib">Km Failed:*</div>
                                        <div class="w210 ib"><input type="number" class="input" name="failed_km" min="1" max="9999999" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib">Fitted By Whom:*</div>
                                        <div class="w590 ib"><input type="text" class="input" name="3rdparty_fit" maxlength="64" required></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="w800 ib">
                                        <div class="w130 ib va-top">Reason for Warranty Request:*</div>
                                        <div class="w590 ib"><textarea class="textare2" name="request_reason" maxlength="512" placeholder="Insert detailed description of the fault/error/mistake you noticed" required></textarea></div>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>
                                <div class="selection full sb ovfInit fl-l">
                                    <div class="full sb ovfInit fl-l">
                                        <div class="w800 ib">
                                            <div class="w130 ib va-top">Assessment Report:</div>
                                            <div class="w590 ib"><textarea class="textare2" name="assess_report" maxlength="512" placeholder="Insert detailed description of the fault/error/mistake you noticed"></textarea></div>
                                        </div>
                                        <div class="full h4 ovfInit">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="full sb ovfInit">
                                    <div class="full h16 ovfInit">&nbsp;</div>
                                    <div class="w380 ib txt-ctr">
                                        <button type="submit" class="btn128" name="claim_do" value="request">Submit Request</button>
                                    </div>
                                    <div class="w380 ib txt-ctr">
                                        <button type="reset" class="btn128">Clear Form</button>
                                    </div>
                                    <div class="full h4 ovfInit">&nbsp;</div>
                                </div>

                            </form>
                        </div>
                    </div>

                    <p><a href="https://sacmarketing.co.za/mysac/warranty-form.php?logout=1">Log Out</a></p>

                </body>

                </html>

<?php
            } else {
                echo '<p><b>You are not allowed to currently see this page</b></p>';
                echo '<p><a href="https://sacmarketing.co.za/mysac/warranty-form.php?logout=1">Log Out</a></p>';
            }
            mysqli_close($dbcon);
        }
    }
} catch (Exception $e) {
    echo "Error" . $e->getMessage();
}

?>