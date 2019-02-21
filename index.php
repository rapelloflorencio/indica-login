<?php
date_default_timezone_set("America/Sao_Paulo");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Entity\Usuario;
use App\Models\Entity\Perfil;
use App\Models\Entity\Profissional;
use App\Models\Entity\Administrador;
use App\Models\Entity\AtividadeProfissional;
use App\Models\Entity\Bairro;
use App\Models\Entity\HorarioServico;
use App\Models\Entity\Orcamento;
use App\Models\Entity\SolicitacaoOrcamento;
use App\Models\Entity\StatusOrcamento;
use App\Models\Entity\LocalAtendimento;
use App\Models\Entity\UrgenciaServico;
use App\Models\Entity\AvaliacaoServico;
use App\Models\Entity\AvaliacaoCliente;
use App\Models\Entity\Servico;
use App\Models\Entity\Pagamento;
use App\Models\Entity\Parametro;
use Slim\App;
use Slim\Container;
use Doctrine\ORM\EntityManager;
use Slim\Middleware\TokenAuthentication;
use App\Auth\Auth;

/** @var Container $cnt */
$cnt = require_once __DIR__ . '/bootstrap.php';
$entityManager = $cnt[EntityManager::class];
/** @var App $app */
$app = $cnt[App::class];

$authenticator = function($request, TokenAuthentication $tokenAuth){
    /**
     * Try find authorization token via header, parameters, cookie or attribute
     * If token not found, return response with status 401 (unauthorized)
     */
    $token = $tokenAuth->findToken($request);
    /**
     * Call authentication logic class
     */
    $auth = new Auth();
    /**
     * Verify if token is valid on database
     * If token isn't valid, must throw an UnauthorizedExceptionInterface
     */
    $auth->validateToken($token);
};

/**
 * Add token authentication middleware
 */
$app->add(new TokenAuthentication([
    'path' =>   '/api',
    'passthrough' => ['/api/login','/api/cadastro','/api/consulta','/api/validaSemSenha', '/api/alterarSenha/', "/api/imagens/", '/api/usuario/','/api/profissional/', '/api/enviar/email' ],
    'authenticator' => $authenticator,
    'secure' => false
]));

$app->add(new Tuupola\Middleware\CorsMiddleware([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PATCH", "PUT", "DELETE", "OPTIONS"],    
    "headers.allow" => ["Origin", "Content-Type", "Authorization", "Accept", "ignoreLoadingBar", "X-Requested-With", "Access-Control-Allow-Origin"],
    "headers.expose" => [],
    "credentials" => true,
    "cache" => 0,   
]));

/**
 * Retornando mais informações do usuario informado pelo id
 * @request curl -X GET http://localhost:8000/user/1
 */
$app->get('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);        

    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/imagens/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = null;
    $retorno = null;
    if($request->getParam('perfil') == "cliente"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        $user = $usersRepository->find($id);
        $retorno = [
            'foto' => $user->getImagem()
        ];
    }elseif($request->getParam('perfil') == "profissional"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        $user = $usersRepository->find($id);
        $retorno = [
            'foto' => $user->getImagem(),
            'frenterg' => $user->getFrenterg(),
            'versorg' => $user->getVersorg(),
            'comprovante' => $user->getComprovante()
        ];
    }
   
    $return = $response->withJson($retorno, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);        

    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/profissional/situacaoCadastral/{situacao}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $situacao_cadastral = $route->getArgument('situacao');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    
    if($situacao_cadastral=="T"){
    	$users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }else{
    
    $users = $usersRepository->findBy(array('situacao_cadastral' => $situacao_cadastral));

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }
});

$app->get('/api/usuario/situacaoCadastral/{situacao}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $situacao_cadastral = $route->getArgument('situacao');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    
    if($situacao_cadastral=="T"){
    	$users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }else{
    
    $users = $usersRepository->findBy(array('situacao_cadastral' => $situacao_cadastral));

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }
});


/**
 * Lista de todos os usuarios
 * @request curl -X GET http://localhost:8000/user
 */
$app->get('/api/usuario', function (Request $request, Response $response) use ($app,$entityManager) {

    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/profissional', function (Request $request, Response $response) use ($app,$entityManager) {

    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $users = $usersRepository->findAll();

    $return = $response->withJson($users, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Cadastra um novo <Usuario></Usuario>
 * @request curl -X POST http://localhost:8000/user -H "Content-type: application/json" -d '{"name":"O Oceano no Fim do Caminho", "author":"Neil Gaiman"}'
 */
$app->post('/api/cadastro/usuario', function (Request $request, Response $response) use ($app,$entityManager) {

    $params = (object) $request->getParams();
    
    $id = $params->perfil;
    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($id);        

    $id = $params->bairro;
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($id);        

    $user = new Usuario($params->nome,$params->password,$params->cep,$params->endereco,$params->complemento,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->cpf,$params->imagem, $perfil, $params->situacao_cadastral);
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $userBanco = $usersRepository->findOneBy(array('email' => $user->getEmail()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um usuário cadastrado para esse email."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }

    $userBanco = $usersRepository->findOneBy(array('telefone1' => $user->getTelefone1()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um usuário cadastrado para esse telefone."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }

    $userBanco = $usersRepository->findOneBy(array('cpf' => $user->getCpf()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um usuário cadastrado para esse CPF."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();


    $return = $response->withJson($user, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/cadastro/profissional', function (Request $request, Response $response) use ($app,$entityManager) {

    $params = (object) $request->getParams();
    
    $id = $params->perfil;
    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($id);          

    $id = $params->atividade_principal;
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade_principal = $atividadeRepository->find($id);
    $id = $params->extra;
    $extra = null;
    if($id != ''){
        $extra = $atividadeRepository->find($id);
    }
    $id = $params->bairro;
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($id); 
    $profissional = new Profissional($params->nome,$params->fantasia,$params->password,$params->cep,$params->endereco,$params->complemento,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->telefone3,$params->telefone4,trim($params->cpf),trim($params->cnpj),$params->frenterg,$params->versorg,$params->comprovante,$params->foto,$atividade_principal,$extra,$params->situacao_cadastral, $perfil,$params->identidade);
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $userBanco = $usersRepository->findOneBy(array('email' => $profissional->getEmail()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um profissional cadastrado para esse email."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }

    $userBanco = $usersRepository->findOneBy(array('telefone1' => $profissional->getTelefone1()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um profissional cadastrado para esse telefone."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }

    $userBanco = $usersRepository->findOneBy(array('cpf' => $profissional->getCpf()));
    
    if($userBanco != null){
        $return = $response->withJson(['mensagem'=>"Já existe um profissional cadastrado para esse CPF."], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($profissional);
    $entityManager->flush();
    

    $return = $response->withJson($profissional, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});
/**
 * Atualiza os dados de um Usuario
 * @request curl -X PUT http://localhost:8000/user/14 -H "Content-type: application/json" -d '{"name":"Deuses Americanos", "author":"Neil Gaiman"}'
 */
$app->put('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);   

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($request->getParam('bairro')); 

    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($request->getParam('perfil'));
    
    $userBanco = $usersRepository->findOneBy(array('cpf' => $request->getParam('cpf')));
    if($userBanco!=null){
	  $return = $response->withJson(['mensagem'=>"Já existe um usuário cadastrado para este CPF."], 409)
        ->withHeader('Content-type', 'application/json');
    	return $return;  
    }
    
    /**
     * Atualiza e Persiste o Usuario com os parâmetros recebidos no request
     */
    $user->setNome($request->getParam('nome'))
        ->setPassword($request->getParam('password'))
        ->setCep($request->getParam('cep'))
        ->setEndereco($request->getParam('endereco'))
        ->setComplemento($request->getParam('complemento'))
        ->setBairro($bairro)
        ->setEmail($request->getParam('email'))
        ->setTelefone1($request->getParam('telefone1'))
        ->setTelefone2($request->getParam('telefone2'))
        ->setCpf($request->getParam('cpf'))
        ->setImagem($request->getParam('imagem'))
        ->setPerfil($perfil)
        ->setSituacao_Cadastral($request->getParam('situacao_cadastral'));

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->put('/api/atualiza/situacaoCadastral/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);   

    $user->setSituacao_Cadastral($request->getParam('situacao_cadastral'));

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


$app->put('/api/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);   

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($request->getParam('bairro')); 

    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($request->getParam('perfil'));
    
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade_principal = $atividadeRepository->find($request->getParam('atividade_principal'));
    $extra = $atividadeRepository->find($request->getParam('extra'));

    $user->setNome($request->getParam('nome'))
        ->setNome_Fantasia($request->getParam('fantasia'))
        ->setPassword($request->getParam('password'))
        ->setCep($request->getParam('cep'))
        ->setEndereco($request->getParam('endereco'))
        ->setComplemento($request->getParam('complemento'))
        ->setBairro($bairro)
        ->setEmail($request->getParam('email'))
        ->setTelefone1($request->getParam('telefone1'))
        ->setTelefone2($request->getParam('telefone2'))
        ->setTelefone3($request->getParam('telefone3'))
        ->setTelefone4($request->getParam('telefone4'))
        ->setImagem($request->getParam('foto'))
        ->setFrenterg($request->getParam('frenterg'))
        ->setVersorg($request->getParam('versorg'))
        ->setComprovante($request->getParam('comprovante'))
        ->setAtividade_Principal($atividade_principal)
        ->setAtividade_Extra($extra)
        ->setSituacao_Cadastral($request->getParam('situacao_cadastral'))
        ->setPerfil($perfil)
        ->setIdentidade($request->getParam('identidade'));

        if($request->getParam('cpf')==""){
            $user->setCpf(null);
        }else{
            $user->setCpf($request->getParam('cpf'));
        }

        if($request->getParam('cnpj')==""){
            $user->setCnpj(null);
        }else{
            $user->setCnpj($request->getParam('cnpj'));
        }
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->put('/api/atualiza/situacaoCadastral/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);   

    $user->setSituacao_Cadastral($request->getParam('situacao_cadastral'));
       
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->put('/api/atualiza/aceite/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);   

    $user->setStatusAceite($request->getParam('aceite'));
    $user->setStatusAceite($request->getParam('data'));
       
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


$app->put('/api/alterarSenha/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = null;
    if($request->getParam('perfil') == "cliente"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    }elseif($request->getParam('perfil') == "profissional"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    }
    /**
     * Encontra o Usuario no Banco
     */ 
    $user = $usersRepository->find($id);   

    /**
     * Atualiza e Persiste o Usuario com os parâmetros recebidos no request
     */
    $user->setPassword($request->getParam('password'));

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();        

    
    $return = $response->withJson($user, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Deleta o Usuario informado pelo ID
 * @request curl -X DELETE http://localhost:8000/user/3
 */
$app->delete('/api/usuario/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    /**
     * Pega o ID do Usuario informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    /**
     * Encontra o Usuario no Banco
     */ 
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $user = $usersRepository->find($id);   

    /**
     * Remove a entidade
     */
    $entityManager->remove($user);
    $entityManager->flush(); 

    $return = $response->withJson(['msg' => "Deletando o Usuario {$id}"], 204)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/login', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }elseif($params->perfil == "admin"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Administrador');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email));
      
        if(password_verify($params->password, $userBanco->getPassword())){
            $token = ['usuario'=> $userBanco , 'token' => 'Bearer $2y$10$AJi4JsNTXkoJar.RvC0r9eEXnvPtUKA74h1UqPAujPac8RChKmvb6' ];
            return $response->withJson($token, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(401, 'Usuario ou senha incorretos');
        
    });

    $app->post('/api/cadastro/validaEmail', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email));
      
        if($userBanco != null){
            return $response->withJson($userBanco, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(404, 'E-mail não cadastrado.');
        
    });


    $app->post('/api/validaSemSenha', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email));
      
        if($userBanco != null){
           return $response->withJson($userBanco, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(404, 'Usuario não encontrado');
        
    });

    $app->post('/api/perfil', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $params = (object) $request->getParams();
            $perfil = new Perfil($params->nome);
            
            $entityManager->persist($perfil);
            $entityManager->flush();
               
            $return = $response->withJson($perfil, 201)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->get('/api/perfil/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
            $perfil = $perfilRepository->find($id);        
        
            $return = $response->withJson($perfil, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
       
        $app->get('/api/perfil', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
            $perfil = $perfilRepository->findAll();
        
            $return = $response->withJson($perfil, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->post('/api/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $params = (object) $request->getParams();
            $atividade = new AtividadeProfissional($params->nome, $params->mneumonico);
            
            $entityManager->persist($atividade);
            $entityManager->flush();
               
            $return = $response->withJson($atividade, 201)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
        
        $app->put('/api/atualiza/atividade/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
                
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
        
            $atividade = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional')->find($id);
            
            $atividade->setNome($request->getParam('nome'));
            $atividade->setMneumonico($request->getParam('mneumonico'));
        
            $entityManager->persist($atividade);
            $entityManager->flush();        
               
            $return = $response->withJson($atividade, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
    $app->get('/api/consulta/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
            $atividade = $atividadeRepository->findAll();
        
            $return = $response->withJson($atividade, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

    $app->get('/api/atividade/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
            $atividade = $atividadeRepository->find($id);        
        
            $return = $response->withJson($atividade, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });
    
        $app->get('/api/consulta/bairro', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
            $bairros = $bairroRepository->findAll();
        
            $return = $response->withJson($bairros, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

    $app->get('/api/bairro/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
            $route = $request->getAttribute('route');
            $id = $route->getArgument('id');
            $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
            $bairro = $bairroRepository->find($id);        
        
            $return = $response->withJson($bairro, 200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

$app->post('/api/consulta/quantidade/profissional/bairro/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
    $params = (object) $request->getParams();
    $repository = $entityManager->getRepository('App\Models\Entity\Profissional');
    
    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($params->id_bairro);
    
    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade = $atividadeRepository->find($params->id_atividade); 

    $totalPorBairro =  $repository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.bairro = :bairro')
            ->setParameter('bairro', $bairro)
            ->andWhere('u.atividade_principal = :atividade')
            ->setParameter('atividade', $atividade)
            ->getQuery()
            ->getSingleScalarResult();
    
    $totalPorAtividade =  $repository->createQueryBuilder('u')
    ->select('count(u.id)')
    ->andWhere('u.atividade_principal = :atividade')
    ->setParameter('atividade', $atividade)
    ->getQuery()
    ->getSingleScalarResult();

    $resultado = [
        'totalPorBairro'=>$totalPorBairro,
        'totalPorAtividade'=>$totalPorAtividade
    ];
    $return = $response->withJson($resultado,200)
                ->withHeader('Content-type', 'application/json');
            return $return;
        });

$app->get('/api/consulta/horario', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $repository = $entityManager->getRepository('App\Models\Entity\HorarioServico');
    $horarios = $repository->findAll();
        
    $return = $response->withJson($horarios, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/consulta/statusOrcamento', function (Request $request, Response $response) use ($app,$entityManager) {
    $status = $entityManager->getRepository('App\Models\Entity\StatusOrcamento')->findAll();
    $return = $response->withJson($status, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/solicitar/orcamento', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
    $usuario = $usersRepository->find($params->idUsuario);   

    $atividadeRepository = $entityManager->getRepository('App\Models\Entity\AtividadeProfissional');
    $atividade = $atividadeRepository->find($params->idAtividade); 

    $bairroRepository = $entityManager->getRepository('App\Models\Entity\Bairro');
    $bairro = $bairroRepository->find($params->idBairro); 

    $horarioRepository = $entityManager->getRepository('App\Models\Entity\HorarioServico');
    $horario = $horarioRepository->find($params->idHorario);
    $horarioAlternativo = $horarioRepository->find($params->idHorarioAlternativo);

    $localAtendimentoRepository = $entityManager->getRepository('App\Models\Entity\LocalAtendimento');
    $localAtendimento = $localAtendimentoRepository->find($params->idLocalAtendimento);

    $urgenciaServicoRepository = $entityManager->getRepository('App\Models\Entity\UrgenciaServico');
    $urgenciaServico = $urgenciaServicoRepository->find($params->idUrgencia);

    $favorito = null;
    if($params->idFavorito != ""){
    $profissionalRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $favorito = $profissionalRepository->find($params->idFavorito);
    }
    $solicitacaoRepository = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento');
    $solicitacaoAberta = $solicitacaoRepository->createQueryBuilder('u')
    ->select('count(u.id)')
    ->andWhere('u.usuario = :usuario')
    ->setParameter('usuario', $usuario)
    ->andWhere('u.atividade = :atividade')
    ->setParameter('atividade', $atividade)
    ->andWhere('u.bairro = :bairro')
    ->setParameter('bairro', $bairro)
    ->andWhere('u.status = :status')
    ->setParameter('status', "A")
    ->getQuery()
    ->getSingleScalarResult();

    if($solicitacaoAberta==0){
    $solicitacao = new SolicitacaoOrcamento($usuario, $atividade, $bairro, $params->textoSolicitacao, $urgenciaServico, $localAtendimento, $horario, $params->endereco, $horarioAlternativo, $params->dataServico, $params->dataAlternativa, $favorito);
    
    $entityManager->persist($solicitacao);
    $entityManager->flush();
       
    $return = $response->withJson($solicitacao, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }else{
        $return = $response->withJson(['mensagem'=>"Já existe uma solicitação em aberto para este serviço"], 409)
        ->withHeader('Content-type', 'application/json');
    return $return;
    }
});

$app->get('/api/consulta/solicitacao/{tipoUsuario}/{idUsuario}/{status}', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $route = $request->getAttribute('route');
    $idUsuario = $route->getArgument('idUsuario');
    $tipoUsuario = $route->getArgument('tipoUsuario');

    $status = $route->getArgument('status');
    $repository = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento');
    $solicitacoes = null;
    $usersRepository = null;
    if($tipoUsuario == "cliente"){
        $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        $usuario = $usersRepository->find($idUsuario);
        if($status == "T"){
            $solicitacoes = $repository->findBy(array('usuario' => $usuario), array('id' => 'DESC')); 
        } else{
            $solicitacoes = $repository->findBy(array('usuario' => $usuario, 'status' => $status), array('id' => 'DESC'));
        }
    }elseif($tipoUsuario == "profissional"){
        $profissionalRepository = $entityManager->getRepository('App\Models\Entity\Profissional'); 
        $profissional = $profissionalRepository->find($idUsuario);
        if($status == "T"){
        $solicitacoes = $repository->findBy(array('atividade' => $profissional->getAtividade_Principal()), array('id' => 'DESC'));
        if($profissional->getAtividade_Extra() != null){
            $solicitacoes_extra = $repository->findBy(array('atividade' => $profissional->getAtividade_Extra()), array('id' => 'DESC'));
            $result = array_merge($solicitacoes, $solicitacoes_extra);
            $solicitacoes = $result;
        }
        } else{
            $solicitacoes = $repository->findBy(array('atividade' => $profissional->getAtividade_Principal(), 'status' => $status), array('id' => 'DESC'));
            if($profissional->getAtividade_Extra() != null){
                $solicitacoes_extra = $repository->findBy(array('atividade' => $profissional->getAtividade_Extra(), 'status' => $status), array('id' => 'DESC'));
                $result = array_merge($solicitacoes, $solicitacoes_extra);
                $solicitacoes = $result; 
            }
        }
    }
    
    $return = $response->withJson($solicitacoes, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->put('/api/cancelar/solicitacao/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $solicitacao = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento')->find($id);
    
    $solicitacao->setStatus("C");

    $entityManager->persist($solicitacao);
    $entityManager->flush();        
  
    $return = $response->withJson($solicitacao, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->post('/api/gravar/orcamento', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    
    $status = $entityManager->getRepository('App\Models\Entity\StatusOrcamento')->find($params->status);
    $solicitacao = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento')->find($params->solicitacao);
    $profissional = $entityManager->getRepository('App\Models\Entity\Profissional')->find($params->profissional);
    $valor = $params->valor;
    $descricao = $params->descricao;

    $orcamento = new Orcamento($status, $solicitacao, $profissional, $valor, $descricao);

    $entityManager->persist($orcamento);
    $entityManager->flush();
    if($solicitacao->getOrcamento1()==null){
        $solicitacao->setOrcamento1($orcamento);
    } else{
        $solicitacao->setOrcamento2($orcamento);
    }
    $entityManager->persist($solicitacao);
    $entityManager->flush();
    

    $valor = (int) str_replace(",","",$entityManager->getRepository('App\Models\Entity\Parametro')->findOneBy(array('nome'=>"valor_aceite_orcamento"))->getValor());
    $pagamento = new Pagamento(null,$orcamento, $profissional,"Débito", $valor);
    $entityManager->persist($pagamento);
    $entityManager->flush();

    $return = $response->withJson($orcamento, 201)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/consulta/orcamento/{idProfissional}/{idStatus}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    
    $status = $entityManager->getRepository('App\Models\Entity\StatusOrcamento')->find($route->getArgument('idStatus'));
    $profissional = $entityManager->getRepository('App\Models\Entity\Profissional')->find($route->getArgument('idProfissional'));
    $orcamentos = $entityManager->getRepository('App\Models\Entity\Orcamento')->findBy(array('profissional' => $profissional, 'status' => $status));

    $return = $response->withJson($orcamentos, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->post('/api/gravar/avaliacao/profissional', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    
    $orcamento = $entityManager->getRepository('App\Models\Entity\Orcamento')->find($params->orcamento_id);
    
    $avaliacao = new AvaliacaoServico($orcamento,  $params->dataTermino, $params->valor, $params->pontualidade,$params->competencia, $params->prazo, $params->organizacao, $params->atitude, $params->comentario);

    $entityManager->persist($avaliacao);
    $entityManager->flush();
    
    $status = $entityManager->getRepository('App\Models\Entity\StatusOrcamento')->find(6);
    $orcamento->setStatus($status);

    $entityManager->persist($orcamento);
    $entityManager->flush();

    $solicitacao = $orcamento->getSolicitacao();
    $solicitacao->setStatus("F");
    
    $entityManager->persist($solicitacao);
    $entityManager->flush();

    $return = $response->withJson($avaliacao, 201)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/consulta/avaliacao/profissional', function (Request $request, Response $response) use ($app,$entityManager) {

    $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findAll();
    $return = $response->withJson($avaliacoes, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/consulta/avaliacao/profissional/{id_atividade}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    
    $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findBy(array('atividade' => $route->getArgument('id_atividade')));
    $return = $response->withJson($avaliacoes, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/profissional/avaliacoes/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    
    $orcamentos = $entityManager->getRepository('App\Models\Entity\Orcamento')->findBy(array('profissional' => $route->getArgument('id')));
    
    $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findBy(array('orcamento' => $orcamentos));
    $return = $response->withJson($avaliacoes, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->get('/api/usuario/avaliacoes/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
        
    $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoCliente')->findBy(array('usuario' => $route->getArgument('id')));
    $return = $response->withJson($avaliacoes, 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->post('/api/enviar/email', function (Request $request, Response $response) use ($app,$entityManager) {      
    $params = (object) $request->getParams();
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
   //Server settings
   $mail->SMTPDebug = 3;                                 // Enable verbose debug output
   //$mail->isSMTP();                                      // Set mailer to use SMTP
   $mail->Host = 'mail.indicaerecomenda.com.br';         // Specify main and backup SMTP servers
   $mail->SMTPAuth = false;                               // Enable SMTP authentication
   $mail->Username = 'contato@indicaerecomenda.com.br'; // SMTP username
   $senha = $entityManager->getRepository('App\Models\Entity\Parametro')->findOneBy(array('nome'=>"senha_email"))->getValor();
   $mail->Password = $senha;                             // SMTP password
   //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
   $mail->Port = 25;                                   // TCP port to connect to 587

    //Recipients
    $mail->setFrom('contato@indicaerecomenda.com.br', '');
    $mail->addAddress($params->email);     // Add a recipient
    
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $params->assunto;
    $mail->Body    = $params->mensagem;

    $mail->send();
    $return = $response->withJson(['mensagem'=>"E-mail enviado com sucesso."], 200)
        ->withHeader('Content-type', 'application/json');
            return $return;
} catch (Exception $e) {
    $return = $response->withJson(['mensagem'=>$mail->ErrorInfo], 409)
        ->withHeader('Content-type', 'application/json');
            return $return;
}
});

$app->get('/api/consulta/parametro', function (Request $request, Response $response) use ($app,$entityManager) {
   
    $repository = $entityManager->getRepository('App\Models\Entity\Parametro');
    $parametros = $repository->findAll();        

    $return = $response->withJson($parametros, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/consulta/parametro/{nome}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $nome = $route->getArgument('nome');
    $repository = $entityManager->getRepository('App\Models\Entity\Parametro');
    $parametro = $repository->findOneBy(array('nome'=>$nome));        

    $return = $response->withJson($parametro, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/consulta/favoritos/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id_usuario = $route->getArgument('id');
    $repository = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento');
    $solicitacoes = $repository->findBy(array('usuario'=>$id_usuario));        
    $favoritos = array();
    foreach ($solicitacoes as $solicitacao){
        if($solicitacao->getOrcamento1()!= null){
            if($solicitacao->getOrcamento1()->getStatus()->getId()==6){
            $profissional = $solicitacao->getOrcamento1()->getProfissional();    
              
            $orcamentos = $entityManager->getRepository('App\Models\Entity\Orcamento')->findBy(array('profissional'=>$profissional));
            $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findBy(array('orcamento'=>$orcamentos));
            $somatorio = 0;
            $divisor = 0;
            $media = 0;
            foreach ($avaliacoes as $avaliacao){
                $somatorio = $somatorio + $avaliacao->getPontualidade() + $avaliacao->getCompetencia() + $avaliacao->getPrazo() + $avaliacao->getOrganizacao() + $avaliacao->getAtitude();
                $divisor=$divisor+5;
            }
            $media = $somatorio/$divisor;
            if($media >= $entityManager->getRepository('App\Models\Entity\Parametro')->findOneBy(array('nome'=>"nota_minima"))){
                $favoritos[] = $profissional;
            }
            continue;
            }
        } elseif($solicitacao->getOrcamento2()!= null){
            if($solicitacao->getOrcamento2()->getStatus()->getId()==6){
                $profissional = $solicitacao->getOrcamento2()->getProfissional();
                $orcamentos = $entityManager->getRepository('App\Models\Entity\Orcamento')->findBy(array('profissional'=>$profissional));
                $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findBy(array('orcamento'=>$orcamentos));
                $somatorio = 0;
                $divisor = 0;
                $media = 0;
                foreach ($avaliacoes as $avaliacao){
                    $somatorio = $somatorio + $avaliacao->getPontualidade() + $avaliacao->getCompetencia() + $avaliacao->getPrazo() + $avaliacao->getOrganizacao() + $avaliacao->getAtitude();
                    $divisor=$divisor+5;
                }
                $media = $somatorio/$divisor;
                if($media >= $entityManager->getRepository('App\Models\Entity\Parametro')->findOneBy(array('nome'=>"nota_minima"))){
                    $favoritos[] = $profissional;
                }
            continue;
            }
        }
    }

    $return = $response->withJson($favoritos, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/consulta/nota/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
   try{ 
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');      
    $orcamentos = $entityManager->getRepository('App\Models\Entity\Orcamento')->findBy(array('profissional'=>$id));
    $avaliacoes = $entityManager->getRepository('App\Models\Entity\AvaliacaoServico')->findBy(array('orcamento'=>$orcamentos));
    $somatorio = 0;
    $divisor = 0;
    $media = 0;
    foreach ($avaliacoes as $avaliacao){
        $somatorio = $somatorio + $avaliacao->getPontualidade() + $avaliacao->getCompetencia() + $avaliacao->getPrazo() + $avaliacao->getOrganizacao() + $avaliacao->getAtitude();
        $divisor=$divisor+5;
    }
    $media = $somatorio/$divisor;
    $return = $response->withJson(['nota'=>number_format($media,1,',','')], 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
}catch(Exception $e){
    $return = $response->withJson(['nota'=>0], 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
}
});
$app->post('/api/parametro', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    $parametro = new Parametro($params->nome, $params->valor);
    
    $entityManager->persist($parametro);
    $entityManager->flush();
       
    $return = $response->withJson($parametro, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->put('/api/parametro/{nome}', function (Request $request, Response $response) use ($app,$entityManager) {

    $route = $request->getAttribute('route');
    $nome = $route->getArgument('nome');

    $parametro = $entityManager->getRepository('App\Models\Entity\Parametro')->findOneBy(array('nome'=>$nome));
    
    $parametro->setValor($request->getParam('valor'));

    $entityManager->persist($parametro);
    $entityManager->flush();        
  
    $return = $response->withJson($parametro, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;

});

$app->post('/api/gravar/inicio/servico', function (Request $request, Response $response) use ($app,$entityManager) {
        
    $params = (object) $request->getParams();
    
    $orcamento = $entityManager->getRepository('App\Models\Entity\Orcamento')->find($params->orcamento_id);
    $solicitacao = $entityManager->getRepository('App\Models\Entity\SolicitacaoOrcamento')->find($params->solicitacao_id);

    $servico = new Servico($solicitacao, $orcamento,  $params->dataInicio, $params->valorInicialServico, $params->valorInicialMaoObra,$params->prazoInicial);
    $entityManager->persist($servico);
    $entityManager->flush();
    
    $solicitacao->setServico($servico);

    $entityManager->persist($solicitacao);
    $entityManager->flush();

    $return = $response->withJson($servico, 201)
        ->withHeader('Content-type', 'application/json');
            return $return;
});

$app->put('/api/gravar/final/servico/{id}', function (Request $request, Response $response) use ($app,$entityManager) {

    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    $servico = $entityManager->getRepository('App\Models\Entity\Servico')->find($id);
    
    $servico->setDataTermino($request->getParam('dataTermino'));
    $servico->setDataPagamento($request->getParam('dataPagamento'));
    $servico->setValorTotalServico($request->getParam('valorTotalServico'));
    $servico->setValorTotalMaoObra($request->getParam('valorTotalMaoObra'));
    $servico->setValorRemunerado($request->getParam('valorRemunerado'));
    $servico->setValorAceiteOrcamento($request->getParam('valorAceiteOrcamento'));
    $servico->setValorDevidoAjustado($request->getParam('valorDevidoAjustado'));
    $servico->setStatus("F");

    $entityManager->persist($servico);
    $entityManager->flush();        
    
    $avaliacaoCliente = new AvaliacaoCliente($servico->getSolicitacao()->getUsuario(), $servico, $request->getParam('desisteAdiaCancelaServico'), $request->getParam('pagaCombinado'),$request->getParam('exigeAlemCombinado'),$request->getParam('comentario'));
    $entityManager->persist($avaliacaoCliente);
    $entityManager->flush(); 

    $pagamento = new Pagamento($servico,null, $servico->getOrcamento()->getProfissional(),"Débito", $servico->getValorDevidoAjustado());
    $entityManager->persist($pagamento);
    $entityManager->flush();

    $return = $response->withJson(['servico'=>$servico, 'avaliacaoCliente'=>$avaliacaoCliente], 200)
        ->withHeader('Content-type', 'application/json');
    return $return;

});

$app->get('/api/consulta/servico/por/orcamento/{id_orcamento}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id_orcamento = $route->getArgument('id_orcamento');
    $repository = $entityManager->getRepository('App\Models\Entity\Servico');
    $servico = $repository->findOneBy(array('orcamento'=>$id_orcamento));        

    $avaliacaoCliente = $entityManager->getRepository('App\Models\Entity\AvaliacaoCliente')->findOneBy(array('servico'=>$servico));

    $return = $response->withJson(['servico'=>$servico, 'avaliacaoCliente'=>$avaliacaoCliente], 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/consulta/servico/status/{status}/{mes}/{ano}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $status = $route->getArgument('status');
    $repository = $entityManager->getRepository('App\Models\Entity\Servico');
    
    $mes = $route->getArgument('mes');
    $ano = $route->getArgument('ano');
    
    $startDate = new \DateTimeImmutable("$ano-$mes-01T00:00:00");
    $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

    $qb = $repository->createQueryBuilder('servico');
    $qb->where('servico.dataTermino BETWEEN :start AND :end');
    $qb->setParameter('start', $startDate);
    $qb->setParameter('end', $endDate);

    $servicos = $qb->getQuery()->getResult();        

    $return = $response->withJson($servicos, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

$app->get('/api/consulta/pagamento/status/{status}/{mes}/{ano}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $status = $route->getArgument('status');
    $repository = $entityManager->getRepository('App\Models\Entity\Pagamento');
    
    $mes = $route->getArgument('mes');
    $ano = $route->getArgument('ano');
    
    $startDate = new \DateTimeImmutable("$ano-$mes-01T00:00:00");
    $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

    $qb = $repository->createQueryBuilder('pagamento');
    $qb->where('pagamento.dataInclusao BETWEEN :start AND :end');
    $qb->setParameter('start', $startDate);
    $qb->setParameter('end', $endDate);

    $pagamentos = $qb->getQuery()->getResult();        

    $return = $response->withJson($pagamentos, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


$app->post('/api/cadastro/administrador', function (Request $request, Response $response) use ($app,$entityManager) {

    $params = (object) $request->getParams();
    
    $id = $params->perfil;
    $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
    $perfil = $perfilRepository->find($id);        
    
    $user = new Administrador($params->nome,$params->password, $params->email,$params->telefone, $perfil);
    
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($user);
    $entityManager->flush();


    $return = $response->withJson($user, 201)
        ->withHeader('Content-type', 'application/json');
    return $return;
});


$app->run();
