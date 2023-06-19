<?php
require_once "meta.php";

// Nome da página
$areaAdmin = 'artigos';

// Requires
require_once 'lib/autoload.php';
require_once "lib/utilidades.php";
require_once 'classe/bo/artigosBO.php';
require_once 'classe/vo/artigosVO.php';

// Instanciamentos

$vo = new artigosVO();
$bo = new artigosBO();
// $catBO = new classe\bo\GenericoBO('categoria');

$data = new DateTime('now');
$hoje = $data->format('Y-m-d');


// echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';

// Cadastra um novo post ("diferencial")
if (isset($_POST['adicionar'])) {
    
    $URL="./artigos_Add.php";
    $vo = new artigosVO();
    $bo = new artigosBO();


    // Verifica se algum dos campos obrigatórios está vazio
    $errors = utilidades::checkEmptyFields(
                    array('titulo','descricao'),
                    $_POST
                );

    $vo->setTitulo($_POST['titulo']);
    $vo->setDescricao($_POST['descricao']);

    
    if ($bo->Add($vo)) {
     $_SESSION['msgSuccess'] = 'Artigo cadastrado com sucesso!';
    } else {
        $_SESSION['msgError'] = 'Dados não inseridos';
        echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
        exit();
    }
}
  
if (isset($_POST["editar"])) {
      // Verifica se algum dos campos obrigatórios está vazio
    $URL="./artigos.php";
      $errors = utilidades::checkEmptyFields(
                      array('titulo','descricao'),
                      $_POST
               );      

    $vo->setId($_POST['id']);
    $vo->setTitulo($_POST['titulo']);
    $vo->setDescricao($_POST['descricao']);

    
    $bo->Edit($vo);
    $_SESSION['msgSuccess'] = 'Artigo editado com sucesso.';
}

if (isset($_GET['excluir'])) {
    $token = $_GET['excluir'];
    $id = $_GET['id'];

    $bo->Delete($id, $token);
    $_SESSION['msgSuccess'] = 'Artigo editado com sucesso.';

    header("Location: artigos.php");
    exit();
}

//-----------------------
$imgPath = 'style/img/';

$artigos = $bo->GetAll();

if (isset($_SESSION['msgError'])) {
    $msgErro = $_SESSION['msgError'];
}
if (isset($_SESSION['msgSuccess'])) {
    $msgSucesso = $_SESSION['msgSuccess'];
}
?>


<script>
    function excluir(token, id) {
        if (confirm("Tem certeza que deseja excluir esta Artigo?")) {
            window.location.href = "artigos.php?excluir="+token+"&id="+id;
        }
    }
    $(document).ready(function(){
        var error = "<?php print($msgErro)?>";
        if (error != "") {
            $('.msgError').show();
        }
        var success = "<?php print($msgSucesso)?>";
        if (success != "") {
            $('.msgSuccess').show();
        }
    });
</script>

<link rel="stylesheet" type="text/css" href="style/css/artigos.css" />

<script type="text/javascript" src="js/artigo.js"></script>
</head>
<body>
    <?php include("header.php"); ?>

    <main role="main" class="container-fluid">

        <div class="col-md-10 offset-md-1">

            <div class="row">
                <div id="header1" class="col p-4 text-center justify-content-center">
                    <h2>Artigos</h2>
                    <hr/>
                </div>
            </div>
            <div class="row">
                <div id="header2" class="col-12 p-2 text-center">
                    <div class="btn-group text-center">
                        <!-- <a class="btn btn-primary active" href="seguradoras.php">Ver Seguradoras</a> -->
                        <a class="btn btn-primary" href="artigos_Add.php">Adicionar Artigo</a>
                    </div>
                </div>
            </div>
            <div class="msgError col p-4 text-center">
                <label><?=$msgErro?></label>
            </div>
            <div class="msgSuccess col p-4 text-center">
                <label><?=$msgSucesso?></label>
            </div>
            <div class="row">
                <div class="col-12 p-4 full-page">

                    <div class="m-3">
                        <table class="data-table table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th scope="col">Imagem Capa Artigo</th> -->
                                    <th scope="col">Imagem Artigo</th>
                                    <th scope="col">Título Artigo</th>
                                    <th scope="col">Descrição Artigo</th>
                                    <th scope="col">Opções</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Exibe teste
                                    if(!empty($artigos)){
                                    foreach ($artigos as $artigo):?>
                                        <tr>
                                            <!-- <td style="text-align:center;">
                                                <img src="<?=$imgPath.$artigo->capa?>" alt="<?=urldecode($artigo->titulo)?>" style="max-width:100px;height:auto;">
                                            </td> -->
                                            <td style="text-align:center;">
                                                <img src="<?=$imgPath.$artigo->imagem?>" alt="<?=urldecode($artigo->titulo)?>" style="max-width:100px;height:auto;">
                                            </td>
                                            <td>
                                                <p>
                                                    <b id="titulo"><?=urldecode($artigo->titulo)?></b>
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                    <b id="texto">
                                                    <?php
                                                        $texto = urldecode($artigo->descricao);
                                                        $tam = strlen($texto); // Tamanho do texto.
                                                        $max = 80; // exibe 80 primeiros caracteres do texto.

                                                        if($tam > $max) // Se o texto for maior do que 80, retira o restante.
                                                        {
                                                            echo substr($texto, 0, $max - $tam);
                                                        
                                                        } else {
                                                            echo $texto;
                                                        }
                                                        
                                                    ?>
                                                    </b>
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group-horizontal">
                                                    <a href="artigos_Edit.php?id=<?=$artigo->id?>" class="btn btn-sm btn-secondary">Editar</a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="excluir('<?=md5($artigo->id)?>', '<?=$artigo->id?>');">
                                                        Excluir
                                                    </button>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                <?php endforeach; }else{?>
                            </tbody>
                        </table>
                        <hr/>
                        <div class="row">
                            <div id="header2" class="col-12 p-2 text-center">
                                <div class="btn-group text-center">
                                    <a class="btn btn-primary" href="artigos_Add.php">Adicionar Artigo</a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <hr/>
                    </div>
                    <?php include "footer.php"; ?>
                </div>
            </div>
        </div>

    </main>
<script>
    function excluir(token, id) {
        if (confirm("Tem certeza que deseja excluir esta Artigo?")) {
            window.location.href = "artigos.php?excluir="+token+"&id="+id;

        }
    }

</script>
</body>
</html>