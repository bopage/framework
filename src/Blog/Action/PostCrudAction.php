<?php

namespace App\Blog\Action;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Action\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{

    protected $viewPath = '@blog/admin/posts';

    protected $routePrefix = 'blog.admin';

    private $categoryTable;

    private $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flashService,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    public function delete(ServerRequestInterface $request)
    {
        $post = $this->Table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    protected function getNewEntity()
    {
        $post = new Post;
        $post->created_at = new DateTime();

        return $post;
    }

    protected function fromParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['categories']['231546'] = 'fake category';
        return $params;
    }

    protected function prePersist(ServerRequestInterface $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        //uploader le fichier
        $image = $this->postUpload->upload($params['image'], $post->image);
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params =  array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i')
        ]);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        $validator =  parent::getValidator($request)
            ->required('name', 'slug', 'content', 'created_at', 'category_id')
            ->length('name', 3, 250)
            ->length('slug', 6, 250)
            ->length('content', 10)
            ->datetime('created_at')
            ->exist('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');

        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
