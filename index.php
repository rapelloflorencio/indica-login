<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Usuario;
use App\Models\Entity\Perfil;
use App\Models\Entity\Profissional;
use App\Models\Entity\AtividadeProfissional;
use App\Models\Entity\Bairro;
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
    'passthrough' => ['/api/login','/api/cadastro','/api/consulta/atividade','/api/validaSemSenha', '/api/alterarSenha/'],
    'authenticator' => $authenticator,
    'secure' => false
]));

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

$app->get('/api/profissional/{id}', function (Request $request, Response $response) use ($app,$entityManager) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
    $user = $usersRepository->find($id);        

    $return = $response->withJson($user, 200)
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

    $user = new Usuario($params->nome,$params->password,$params->cep,$params->endereco,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->cpf,$params->imagem, $perfil);
    
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
    $profissional = new Profissional($params->nome,$params->fantasia,$params->password,$params->cep,$params->endereco,$params->complemento,$bairro,$params->email,$params->telefone1,$params->telefone2,$params->telefone3,$params->telefone4,$params->cpf,$params->cnpj,$params->frenterg,$params->versorg,$params->comprovante,$params->foto,$atividade_principal,$extra,$params->situacao_cadastral, $perfil);
    
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

    /**
     * Atualiza e Persiste o Usuario com os parâmetros recebidos no request
     */
    $user->setUsuario($request->getParam('usuario'))
        ->setPassword($request->getParam('password'));

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

    /**
     * Encontra o Usuario no Banco
     */ 
    $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
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
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email));
      
        if(password_verify($params->password, $userBanco->getPassword())){
            $token = ['usuario'=> $userBanco , 'token' => '$2y$10$AJi4JsNTXkoJar.RvC0r9eEXnvPtUKA74h1UqPAujPac8RChKmvb6' ];
            return $response->withJson($token, 200)
            ->withHeader('Content-type', 'application/json');
        } 
        return $response->withStatus(401, 'Usuario ou senha incorretos');
        
    });

    $app->post('/api/validaSemSenha', function (Request $request, Response $response) use ($app,$entityManager) {
    
        $params = (object) $request->getParams();
        $usersRepository = null;
        if($params->perfil == "cliente"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Usuario');
        }elseif($params->perfil == "profissional"){
            $usersRepository = $entityManager->getRepository('App\Models\Entity\Profissional');
        }
        $userBanco = $usersRepository->findOneBy(array('email' => $params->email,'cpf' => $params->cpf,'telefone1' => $params->telefone1));
      
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
    
        $app->get('/api/perfil', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $perfilRepository = $entityManager->getRepository('App\Models\Entity\Perfil');
            $perfil = $perfilRepository->findAll();
        
            $return = $response->withJson($perfil, 200)
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
    
        $app->post('/api/atividade', function (Request $request, Response $response) use ($app,$entityManager) {
        
            $params = (object) $request->getParams();
            $atividade = new AtividadeProfissional($params->nome);
            
            $entityManager->persist($atividade);
            $entityManager->flush();
               
            $return = $response->withJson($atividade, 201)
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
$app->run();