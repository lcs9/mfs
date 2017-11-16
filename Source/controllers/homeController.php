<?php
/**
 * This class is the Controller of the HomePage.
 *
 * @author  samuelrcosta
 * @version 0.1.0, 10/10/2017
 * @since   0.1
 */
class homeController extends controller{

    /**
     * This function shows the homepage.
     */
    public function index($p = 1){
        $a = new Anuncios();
        $c = new Categorias();
        $filtros = array(
            'categoria' => '',
            'preço' => '',
            'estado' => '',
        );
        if(isset($_GET['filtros'])){
            $filtros = $_GET['filtros'];
        }
        $max_pagina = 4;
        $total_paginas = ceil(count($a->getAnuncios())/$max_pagina);
        $anuncios = $a->getUltimosAnuncios($p, $max_pagina, $filtros);
        $categorias = $c->getCategorias();
        $dados = array(
            'titulo' => 'Classi-O',
            'categorias' => $categorias,
            'total_paginas' => $total_paginas,
            'anuncios' => $anuncios,
            'filtros' => $filtros,
        );
        $this->loadTemplate('home', $dados);
    }

    /**
     * This function checks if the user if logged in, if so shows the user data page.
     */
    public function MinhaConta(){
        if(!isset($_SESSION['cLogin']) || empty($_SESSION['cLogin'])){
            header("Location: ".BASE_URL);
        }
        $u = new Usuarios();
        $dadosUsuario = $u->getDados(1, $_SESSION['cLogin']);
        $dados = array(
            'titulo' => 'Minha Conta',
            'dados' => $dadosUsuario
        );
        $this->loadTemplate('MinhaConta', $dados);
    }

    /**
     * This function checks if the user if logged in, if so shows the user data editing page.
     * Receive the input data and use the user's edit method
     */
    public function editarConta(){
        if(!isset($_SESSION['cLogin']) || empty($_SESSION['cLogin'])){
            header("Location: ".BASE_URL);
        }
        $u = new Usuarios();
        $dadosUsuario = $u->getDados(1, $_SESSION['cLogin']);
        $dados = array(
            'titulo' => 'Minha Conta',
            'dados' => $dadosUsuario
        );
        if(isset($_POST['nome']) && !empty($_POST['nome'])){
            $nome = addslashes($_POST['nome']);
            $email = addslashes($_POST['email']);
            $senha = addslashes($_POST['senha']);
            $novaSenha = addslashes($_POST['NovaSenha']);
            $telefone = addslashes($_POST['telefone']);
            $celular = addslashes($_POST['celular']);

            if(!empty($nome) && !empty($email)){
                if($senha != "" && $novaSenha != ""){
                    if($u->login($email, $senha)){
                        if($u->editar($_SESSION['cLogin'], $nome, $email, $novaSenha, $telefone, $celular)){
                            header("Location: ".BASE_URL."/home/MinhaConta");
                        }else{
                            $dados['aviso'] =
                                '<div class="alert alert-warning">
                                    Este email já existe.
                                </div>';
                        }
                    }else{
                        $dados['aviso'] =
                            '<div class="alert alert-warning">
                                Senha Incorreta.
                            </div>';
                    }
                }else{
                    if($u->editar($_SESSION['cLogin'], $nome, $email, $senha, $telefone, $celular)){
                        header("Location: ".BASE_URL."/home/MinhaConta");
                    }else{
                        $dados['aviso'] =
                            '<div class="alert alert-warning">
                                    Este email já existe.
                             </div>';
                    }
                }
            }else{
                $dados['aviso'] =
                    '<div class="alert alert-warning">
                    Preencha todos os campos!
                </div>';
            }
        }
        $this->loadTemplate('editarConta', $dados);
    }

    /**
     * This function use the user's delete method and redirects to homepage
     */
    public function excluirConta(){
        if(!isset($_SESSION['cLogin']) || empty($_SESSION['cLogin'])){
            header("Location: ".BASE_URL);
        }
        $u = new Usuarios();
        $u->excluir($_SESSION['cLogin']);
        $u->logOff($_SESSION['cLogin']);
        header("Location: ".BASE_URL."/home");
    }
}