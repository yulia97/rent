<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;





use App\Entity\Apartment;
use App\Entity\User;
use App\Entity\Bookmark;

class ListController extends Controller
{
    
    /**
     * @Route("/list", name="list")
     */
        public function index(){
    	$repository = $this->getDoctrine()->getRepository(Apartment::class);
	$all = $repository->findAll();

		
        return $this->render('list/index.html.twig', [
            'controller_name' => 'ListController',
	    'apartments' => $all,
	    'links'=>false
        ]);
    }
    /**
     * @Route("/my-ads", name="myads")
     */
    public function myads(){
    	//$repository = $this->getDoctrine()->getRepository(Apartment::class);
	$all = $this->get('security.token_storage')->getToken()->getUser()->getApartments();

	
	
        return $this->render('list/index.html.twig', [
            'controller_name' => 'ListController',
	    'apartments' => $all,
	    'links'=>true
        ]);
    }


    /**
     * @Route("/create", name="create")
     * @Method({"GET", "POST"})
     */
    public function create(Request $request){
	if ($request->isMethod('post')) {
	    $apt = new Apartment();
	    $user = $this->get('security.token_storage')->getToken()->getUser();

	    $title = $request->request->get('title');
	    $address = $request->request->get('address');
	    $description = $request->request->get('description');
	    $files = $request->request->get('files');

	    $entityManager = $this->getDoctrine()->getManager();
	    
	    $apt->setOwner($user);
	    $apt->setTitle($title);
	    $apt->setAddress($address);
	    $apt->setDescription($description);

	    if (!is_null($files))
		foreach($files as $file){
		    $alreadyFile = $entityManager->getRepository(\App\Entity\Document::class)->findBy(array('md5' => $file['md5']));
		    if (count($alreadyFile)){
			$alreadyFile[0]->addApartment($apt);
		    }
		}
	    
            // 4) save the User!

            $entityManager->persist($apt);
            $entityManager->flush();

            //return $this->redirectToRoute('myads');
	    return $this->json(['res'=>'200 Ok']);
        }
	
	return $this->render('create.html.twig', [
	]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @Method({"GET", "POST"})
     */
    public function update(Request $request, $id){

	$user = $this->get('security.token_storage')->getToken()->getUser();
	
	$apt = $user->getApartments();

	$ap = null;
	foreach($apt as $a){
	    if ($a->getId() == $id) {
		$ap = $a; break;
	    }
	}


	if (is_null($ap)) return $this->redirectToRoute('myads');
	
	
	if ($request->isMethod('post')) {
	    $title = $request->request->get('title');
	    $address = $request->request->get('address');
	    $description = $request->request->get('description');
	    $files = $request->request->get('files');	    


	    $entityManager = $this->getDoctrine()->getManager();

	    
	    $ap->setTitle($title);
	    $ap->setAddress($address);
	    $ap->setDescription($description);

	    if (!is_null($files))
		foreach($files as $file){
		    $alreadyFile = $entityManager->getRepository(\App\Entity\Document::class)->findBy(array('md5' => $file['md5']));
		    if (count($alreadyFile)){
			$alreadyFile[0]->addApartment($ap);
		    }
		}
	    
	    
            
            $entityManager->persist($ap);
            $entityManager->flush();

            //return $this->redirectToRoute('myads');
	    return $this->json(['res' => '200 Ok']);
        }
	
	return $this->render('update.html.twig', [
	    'uid' => $ap->getId(),
	    'title' => $ap->getTitle(),
	    'address' => $ap->getAddress(),
	    'description' => $ap->getDescription()
	]);
    }


    
    /**
     * @Route("/delete/{id}", name="delete")
     * @Method({"GET", "POST"})
     */
    public function del(Request $request, $id){

	$user = $this->get('security.token_storage')->getToken()->getUser();
	
	$apt = $user->getApartments();

	$ap = null;
	foreach($apt as $a){
	    if ($a->getId() == $id) {
		$ap = $a; break;
	    }
	}


	if (is_null($ap)) return $this->redirectToRoute('myads');
	
	
	
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ap);
        $entityManager->flush();
	
        return $this->redirectToRoute('myads');
        
    }


    /**
     * @Route("/apartment/{id}", name="aptt")
     * @Method({"GET"})
     */
    public function aptt(Request $request, $id){
	$entityManager = $this->getDoctrine()->getManager();
	$alreadyFile = $entityManager->getRepository(\App\Entity\Apartment::class)->findBy(array('id' => $id));

	if (!count($alreadyFile)) return $this->redirectToRoute('index');

	$ap = $alreadyFile[0];	

	$files = [];

	foreach($ap->getDocuments() as $file){
	    $files[] = '/'.$file->getFilename();
	}

	$me = $this->get('security.token_storage')->getToken()->getUser();
	$likedByMe = false;
	
	$lbm = $entityManager->getRepository(Bookmark::class)->findBy(array('user' => $me, 'apartment'=> $id));
	
	if (count($lbm)) $likedByMe = true;
	
	

	return $this->render('info.html.twig', [
	    'uid' => $ap->getId(),
	    'title' => $ap->getTitle(),
	    'address' => $ap->getAddress(),
	    'description' => $ap->getDescription(),
	    'likedbyme' => $likedByMe,
	    'files' => json_encode($files)
	]);
    }
    
    /**
     * @Route("/fav/{id}", name="fav-apt")
     */
    public function likeUser($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(Apartment::class)->findBy(array('id' => $id));

	$me = $this->get('security.token_storage')->getToken()->getUser();
	
	
	
	$like = new Bookmark();
	$like->setUser($me);
	$like->setApartment($user[0]);

	$entityManager->persist($like);
	$entityManager->flush();

	return $this->json(['res' => '200 Ok']);
    }

    /**
     * @Route("/unfav/{id}", name="unfav-apt")
     */
    public function unfavApt($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(Apartment::class)->findBy(array('id' => $id));

	$me = $this->get('security.token_storage')->getToken()->getUser();


	$lbm = $entityManager->getRepository(Bookmark::class)->findBy(array('user' => $me, 'apartment'=>$user[0]));


	
	if (count($lbm)){
	    	$like = $lbm[0];
	    $entityManager->remove($like);
	    $entityManager->flush();
	}
	return $this->json(['res' => '200 Ok']);
    }


    
    /**
     * @Route("/favourite", name="fav")
     */
    public function myFav(){
	$entityManager = $this->getDoctrine()->getManager();
	$me = $this->get('security.token_storage')->getToken()->getUser();
	
    	$repository = $this->getDoctrine()->getRepository(Apartment::class);
	$lbm = $entityManager->getRepository(Bookmark::class)->findBy(array('user' => $me));

	$all = [];
	foreach($lbm as $apt){
	    $all[] = $apt->getApartment();
	}
	
	
	
        return $this->render('list/index.html.twig', [
	    'controller_name' => 'ListController',
	    'apartments' => $all,
	    'links'=>false
        ]);
    }
    
    
    /**
     * @Route("/user/{id}", name="user")
     */
    public function indexInfo($id)
    {
	$entityManager = $this->getDoctrine()->getManager();
	$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));
	
	
	if (count($user) == 0){
	    return $this->redirectToRoute('chat');
	}
	
	
	$me = $this->get('security.token_storage')->getToken()->getUser();
	$likedByMe = false;	
	
	return $this->render('user.html.twig', [
	    'username' => $user[0]->getUsername(),
	    'user' => $user[0]->getId(),
	    //'likedbyme' => $likedByMe,
	    //'likes' => count($user[0]->getLikedBy()),
	    'status' => $user[0]->getUserInfo()->getStatus()
        ]);    
	
	
    }


    /**
     * @Route("/shout", name="user-shout")
     * @Method({"POST"})
     */
    public function shout(Request $request)
    {
	$entityManager = $this->getDoctrine()->getManager();
	//$user = $entityManager->getRepository(User::class)->findBy(array('id' => $id));
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$text = $request->request->get('status');
	$uinf = $user->getUserInfo();
	$uinf->setStatus($text);

	$entityManager->persist($uinf);
	$entityManager->flush();

	return $this->json(["res"=>'200 ok', 'status' => $text]);
    }


}
