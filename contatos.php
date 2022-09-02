<DOCTYPE Html>
    <html lang="pt-br">

    <head>

        <meta charset="utf-8">

        <title>Agenda de Contatos</title>

        <style>
            table,
            th,
            td {
                border: 1px solid black;
            }
        </style>
    </head>

    <body>

        <h1>AGENDA DE CONTATOS</h1>

        <hr>
        <form method="POST">
            <label for="fnome">Nome:</label>
            <input type="text" id="nome" placeholder="Nome Sobrenome" value="<?php echo isset($_REQUEST["nome"]) ? $_REQUEST["nome"] : ''; ?>" name="nome">
            <label for="femail" >E-mail:</label>
            <input type="email" id="email" placeholder="Email@Provedor.com" name="email" value="<?php echo isset($_REQUEST["email"]) ? $_REQUEST["email"] : ''; ?>">
            <label for="fcelular">Celular:</label>
            <input type="number" id="numero" placeholder="DD-ddddd-dddd" value="<?php echo isset($_REQUEST["celular"]) ? $_REQUEST["celular"] : ''; ?>" name="numero" ng-model="number" onKeyPress="if(this.value.length==13) return false;" min="0">
            <button name="btnsalvar">Salvar</button>
            <button name="btnnovo">Novo</button>
        </form>
        <hr>

    </body>

    </html>

    <?php

    $dsn = 'mysql:host=localhost;port=3306;dbname=contatos';
    $usuario = 'root';
    $senha = '';

    try {
        
        $conexao = new PDO($dsn, $usuario, $senha);

        $create = 'create database IF NOT EXISTS contatos; use contatos; create table IF NOT EXISTS contato(
                        idcontato int not null primary key auto_increment,
                        nome varchar(255) not null,
                        email varchar(255),
                        celular varchar(13)
                        )';

        $retornodb = $conexao->exec($create);

        $query = 'select * from contato';
        $stm = $conexao->query($query);
        $lista = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo '<table border="1" style="width: 100%; text-align: center;">   <tr><th>ID</th><th>NOME</th><th>EMAIL</th><th>CELULAR</th><th colspan="2">AÇÕES</th></tr>';

        foreach ($lista as $key => $value) {

            $nome = str_replace(' ', '%20', $value["nome"]);

            echo '<td>' . $value["idcontato"] . '</td>';

            echo '<td>' . $value["nome"] . '</td>';

            echo '<td>' . $value["email"] . '</td>';

            echo '<td>' . $value["celular"] . '</td>';

            echo '<td><a href=contatos.php?idcontato=' . $value['idcontato'] . '&nome=' . $nome . '&email=' . $value['email'] . '&celular=' . $value['celular'] . '&action=alter><button type="submit" name="btnalterar">Alterar</button></td>';
            //echo '<form method="post"><td><button type="submit" name="btnexcluir">Excluir</button></td></form>
            echo '<td><a href=contatos.php?idcontato=' . $value['idcontato'] . '&action=delete><button type="submit">Excluir</button></td></form>
            </tr>';
        }

        echo '</table>';
    } catch (PDOException $e) {
        echo 'Message : ' . $e->getMessage();
        echo '<br> Code ' . $e->getCode();
    }


    function novo(){
        header("Location: contatos.php");
    };

    function insere($conexao){
        $insert = 'INSERT INTO contato(nome, email, celular) VALUES (:nome,:email,:celular)';
        $retorno = $conexao->prepare($insert);
        $retorno->bindValue(':nome', $_POST['nome']);
        $retorno->bindValue(':email', $_POST['email']);
        $retorno->bindValue(':celular', $_POST['numero']);
        $retorno->execute();
    }

    function altera($conexao, $id){
        $update = 'UPDATE contato SET nome = :nome, email = :email, celular = :celular WHERE idcontato = :idcontato';  
        $retornoup = $conexao->prepare($update);
        $retornoup->bindParam(':idcontato', $id);
        $retornoup->bindParam(':nome', $_POST['nome']);
        $retornoup->bindParam(':email', $_POST['email']);
        $retornoup->bindParam(':celular', $_POST['numero']);
        $retornoup->execute();
    }

    function apaga($conexao, $id){
        $delete = 'DELETE FROM contato WHERE idcontato = :idcontato';
        $retornodel = $conexao->prepare($delete);
        $retornodel->bindParam(':idcontato', $id);
        $retornodel->execute();
    }

    
    if (isset($_POST["btnnovo"])) {
        //novo();
        echo !empty($_POST["nome"]);
    } 
    
    if (isset($_POST["btnsalvar"])) {

        if (!empty($_POST["nome"]) && !empty($_POST["email"]) && !empty($_POST["numero"])){

            if ($_GET["action"] == 'alter'){
                altera($conexao, $_GET['idcontato']);
            } else {
                insere($conexao);
            }
            novo();

        } else {
            echo '<script type="text/javascript">alert("Preencha todos os campos"); window.location.href = "contatos.php"</script>';
        }
    }
    
    if(isset($_GET['idcontato']) && $_GET['action'] == 'delete'){
        apaga($conexao, $_GET['idcontato']);
        novo();
    }

 ?>
