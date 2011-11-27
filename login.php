<?php
/*
 * Cru Doctrine
 * Login
 * Keith Roehrenbeck | Campus Crusade for Christ
 */

try {

    //get values
    $submit     = isset($_POST['submit'])   ? true                  : false;
    $ajax       = isset($_POST['ajax'])     ? true                  : false;

    $email      = isset($_POST['email'])    ? $_POST['email']       : '';
    $pass       = isset($_POST['pass'])     ? $_POST['pass']        : '';
    $redir      = isset($_POST['redir'])    ? $_POST['redir']       : '';

    $errors     = isset($_POST['errors'])   ? $_POST['errors'] : '';

    //check for form submission
    if($submit) {    //form was submitted, process data

	    //initialize pdo object
	    $db = new PDO('mysql:host=crudoctrine.db.6550033.hostedresource.com;port=3306;dbname=crudoctrine', 'crudoctrine', 'D6LLd2mxU6Z34i');

        //prepare query
        $query = $db->prepare("SELECT * FROM user WHERE Email = ? AND Password = ?");
        $query->bindValue(1, $email,    PDO::PARAM_STR);
        $query->bindValue(2, $pass,     PDO::PARAM_STR);

        //get results
        $query->execute();
        $result = $query->fetchAll();

        //check result to verify login
        if(count($result) > 0){ //success
            //log user in
            session_start();
            $_SESSION['email']  = $email;
            $_SESSION['fname']  = $result[0]['FName'];
            $_SESSION['lname']  = $result[0]['LName'];
            $_SESSION['type']   = $result[0]['Type'];
            $_SESSION['region'] = $result[0]['Region'];
            $_SESSION['loc']    = $result[0]['Loc'];

            //$_SESSION['documentRoot']  = $_SERVER['REQUEST_URI'];

            //if ajax, return user attributes as xml
            if ($ajax) {
                header ("Location: /crudoctrine/");
            } else {
                header ("Location: /crudoctrine/");
            }

        } else { //fail
            //return errors
            $errors .= 'Login failed. Please check your email and password.';

            //if ajax, return error
            if ($ajax) {

                echo 'error';
                exit();

            }
        }
        $db = null;
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>

<link rel="stylesheet" type="text/css" media="screen" href="/crudoctrine/login.css" />

<div id="login">

    <form action="login.php" method="post">

        <fieldset id="credentials">
            <legend>Please Login</legend>
            <div>
                <label>Email</label><input type="text" name="email" value="<?php echo $email; ?>" /><a class="required"></a>
            </div>
            <div>
                <label>Password</label><input type="password" name="pass" value="<?php echo $pass; ?>" /><a class="required"></a>
            </div>
        </fieldset>

        <fieldset id="feedback">
            <div id="errors"><?php echo $errors; ?></div>
        </fieldset>

        <button type="submit" name="submit" class="ui-state-default ui-corner-all">Login<span class="ui-icon ui-icon-circle-triangle-e"></span></button>

    </form>

</div>

<script type="text/javascript">

    //hide submit button
//    $(function() {
//        $('form button:[name=submit]').hide();
//    });

    //validate form submission
    $('#login form').submit(function(){
        var submit = false;
        var errors = '';

        if ($('#login input:[name=email]').val().length == 0){
            $('#login input:[name=email]').css('border-color', 'orange').siblings('a').css('display','inline-block');
            errors += '<div>Please enter your email.</div>';
        }

        if ($('#login input:[name=pass]').val().length == 0) {
            $('#login input:[name=pass]').css('border-color', 'orange').siblings('a').css('display','inline-block');
            errors += '<div>Please enter your password.</div>';
        }

        if (errors !== ''){
           $('#login #errors').html(errors);
           submit = false;
        } else {
           submit = true;
        }

        if(submit){
            $.ajax({
                url: '/crudoctrine/login.php',
                type: 'POST',
                data: {
                    ajax       : true,
                    submit     : true,
                    email      : $('form input:[name=email]').val(),
                    pass       : $('form input:[name=pass]').val()
                },
                dataType: "html",
                success: function(msg){

                    if(msg != 'error') {
                        $('#loginbox  #login').click();
                        $('#header').html($(msg).find('#header').html());
                    } else {
                        $('#login #errors').html('<div>Login failed. Please check your email and password.</div>')
                    }

                }
            });
        }

        return false;

    });

    //jquery class interaction states

    $('button').addClass('ui-state-default');

    $('.ui-state-default').hover(
        function(){
            $(this).addClass("ui-state-hover");
        },
        function(){
            $(this).removeClass("ui-state-hover");
        }
    );

</script>
