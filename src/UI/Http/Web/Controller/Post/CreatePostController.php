<?php

namespace App\UI\Http\Web\Controller\Post;

use App\Application\UseCase\Command\Post\Create\CreatePostCommand;
use App\Application\UseCase\Command\Post\Create\CreatePostUseCase;
use App\Domain\Post\Exception\InvalidPostDataException;
use App\UI\Http\Web\Form\Post\PostType;
use DateTime;
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
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            /** @var string $title */
            $title = $form->get('title')->getData();

            /** @var string $content */
            $content = $form->get('content')->getData();

            /** @var DateTime|null $publishedAt */
            $publishedAt = $form->get('publishedAt')->getData();

            $createPostCommand = new CreatePostCommand(
                $title, $content, $publishedAt
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
