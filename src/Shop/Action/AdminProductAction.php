<?php
namespace App\Shop\Action;

use App\Shop\Entity\Product;
use App\shop\Table\ProductTable;
use App\Shop\Upload\PdfUpload;
use App\Shop\Upload\ProductImageUpload;
use DateTime;
use Framework\Action\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Validator;

class AdminProductAction extends CrudAction
{
    protected $viewPath = "@shop/admin/products";

    protected $routePrefix = 'shop.admin.products';

    protected $acceptedParams = ['name', 'slug', 'price', 'description', 'created_at'];

    private $productImageUpload;

    private $pdfUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        ProductTable $table,
        FlashService $flashService,
        ProductImageUpload $productImageUpload,
        PdfUpload $pdfUpload
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
        $this->productImageUpload = $productImageUpload;
        $this->pdfUpload = $pdfUpload;
    }

    protected function getNewEntity()
    {
        /** @var Product */
        $entity = parent::getNewEntity();
        $entity->setCreatedAt(new DateTime());
        return $entity;
    }

    protected function postPersist(ServerRequestInterface $request, $item)
    {
        $file = $request->getUploadedFiles()['pdf'];
        $productId = $item->getId() ?: $this->Table->getPdo()->lastInsertId();
        $this->pdfUpload->upload($file, "$productId.pdf", "$productId.pdf");
    }
    
    /**
     * prePersist
     *
     * @param  ServerRequestInterface $request
     * @param  Product $item
     * @return array
     */
    protected function prePersist(ServerRequestInterface $request, $item): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $image = $this->productImageUpload->upload($params['image'], $item->getImage());
        if ($image) {
            $params['image'] = $image;
            $this->acceptedParams[] = 'image';
        }
        $params =  array_filter($params, function ($key) {
            return in_array($key, $this->acceptedParams);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i')
        ]);
    }

    public function delete(ServerRequestInterface $request)
    {
        /** @var Product */
        $product = $this->Table->find($request->getAttribute('id'));
        $this->productImageUpload->delete($product->getImage());
        $this->pdfUpload->delete($product->getPdf());
        return parent::delete($request);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        $validator = parent::getValidator($request)
                ->required($this->acceptedParams)
                ->length('name', 5)
                ->length('slug', 5)
                ->slug('slug')
                ->unique('slug', $this->Table, null, $request->getAttribute('id'))
                ->length('description', 5)
                ->datetime('created_at')
                ->numeric('price')
                ->extension('image', ['jpg', 'png'])
                ->extension('pdf', ['pdf']);
        if ($request->getAttribute('id') === null) {
            $validator->uploaded('image');
            $validator->uploaded('pdf');
        }
        return $validator;
    }
}
