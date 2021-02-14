<?php
  session_start();
  require('app/class/Config.inc.php');

  if(isset($_GET['logout']) && $_GET['logout']==1): 
    $_SESSION = array();
    $_SESSION['user_logged'] = false;
    session_destroy();
    header('Location: index.php');
  endif;

  $corJanelaMensagem = 'bg-branco';
  $txCor = 'tx-preto';
  $textoJanela = 'Informe Usuário e senha para logar!';
  $ico = getSvgf('flag', '63', $txCor);
  
  $_SESSION['user_logged'] = $_SESSION['user_logged'] ?? false;
  
  if(isset($_POST['AdminLogin'])):
    $postU = $_POST['user'] ?? null;
    $postP = md5($_POST['senha']) ?? null;
    $read = new Read;
    $read->ExeRead("adm_log", "WHERE user_email = :e AND user_password = :p", "e={$postU}&p={$postP}");
    if($read->getResult()):
      $_SESSION['id']             = $read->getResult()[0]['id'];
      $_SESSION['user_name']      = $read->getResult()[0]['user_name'];
      $_SESSION['user_sobrenome'] = $read->getResult()[0]['user_sobrenome'];
      $_SESSION['user_email']     = $read->getResult()[0]['user_email'];
      $_SESSION['user_password']  = $read->getResult()[0]['user_password'];
      $_SESSION['user_logged']    = $read->getResult()[0]['user_logged'];
      
      $corJanelaMensagem = 'bg-verde';
      $txCor = 'tx-preto';
      $textoJanela = 'Logado';
      $ico = getSvgf('asterisk', '63', $txCor);
    else:
      $_SESSION = array();
      $corJanelaMensagem = 'bg-laranja';
      $txCor = 'tx-preto';
      $textoJanela = 'Erro: Usuário ou senha não encontrado!';
      $ico = getSvgf('asterisk', '63', $txCor);
      $_SESSION['user_logged'] = false;
    endif;
    
  else:
    if($_SESSION['user_logged']):
    $corJanelaMensagem = 'bg-branco';
    $txCor = 'tx-preto';
    $textoJanela = 'Você já esta logado(a)!!';
    $ico = getSvgf('asterisk', '63', $txCor);
    endif;
  endif;

  function getSvgf ($qual, $tamanho, $cor){
    $comum = "<svg width=\"{$tamanho}\" height=\"{$tamanho}\" viewBox=\"0 0 16 16\" class=\"{$cor}\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
      <use xlink:href=\"app/imgs/bootstrap-icons.svg#{$qual}\"/>
    </svg>";
    return $comum;
  }

?>
<!doctype html>
<html lang="en">
  <head><meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Encantos Finanças</title>
    <!-- Bootstrap core CSS -->
    <link href="app/assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="app/css/offcanvas.css" rel="stylesheet">

  </head>

<body class="bg-body">
  <main role="main" class="container">
  <?php
  if(isset($_SESSION) && !$_SESSION['user_logged']):
    echo"
    <div class=\"alert {$corJanelaMensagem} mt-3 shadow-sm\" role=\"alert\">
        <div class=\"d-flex\">
          <div class=\"p-4 flex-shrink-1\">
          {$ico}
          </div>
          <div class=\"p-2 w-100\">
            <h5 class=\"alert-heading {$txCor}\">Sistema</h5>
            <hr>
            <p class=\"{$txCor}\">{$textoJanela}</p>
          </div>
        </div>
      </div>
        <div class=\"my-3 p-3 bg-white rounded shadow-sm\">
          <h6 class=\"border-bottom border-gray pb-2 mb-0\">Digite suas informações de Acesso!!!</h6>

            <div class=\"media pt-3\">
              <div class=\"col-md-auto\">
              <form class=\"mt-4\" id=\"formid\" method=\"post\" enctype=\"multipart/form-data\">
                <div class=\"panel-body\">
                  <fieldset>
                    <div class=\"form-group\">
                    <label for=\"user\">Email</label>
                      <input class=\"form-control\" placeholder=\"Usuário\" name=\"user\" type=\"text\" autofocus>
                    </div>
                    <div class=\"form-group\">
                    <label for=\"senha\">Senha</label>
                      <input class=\"form-control\" placeholder=\"Senha\" name=\"senha\" type=\"password\">
                    </div>
                  </fieldset>
                  <hr>
                  <button type=\"submit\" class=\"form-btn\" name=\"AdminLogin\" value=\"Logar\">Entrar</button>
                </div>
              </form>
              </div>
            </div>
          </div>
    ";
  else:
    echo "
      <div class=\"alert {$corJanelaMensagem} mt-3 shadow-sm\" role=\"alert\">
        <div class=\"d-flex\">
          <div class=\"p-4 flex-shrink-1\">
          {$ico}
          </div>
          <div class=\"p-2 w-100\">
            <h5 class=\"alert-heading {$txCor}\">Sistema</h5>
            <hr>
            <p class=\"{$txCor}\">{$textoJanela}</p>
            <a class=\"btn btn-primary\" href=\"estoque/\" role=\"button\">Entrar no Sistema</a>
          </div>
        </div>
      </div>
    ";
  endif;?>
     

      <small class="d-block text-right mt-3">
        <a href="#">@ Aroni Souza</a>
      </small>
    </div>
  </main>
  <script src="app/java/jquery-3.5.1.slim.min.js"></script>
  <script src="app/assets/dist/js/bootstrap.bundle.min.js"></script>
  <script src="app/java/offcanvas.js"></script>

  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });
  </script>
</body>
</html>