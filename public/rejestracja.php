<?php
    include(__DIR__.'/../include/config/config_init.php');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="include/css/default.css" rel="stylesheet">
    <link href="include/css/main_menu.css" rel="stylesheet">
    <link href="include/css/main_footer.css" rel="stylesheet">
    <link href="include/css/sites/default.css" rel="stylesheet">
    <link href="include/css/sites/style_rejestracja.css" rel="stylesheet">
    <?php include(__DIR__."/../include/portal/metadata.php"); ?>

</head>
<body>
    <?php include(__DIR__."/../include/portal/main_menu.php"); ?>
    <div id="welcome_sector">
        <img src="img/background.gif" draggable="false"/>
        <h1>Zarejestruj się</h1>
    </div>
    <br style="clear: both;" />
    <div id="content">
        <h2 style="text-align: center; color: rgb(0, 35, 50);">Formularz rejestracji</h2>
        <form method="POST" action="app/process.php?r=register" style="display: flex; flex-direction: column; width: 40%; margin: auto; margin-top: 5vmax; margin-bottom: 5vmax;">
            <fieldset class="registration_input" id="r_i_name" >
                <legend>Imię:</legend>
                <input type="text" name="name" onChange="validate_data();" />
            </fieldset>
            <fieldset class="registration_input" id="r_i_surname" >
                <legend>Nazwisko:</legend>
                <input type="text" name="surname" onChange="validate_data();" />
            </fieldset>
            <fieldset class="registration_input" id="r_i_org" >
                <legend>Szkoła, klasa:</legend>
                <input type="text" name="org" onChange="validate_data();" />
            </fieldset>
            <fieldset class="registration_input" id="r_i_username" style="margin-top: 2vmax;">
                <legend>Nazwa uzytkownika:</legend>
                <input type="text" name="username" onChange="validate_data();" />
            </fieldset>
            <fieldset class="registration_input" id="r_i_mail" style="margin-top: 2vmax;">
                <legend>Adres e-mail:</legend>
                <input type="text" name="mail" onChange="validate_data();" />
            </fieldset>
            <fieldset class="registration_input" id="r_i_pass" >
                <legend>Hasło:</legend>
                <input type="password" name="pass" onChange="validate_data();"/>
            </fieldset>
            <fieldset class="registration_input" id="r_i_pass_repeat" >
                <legend>Powtórz hasło:</legend>
                <input type="password" name="pass_repeat" onChange="validate_data();" />
            </fieldset>
            <fieldset id="r_i_checkbox" style="margin-top: 2vmax;">
                <legend>Wymagane zgody:</legend>
                <input type="checkbox" id="agree_1" name="agree_1" onChange="validate_data();" required/>
                <label for="agree_1">Wyrażam zgodę przetwarzanie moich danych osobowych przez podmiot "<?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT webiste"); } ?>" na realizacji jego zadań. Jestem świadom_a wszelkich przysługujących mi praw z tym związanych.</label><br />
                <br />
                <input type="checkbox" id="agree_2" name="agree_2" onChange="validate_data();" required/>
                <label for="agree_2">Wyrażam zgodę mój udział/udział mojego dziecka w wydarzeniu "<?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT webiste"); } ?>".</label><br />
                <br />
                <p>Jestem świadom_a, iż podanie moich danych osobowych i wyrażenie powyższych zgód jest całkowicie dobrowolne. Brak zgody na przetwarzanie danych osobowych jest równoznaczny z rezygnacją z uczestnictwa w wydarzeniu "<?php if(isset($general_title)) { echo($general_title); } else { echo("This is my first ESIT webiste"); } ?>".
            </fieldset>
            <input type="submit" value="Zarejestruj się" class="registration_button" id="r_i_registration_button" style="display: none;"/>
            <fieldset id="r_i_validation" style="margin-top: 2vmax;">
                <legend>Walidacja danych</legend>
                <b>Twoja nazwa użytkownika...</b>
                <p class="data-invalid" id="c_1">...musi mieć co najmniej 6 znaków</p>
                <p class="data-invalid" id="c_2">...musi być unikalna</p>
                <p class="data-invalid" id="c_10">...nie może zawierać znaków specjalnych i diaktrycznych</p>
                <b>Twoje hasło...</b>
                <p class="data-invalid" id="c_3">...musi mieć co najmniej 8 znaków</p>
                <p class="data-invalid" id="c_4">...musi mieć co najmniej 1 znak specjalny</p>
                <p class="data-invalid" id="c_5">...musi mieć co najmniej 1 dużą literę</p>
                <p class="data-invalid" id="c_6">...musi mieć co najmniej 1 cyfrę</p>
                <p class="data-invalid" id="c_7">...musi się zgadzać z tym podanym w drugiej rubryce</p>
                <b>Twój adres mailowy...</b>
                <p class="data-invalid" id="c_8">...musi być unikalny</p>
                <b>Twoje imię, nazwisko i szkoła z klasą lub organizacja do której przynależysz...</b>
                <p class="data-invalid" id="c_9">...muszą zostać przez Ciebie podane</p>
                <script>
                    let e = 0;
                    function check_data()
                    {
                        if(e>10)
                        {
                            document.getElementById('r_i_registration_button').style.display = 'block';
                        } else {
                            document.getElementById('r_i_registration_button').style.display = 'none';
                        }
                    }

                    function validate_data()
                    {
                        e = 0;
                        if(document.querySelector('input[name="username"]').value.length>5)
                        {
                            e++;
                            document.getElementById('c_1').classList.add("data-valid");
                            document.getElementById('c_1').classList.remove("data-invalid");

                            const xhttp = new XMLHttpRequest();
                            xhttp.onload = function() {
                                if(this.responseText=="OK")
                                {
                                    e++;
                                    document.getElementById('c_2').classList.add("data-valid");
                                    document.getElementById('c_2').classList.remove("data-invalid");
                                } else {
                                    document.getElementById('c_2').classList.add("data-invalid");
                                    document.getElementById('c_2').classList.remove("data-valid");
                                }
                                check_data();
                            }
                            xhttp.open("GET", "app/process.php?r=registration_is_unique&value=" + document.querySelector('input[name="username"]').value);
                            xhttp.send();
                        } else {
                            document.getElementById('c_1').classList.add("data-invalid");
                            document.getElementById('c_1').classList.remove("data-valid");
                        }

                        if(document.querySelector('input[name="pass"]').value.length>7)
                        {
                            e++;
                            document.getElementById('c_3').classList.add("data-valid");
                            document.getElementById('c_3').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_3').classList.add("data-invalid");
                            document.getElementById('c_3').classList.remove("data-valid");
                        }

                        var specialchars = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
                        if(specialchars.test(document.querySelector('input[name="pass"]').value))
                        {
                            e++;
                            document.getElementById('c_4').classList.add("data-valid");
                            document.getElementById('c_4').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_4').classList.add("data-invalid");
                            document.getElementById('c_4').classList.remove("data-valid");
                        }

                        var capitalchars = /[A-Z]/g;
                        if(capitalchars.test(document.querySelector('input[name="pass"]').value))
                        {
                            e++;
                            document.getElementById('c_5').classList.add("data-valid");
                            document.getElementById('c_5').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_5').classList.add("data-invalid");
                            document.getElementById('c_5').classList.remove("data-valid");
                        }

                        var numberchars = /[0-9]/g;
                        if(numberchars.test(document.querySelector('input[name="pass"]').value))
                        {
                            e++;
                            document.getElementById('c_6').classList.add("data-valid");
                            document.getElementById('c_6').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_6').classList.add("data-invalid");
                            document.getElementById('c_6').classList.remove("data-valid");
                        }

                        if(document.querySelector('input[name="pass"]').value==document.querySelector('input[name="pass_repeat"]').value && document.querySelector('input[name="pass"]').value.length>7)
                        {
                            e++;
                            document.getElementById('c_7').classList.add("data-valid");
                            document.getElementById('c_7').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_7').classList.add("data-invalid");
                            document.getElementById('c_7').classList.remove("data-valid");
                        }

                        if(document.querySelector('input[name="mail"]').value.length>3)
                        {
                            const xhttp = new XMLHttpRequest();
                            xhttp.onload = function() {
                                if(this.responseText=="OK")
                                {
                                    e++;
                                    document.getElementById('c_8').classList.add("data-valid");
                                    document.getElementById('c_8').classList.remove("data-invalid");
                                } else {
                                    document.getElementById('c_8').classList.add("data-invalid");
                                    document.getElementById('c_8').classList.remove("data-valid");
                                }
                                check_data();
                            }
                            xhttp.open("GET", "app/process.php?r=registration_is_unique&value=" + document.querySelector('input[name="mail"]').value);
                            xhttp.send();
                        } else {
                            document.getElementById('c_8').classList.add("data-invalid");
                            document.getElementById('c_8').classList.remove("data-valid");
                        }

                        if(document.querySelector('input[name="name"]').value.length>1 && document.querySelector('input[name="surname"]').value.length>1 && document.querySelector('input[name="org"]').value.length>3)
                        {
                            e++;
                            document.getElementById('c_9').classList.add("data-valid");
                            document.getElementById('c_9').classList.remove("data-invalid");
                        } else {
                            document.getElementById('c_9').classList.add("data-invalid");
                            document.getElementById('c_9').classList.remove("data-valid");
                        }

                        var specialchars = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~ążźćśółńę]/;
                        if(specialchars.test(document.querySelector('input[name="username"]').value))
                        {
                            document.getElementById('c_10').classList.add("data-invalid");
                            document.getElementById('c_10').classList.remove("data-valid");
                        } else {
                            e++;
                            document.getElementById('c_10').classList.add("data-valid");
                            document.getElementById('c_10').classList.remove("data-invalid");
                        }

                        if(document.querySelector('input[name="agree_1"]').checked && document.querySelector('input[name="agree_2"]').checked)
                        {
                            e++;
                        }
                        check_data();
                    }
                </script>
            </fieldset>
        </form>
    </div>
    <br style="clear: both;" />
    <br />
    <?php include(__DIR__.'/../include/portal/main_footer.php'); ?>
</body>
</html>