<!DOCTYPE html>
    <html lang="en">
    <head>
        <script type='text/javascript'> if(history.replaceState) history.replaceState({}, "", "/"); // show only root url</script>
        <title>My SAC Internal</title>
        <meta charset="UTF-8">
        <meta name="author" content="Jan van der Westhuizen">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/mysac/mysac01.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <style>
            #login{width:328px;margin: 0 auto}
        </style>
    </head>
    <body>
        <section id="login" class="txt-ctr">
            <h1>SAC Marketing Portal</h1>
            <div class="h8"></div>

            <?php if ( isset($msg) && !empty($msg) ){ echo "<p>$msg</p>"; } else { echo "<p> </p>"; } ?>

            <form id="loginform" action="<?php echo $thisfile; ?>" method="post">
				<label>Username: </label><br>
				<input required name="username" id="username" class="input username" type="text" placeholder="just your name, not like MySAC" autofocus>
				<div class="h32"> </div>

				<label>Password: </label><br>
				<input required name="password" class="input" type="password" >
                <div class="txt-rhs link"><a href="mysac/loginpage-password-request.php?this=<?php echo $thisfile; ?>">Forgot your password?</a></div>
				<div class="h32"> </div>

                <div id="uncheck" class="txt-ctr"> </div>
                <div class="h16"> </div>

				<button class="btnLogin" type="submit" name="submit" value="login">LOGIN</button>
                <div class="h16"> </div>

				<a href="/mysac/request-login.php" target="_blank"><button class="btnLogin" type="button">REQUEST LOGIN</button></a>
            </form>
        </section>
        <div class="h64"> </div>

        <script>
            //$('#loginform').keydown(function(e) {
            //    var key = e.which;
            //    if (key == 13) { // As ASCII code for ENTER key is "13"
            //        $('#loginform').submit();
            //        //e.preventDefault();
            //        return false;
            //    }
            //});

            function getResults(){
                var thisun = $('#username').val();
                var dataString = 'thisun='+thisun;

                $.ajax({ 
                    type: "POST",
                    url: "/mysac/loginpage-check.php",
                    data: dataString,
                    cache: false,
                    success: function(result){
                        //$("#uncheck").html(dataString);
                        echo reset;
                        if ( result == 'good' ){
                            showRes = '<span style="color:#26e326">Username exist</span>';
                            $('.username').css("border-bottom","2px solid #26e326").css("background-color","#ccffcc");
                        }
                        else if ( result == 'bad' ){
                            showRes = '<span style="color:#e32429">Invalid username</span>';
                            $('.username').css("border-bottom","2px solid #e32429").css("background-color","#ececec");
                        }
                        $("#uncheck").html(showRes);
                    },
                    error: function(xhr){
                        $('#uncheck').html("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
                return false;
            }

            if ( $('#username').val().length > 0 ){
                getResults();
            }

            $('#username').on('keyup',function(){
                if ( $('#username').val().length > 0 ){
                    getResults();
                }
            });
        </script>
    </body>
</html>