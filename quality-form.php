    <?php
    try {

        session_start();
        //echo "s: ".$_SESSION['sacmbs']."<br>c: ".$_COOKIE['sacmbs']."<br>i: $rid";
        if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1) {
            $_SESSION = array();
            setcookie('sacmbs', $rid, ['expires' => time() - 3600, 'path' => '/', 'domain' => 'sacmarketing.co.za', 'secure' => true, 'httponly' => false, 'samesite' => 'None']);
            session_destroy();
            header("location: /mysac/quality-form.php");
            exit;
            //<script type='text/javascript'> if(history.replaceState) history.replaceState({}, "", "/"); // show only root url</script>
        } else if (isset($_REQUEST['showform']) && $_REQUEST['showform'] == 1) {
            $showform = 1;
        }
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] == "login") {
            require "includes/login.inc";
        } else if (isset($_COOKIE['sacmbs'])) {
            $rid = $_COOKIE["sacmbs"];
            $_SESSION["sacmbs"] = "$rid";
            $showform = 1;
        } else if (isset($_SESSION['sacmbs'])) {
            $showform = 1;
        } else {
            if (isset($msg)) {
                $msg = $_GET['notice'];
            }
            $thisfile = htmlspecialchars($_SERVER["PHP_SELF"]);
            require '../mysac/loginpage.php';
        }
        if (isset($showform) && $showform === 1) 
        {

            if (isset($_SESSION['sacmbs'])) {
                $today = date('Y-m-d');
                $timenow = date('Y-m-d H:i:s');
                require_once "../globals/dbcon.inc";
                require_once "includes/log.inc"; // provides $ucode $userlevel $userbranch

               // echo "<p>ul: $userlevel</p>";

                if ($userlevel > "10") {
                    //echo 'jy kan dit sien ';
                    $allowed = 1;
                }

                if ($allowed === 1) 
                {
                   // echo 'die moet die html maak';
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
                                <h1>Quality Return Form</h1>
                                <p>&nbsp;</p>
                                <?php

                                $return_ucode = $_POST['retucode'];

                                $return_do = $_REQUEST["return_do"];
                                //echo "die is die if statement ".$return_do;
                                if ($return_do == "request") {
                                    $return_nr = sprintf('%07d', $_POST["return_nr"]); //this one is for branch
                                    $return_date = $_POST["return_date"];
                                    $return_compny = $_POST["return_compny"];
                                    $return_branch = $_POST["return_branch"];
                                    $return_salesman = $_POST["return_salesman"];
                                    $invoice_nr = $_POST["invoice_nr"];
                                    $part_qty = $_POST["part_qty"];
                                    $part_nr = $_POST["part_nr"];
                                    $part_desc = $_POST["part_desc"];
                                    $part_desc = mysqli_real_escape_string($dbcon, $part_desc);
                                    $part_checked = $_POST["part_checked"];
                                    $qi01 = $_POST["qi01"];
                                    $qi02 = $_POST["qi02"];
                                    $qi03 = $_POST["qi03"];
                                    $qi04 = $_POST["qi04"];
                                    $qi05 = $_POST["qi05"];
                                    $qi06 = $_POST["qi06"];
                                    $qi07 = $_POST["qi07"];
                                    $qi08 = $_POST["qi08"];
                                    $qi09 = $_POST["qi09"];
                                    $qi10 = $_POST["qi10"];
                                    $quality_issue = $qi01 . $qi02 . $qi03 . $qi04 . $qi05 . $qi06 . $qi07 . $qi08 . $qi09 . $qi10;
                                    //$qi = $_POST["quality_issue"];
                                    $return_notes = $_POST["return_notes"];
                                    $return_notes = mysqli_real_escape_string($dbcon, $return_notes);
                                    $warranty_receive = $_POST["warranty_receive"];
                                    if (empty($warranty_receive)) {
                                        $warranty_receive = '0000-00-00';
                                        echo '1';
                                    }
                                    $status = $_POST["status"];
                                    $status = mysqli_real_escape_string($dbcon, $status);
                                    if (empty($status)) {
                                        $status = 'Submitted';
                                        echo '2';
                                        
                                    }
                                    $status_date = $_POST["status_date"];
                                    if (empty($status_date)) {
                                        $status_date = '0000-00-00';
                                        echo '3';
                                    }
                                    $notes = $_POST["notes"];
                                    $notes = mysqli_real_escape_string($dbcon, $ontes);


                                    $sql1 = "INSERT INTO qualityreturn (return_nr,return_date,return_compny,return_branch,return_ucode,return_salesman,invoice_nr,part_qty,part_nr,part_desc,part_checked,quality_issue,return_notes,warranty_receive,status,status_date,notes) VALUES ('$return_nr', '$return_date', '$return_compny', '$return_branch', '$return_ucode', '$return_salesman', '$invoice_nr', '$part_qty', '$part_nr', '$part_desc', '$part_checked', '$quality_issue', '$return_notes', '$warranty_receive', '$status', '$status_date', '$notes');";
                                    //echo "die is die sql statement /// ".$sql1;
                                    //echo "<p>".$sql1."<br>".$quality_issue."</p>";

                                    if (mysqli_query($dbcon, $sql1)) {
                                        /* Mail Notifications */
                                        $sql5 = "SELECT email FROM sacstaff WHERE ucode = '$return_ucode';";
                                        $qry5 = mysqli_query($dbcon, $sql5);
                                        $row5 = mysqli_fetch_assoc($qry5);
                                        $salesmanemail = $row5['email'];

                                        //$headoffice_receiving = "idstore@sactrucks.co.za";
                                        $headoffice_receiving = "dev@sactrucks.co.za, dev.sactrucks@gmail.com";


                                        //$tomail = "dev@sactrucks.co.za";
                                        //$ccmail = "$salesmanemail, dev.sactrucks@gmail.com";
                                        $frmail = "warranty.claims@sactrucks.co.za";

                                        $tomail = "warrantees@sactrucks.co.za";
                                        $ccmail = "$salesmanemail";
                                        $header = "MIME-Version: 1.0" . "\r\n";
                                        $header .= "Content-type: text/html; charset=UTF-8" . "\r\n";
                                        $header .= "From: " . $frname . " <" . $frmail . ">\r\n";
                                        $header .= "Cc: " . $ccmail . PHP_EOL;
                                        $header .= "Bcc: dev@sactrucks.co.za" . PHP_EOL;

                                        $content = "
                                        <html>
                                        <head>
                                            <title>New Quality Return Submitted</title>
                                        </head>
                                        <body>
                                            <p>A new quality return with number, Q$return_nr, has just been submitted by $return_salesman at $return_branch.</p>
                                            <p>Please log in onto the <a href='https://sacmarketing.co.za'>SAC Internal Marketing</a> platform to view this quality return.</p>
                                        </body>
                                        </html>
                                        ";

                                        $subject = "$return_nr: New Quality Return Submitted";
                                        mail($tomail, $subject, $content, $header);
                                        /* End mail Notifications */

                                        // ATTACHMENT UPLOAD SECTION // name="fileToUpload" id="fileToUpload"
                                        $target_dir = "quality-uploads/";

                                        // IMAGE DOCUMENT
                                        $target_file = $target_dir . basename($_FILES["imageToUpload"]["name"]);
                                        $uploadOk = 1;
                                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                                        $newfilename = $return_nr . "_img." . $imageFileType;
                                        $newfile = $target_dir . $newfilename;

                                        // Check if image file is right format
                                        if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                                            $uploadnotice2 = "Sorry, only PDF, DOC, DOCX, JPG, JPEG or PNG files are allowed.";
                                            $uploadOk = 0;
                                        }
                                        // Check if file already exists
                                        if (file_exists($newfilename)) {
                                            $uploadnotice2 = "Sorry, file already exists.";
                                            $uploadOk = 0;
                                        }
                                        // Check file size
                                        if ($_FILES["imageToUpload"]["size"] > 5242880) {
                                            $uploadnotice2 = "Sorry, your file is too large. Maximum size is 5MB.";
                                            $uploadOk = 0;
                                        }
                                        // Check if $uploadOk is set to 0 by an error
                                        if ($uploadOk == 0) {
                                            //echo "Sorry, your file was not uploaded.";
                                            // if everything is ok, try to upload file
                                        } else {
                                            if (move_uploaded_file($_FILES["imageToUpload"]["tmp_name"], $newfile)) {
                                                $uploadnotice2 = "The file, <i>" . basename($_FILES["imageToUpload"]["name"]) . "</i>, has been uploaded as <b>$newfilename</b>.";
                                            } else {
                                                $uploadnotice2 = "Sorry, there was an error uploading your file.";
                                            }
                                        }
                                        // END IMAGE UPLOAD SECTION //

                                        // RFC DOCUMENT
                                        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                                        $uploadOk = 1;
                                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                                        $newfilename = $return_nr . "_rfc." . $imageFileType;
                                        $newfile = $target_dir . $newfilename;

                                        // Check if image file is right format
                                        if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                                            $uploadnotice = "Sorry, only PDF, DOC, DOCX, JPG, JPEG or PNG files are allowed.";
                                            $uploadOk = 0;
                                        }
                                        // Check if file already exists
                                        if (file_exists($newfilename)) {
                                            $uploadnotice = "Sorry, file already exists.";
                                            $uploadOk = 0;
                                        }
                                        // Check file size
                                        if ($_FILES["fileToUpload"]["size"] > 5242880) {
                                            $uploadnotice = "Sorry, your file is too large. Maximum size is 5MB.";
                                            $uploadOk = 0;
                                        }
                                        // Check if $uploadOk is set to 0 by an error
                                        if ($uploadOk == 0) {
                                            //echo "Sorry, your file was not uploaded.";
                                            // if everything is ok, try to upload file
                                        } else {
                                            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newfile)) {
                                                $uploadnotice = "The file, <i>" . basename($_FILES["fileToUpload"]["name"]) . "</i>, has been uploaded as <b>$newfilename</b>.";
                                            } else {
                                                $uploadnotice = "Sorry, there was an error uploading your file.";
                                            }
                                        }
                                        // END RFC UPLOAD SECTION //
                                ?>
                                        <div class="full sb ovfInit fl-l">
                                            <h2>Your Return Request was submitted successfully with number Q<?php echo $return_nr; ?></h2>
                                            <p><?php echo $uploadnotice . "<br>" . $uploadnotice2; ?></p>
                                        </div>
                                    <?php
                                    } else {
                                        echo "dit is in die else";
                                    ?>
                                        <div class="full sb ovfInit fl-l">
                                            <h2 class="red1">ERROR: Your quality return could not be submitted</h2>
                                            <p><?php echo $sql1 . "<br>" . mysqli_error($dbcon); ?></p>
                                        </div>
                                    <?php
                                    }
                                }


                                $qry = mysqli_query($dbcon, "SELECT branch FROM sacstaff WHERE id = '$rid' LIMIT 1;");
                                $user_branch = mysqli_fetch_assoc($qry)['branch'];
                                //$userbranch = mysqli_fetch_assoc($qry)['branch'];
                                //$qry = mysqli_query($dbcon, "SELECT bncode FROM branchcodes WHERE branch = '$userbranch' LIMIT 1;");
                                //$user_branch = mysqli_fetch_assoc($qry)['bncode'];
                                //$_COOKIE["sacsmlv"];
                                if (!isset($_POST['userbranch']) && $user_branch == "Head Office" ||  !isset($_POST['userbranch']) && $user_branch == "Broederstroom" ||  !isset($_POST['userbranch']) && $_COOKIE["sacsmlv"] == "72") { ?>
                                    <form method="post" action="/mysac/quality-form.php">
                                        <input type="hidden" name="showform" value="1">
                                        <div class="full sb ovfInit fl-l noprint">
                                            <div class="w380 ib">
                                                
                                                <div class="w130 ib">Select Branch:</div>
                                                
                                                <div class="w210 ib">
                                                    <select name="userbranch" class="input">
                                                        <?php
                                                       
                                                        $sql = "SELECT code,name FROM codes WHERE business = 'SAC Commercial Parts' AND type = 'Branch' OR business = 'SAC Commercial Parts' AND type = 'Warehouse' ORDER BY code ASC;";
                                                        //echo "'<script>console.log('" . $sql . "');</script>'";
                                                        $qry = mysqli_query($dbcon, $sql);
                                                        while ($row = mysqli_fetch_assoc($qry)) {
                                                            $bcodes = $row["code"];
                                                            $brnchs = $row["name"];
                                                            echo "<option value='$brnchs'>$bcodes - $brnchs</options>";
                                                          
                                                        }
                                                        $sql = "SELECT code,name FROM codes WHERE business = 'SAC Used Commercial Parts' AND type = 'Warehouse' ORDER BY code ASC;";
                                                        $qry = mysqli_query($dbcon, $sql);
                                                        while ($row = mysqli_fetch_assoc($qry)) {
                                                            $bcodes = $row["code"];
                                                            $brnchs = $row["name"];
                                                            echo "<option value='$brnchs'>$bcodes - $brnchs</options>";
                                                            
                                                        }
                                                        echo "'<script>console.log(' neee');</script>'";
                                                        
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib"><button type="submit" class="btn128" name="setbranch" value="selected" onclick="setBranch(); return false;">Select</button></div>
                                                <div class="w210 ib"></div>
                                            </div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                  
                                    if (isset($_POST['userbranch'])) {
                                        $userbranch = $_POST['userbranch'];
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
                                        echo $active101 .' or '.$active210;
                                        echo '<p><a href="/mysac/quality-form.php?prec=101" class="ib"><button class="btn128grey ' . $active101 . ' ib"><b>101</b></button></a> &emsp; <a href="/mysac/quality-form.php?prec=210" class="ib"><button class="btn128grey ' . $active210 . ' ib"><b>210</b></button></a></p>';
                                        //echo "<p>pre: $prec<br>pcd: $preclaimcode</p>";
                                    } else {
                                        $userbranch = $user_branch;
                                    }
                                    //$return_compny = $_COOKIE["sacsmcb"];
                                    $return_compny = "Trucks";

                                    $sql4 = "SELECT code FROM codes WHERE name = '$userbranch' LIMIT 1;";
                                    $res4 = mysqli_query($dbcon, $sql4);
                                    $row4 = mysqli_fetch_assoc($res4);
                                    $prereturncode = $row4["code"];
                                    //echo "<p>prc: $prereturncode</p>";

                                    $sql2 = "SELECT name, surname, departm FROM sacstaff WHERE ucode = '$ucode' LIMIT 1;";
                                    $res2 = mysqli_query($dbcon, $sql2);
                                    $row2 = mysqli_fetch_assoc($res2);
                                    $fname = $row2["name"];
                                    $lname = $row2["surname"];
                                    $deptm = $row2["departm"];
                                    $userfullname = $fname . " " . $lname;

                                    //if ( $return_compny == "LCV" || $return_compny == "Trucks" ){
                                    //    $returncpy = "return_compny = 'LCV' AND return_branch = '$userbranch' OR return_compny = 'Trucks' AND return_branch = '$userbranch'";
                                    //} else {
                                    $returncpy = "return_compny = '$return_compny' AND return_branch = '$userbranch'";
                                    //}

                                    $sql3 = "SELECT return_nr AS last_code FROM qualityreturn WHERE $returncpy ORDER BY return_nr DESC LIMIT 1;";
                                    $res3 = mysqli_query($dbcon, $sql3);
                                    $num3 = mysqli_num_rows($res3);
                                    if ($num3 > 0) {
                                        $row3 = mysqli_fetch_assoc($res3);
                                        $last_cd = sprintf('%07d', $row3["last_code"]);
                                        $return_nr = $last_cd + 1;
                                    } else {
                                        $return_nr = $prereturncode . "0001";
                                    }
                                    //echo "<p>lcd: $last_cd<br>cpy: $return_compny<br>ucd: $ucode<br>rcd: $return_ucode</p>";
                                ?>

                                    <form method="post" action="/mysac/quality-form.php" enctype="multipart/form-data">
                                        <input type="hidden" name="showform" value="1">
                                        <input type="hidden" name="retucode" value="<?php echo $ucode; ?>">
                                        <div class="full sb ovfInit fl-l noprint">
                                            <div class="w380 ib">
                                                <div class="w130 ib">Branch Return Nr:</div>
                                                <div class="w210 ib"><input type="hidden" name="return_compny" value="<?php echo $return_compny; ?>"><input type="hidden" class="input" name="return_nr" value="<?php echo sprintf('%07d', $return_nr); ?>">Q<?php echo sprintf('%07d', $return_nr); ?></div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib">Return Date:</div>
                                                <div class="w210 ib"><input type="hidden" class="input" name="return_date" value="<?php echo $today; ?>"><?php echo $today; ?></div>
                                            </div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit fl-l">
                                            <div class="w380 ib">
                                                <div class="w130 ib">Branch:</div>
                                                <div class="w210 ib"><input type="hidden" class="input" name="return_branch" value="<?php echo $userbranch; ?>"><?php echo $userbranch; ?></div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib">Salesman:</div>
                                                <div class="w210 ib"><input type="hidden" class="input" name="return_salesman" maxlength="64" value="<?php echo $userfullname; ?>" required><?php echo $userfullname; ?></div>
                                            </div>
                                            <div class="full h16 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit fl-l">
                                            <div class="w380 ib">
                                                <div class="w130 ib">Invoice Nr:*</div>
                                                <div class="w210 ib"><input type="text" class="input" name="invoice_nr" required></div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib"></div>
                                                <div class="w210 ib"></div>
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
                                                <div class="w210 ib"><input type="number" class="input" name="part_qty" min="1" max="9999" required></div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib">Part Number:*</div>
                                                <div class="w210 ib"><input type="text" class="input" name="part_nr" maxlength="16" required></div>
                                            </div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w800 ib">
                                                <div class="w130 ib">Description:</div>
                                                <div class="w590 ib"><input type="text" class="input" name="part_desc" maxlength="64"></div>
                                            </div>
                                            <div class="full h16 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w800 ib">
                                                <div class="w130 ib">Part Checked:*</div>
                                                <div class="w590 ib" id="selectbuttons">
                                                    <label class="ready"><input type="radio" class="chkgrp" name="part_checked" value="Yes" <?php if ($part_checked == "Yes") {
                                                                                                                                                echo "selected";
                                                                                                                                            } ?> required><span>Yes</span></label>&emsp;
                                                    <label class="ready"><input type="radio" class="chkgrp" name="part_checked" value="No" <?php if ($part_checked == "No") {
                                                                                                                                                echo "selected";
                                                                                                                                            } ?> required><span>No</span></label> &emsp; (Checked at branch?)
                                                </div>
                                            </div>
                                            <div class="full h16 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w380 ib">
                                                <div class="w130 ib va-top">Quality Issue:*</div>
                                                <div class="w210 ib">
                                                    <input type="checkbox" name="qi01" value="Incorrect Part Number. ">Incorrect Part Number</input><br>
                                                    <input type="checkbox" name="qi02" value="Parts Missing From Kit. ">Parts Missing From Kit</input><br>
                                                    <input type="checkbox" name="qi03" value="Quality. ">Quality</input><br>
                                                    <input type="checkbox" name="qi04" value="Incorrect Size. ">Incorrect Size</input><br>
                                                    <input type="checkbox" name="qi05" value="Part Leaking. ">Part Leaking</input>
                                                </div>
                                            </div>
                                            <div class="w380 ib">
                                                <div class="w130 ib"></div>
                                                <div class="w210 ib">
                                                    <input type="checkbox" name="qi06" value="Incorrect Packaging. ">Incorrect Packaging</input><br>
                                                    <input type="checkbox" name="qi07" value="Damaged Or Scratch Part. ">Damaged Or Scratch Part</input><br>
                                                    <input type="checkbox" name="qi08" value="Faulty Part. ">Faulty Part</input><br>
                                                    <input type="checkbox" name="qi09" value="Fitment Issues. ">Fitment Issues</input><br>
                                                    <input type="checkbox" name="qi10" value="Other.">Other (Describe Below)</input>
                                                </div>
                                            </div>
                                            <div class="full h16 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w800 ib">
                                                <div class="w130 ib va-top">Reason for Quality Return:*</div>
                                                <div class="w590 ib"><textarea class="textare2" name="return_notes" maxlength="512" placeholder="Insert detailed description of the fault/error/mistake you noticed" required></textarea></div>
                                            </div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w380 ib">
                                                <div class="w130 ib">Upload Image:</div>
                                                <div class="w210 ib"><input type="file" name="imageToUpload" id="imageToUpload" accept=".jpe, .jpg, .png, .doc, .docx, .pdf"></div>
                                            </div>
                                            <div class="w380 ib">3MB Maximum File Size</div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="w380 ib">
                                                <div class="w130 ib">Upload RFC:*</div>
                                                <div class="w210 ib"><input type="file" name="fileToUpload" id="fileToUpload" accept=".jpe, .jpg, .png, .doc, .docx, .pdf" required></div>
                                            </div>
                                            <div class="w380 ib">3MB Maximum File Size</div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>
                                        <div class="full sb ovfInit">
                                            <div class="full h16 ovfInit">&nbsp;</div>
                                            <div class="w380 ib txt-ctr">
                                                <button type="submit" class="btn128" name="return_do" value="request">Send Request</button>
                                            </div>
                                            <div class="w380 ib txt-ctr">
                                                <!--button type="reset" class="btn128">Clear Form</button-->
                                            </div>
                                            <div class="full h4 ovfInit">&nbsp;</div>
                                        </div>

                                    </form>
                                <?php } ?>
                            </div>
                        </div>

                        <p><a href="https://sacmarketing.co.za/mysac/quality-form.php?logout=1">Log Out</a></p>

                    </body>

                    </html>

    <?php
                } else {

                    
                    echo '<p><b>You are not allowed to currently see this page</b></p>';
                    echo '<p><a href="https://sacmarketing.co.za/mysac/quality-form.php?logout=1">Log Out</a></p>';
                }
                
                mysqli_close($dbcon);
            }
        }
    } catch (Exception $e) {
        echo "Error" . $e->getMessage();
    }


    ?>