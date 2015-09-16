<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css/odometer-theme-minimal.css">
<title>Dominos.fr</title>
</head>

<body>
<style>
body { background:none transparent; }
#compteur {
    background: url('img/compteur_bg.png') no-repeat;
    width: 90px;
    height: 28px;
    padding: 0;
    text-align: right;
    letter-spacing: 8px;
    font-size: 22px;
    font-weight: 600;
    margin: 0 5px 0 8px;
}

#compteur_wrapper {margin:auto;display:block;font-family: Arial, Helvetica, sans-serif;font-size: 18px;text-align: center;}
</style>
<div id="compteur_wrapper">Il reste<span id="compteur"></span> codes</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.7/odometer.min.js"></script>

<script>

function doAjax() {
    var root_path = "http://testing.dominos.fr/op/vod/dominos/web/";
    var interval = 2000;
    el = document.querySelector("#compteur");
    $.ajax({
       url : root_path + 'compteur.php',
       type : 'GET',
       dataType : 'json',
       success : function(data, statut){
            compteur = data.compteur;
                od = new Odometer({
                    el: el,
                    value: compteur,
                    format: "dddd",
                    theme: "minimal"
                });
	el.innerHTML = data.compteur
       },
       error : function(result, statut, erreur){
         console.log(result);
         console.log(erreur.message);

       },
       complete: function (data) {
            // Schedule the next
            setTimeout(doAjax, interval);
        }
    });

}
var interval = 1000;
setTimeout(doAjax, interval);
  </script>

</html>
