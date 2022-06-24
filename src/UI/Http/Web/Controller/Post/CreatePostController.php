<?php

namespace App\UI\Http\Web\Controller\Post;

use App\Application\UseCase\Command\Post\Create\CreatePostCommand;
use App\Application\UseCase\Command\Post\Create\CreatePostUseCase;
use App\Domain\Post\Exception\InvalidPostDataException;
use App\Infrastructure\Post\Doctrine\Post;
use App\UI\Http\Web\Form\Post\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts/create", name="app.post.create")
 */
class CreatePostController extends AbstractController
{
    /**
     * @param Request $request
     * @param CreatePostUseCase $createPostUseCase
     *
     * @return Response
     */
    public function __invoke(Request $request, CreatePostUseCase $createPostUseCase): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $createPostCommand = new CreatePostCommand(
                $post->getTitle() ?? '',
                $post->getContent() ?? '',
                $post->getPublishedAt() ?? null
            );

            try {
                $post = $createPostUseCase->create($createPostCommand);

                $this->addFlash('success', "{$post->getPost()->getTitle()} created.");

                return $this->redirectToRoute('app.post.create');
            } catch (InvalidPostDataException $dataException) {
                $this->addFlash('error', $dataException->getMessage());
            }
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
