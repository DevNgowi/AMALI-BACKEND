<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Utils\UtilController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{

    private const REQUIRED_ATTRIBUTES_RULES = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
    ];

    private const VALIDATION_NULLABLE_STRING = 'nullable|string';
    private const VALIDATION_NULLABLE_STRING_WITH_MAX_255 = 'nullable|string|max:255';

    private const ATTRIBUTE_VALIDATION_RULES = [
        'description' => self::VALIDATION_NULLABLE_STRING,
        'sku' => self::VALIDATION_NULLABLE_STRING_WITH_MAX_255,
        'image' => self::VALIDATION_NULLABLE_STRING,
        'size' => self::VALIDATION_NULLABLE_STRING,
        'color' => self::VALIDATION_NULLABLE_STRING,
        'material' => self::VALIDATION_NULLABLE_STRING,
        'author_id' => self::VALIDATION_NULLABLE_STRING,
        'isbn' => self::VALIDATION_NULLABLE_STRING_WITH_MAX_255,
    ];
    private const OPTIONAL_STRING_ATTRIBUTES = [
        'description' => 'nullable|string|max:500',
    ];

    private const OPTIONAL_NUMERIC_ATTRIBUTES = [
        'pages' => 'nullable|numeric',
    ];

    private const OPTIONAL_DATE_ATTRIBUTES = [
        'published_date' => 'nullable|date',
    ];

    private const OPTIONAL_RELATIONSHIP_ATTRIBUTES = [
        'publisher_id' => 'nullable|exists:publishers,id',
    ];

    private const PRODUCT_VALIDATION_RULES = [
        ...self::REQUIRED_ATTRIBUTES_RULES,
        ...self::OPTIONAL_STRING_ATTRIBUTES,
        ...self::OPTIONAL_NUMERIC_ATTRIBUTES,
        ...self::OPTIONAL_DATE_ATTRIBUTES,
        ...self::OPTIONAL_RELATIONSHIP_ATTRIBUTES,
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->search;

        $productQuery = Product::query();
        if ($search) {
            $productQuery->where('name', 'like', "%$search%");
        }

        $products = $productQuery->paginate($perPage);
        $serializedProducts = $products->map(fn($product) => $this->serializeProduct($product));
        $pagination = UtilController::serializePagination($products);

        return response()->json(
            ['data' => $serializedProducts, 'pagination' => $pagination],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            ['data' => ['categories' => $this->getCategories()]],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validationRules = $this->getValidationRules();
        $this->validateRequest($request, $validationRules);

        try {
            \DB::beginTransaction();

            $newProduct = Product::create($request->except(['author_ids', 'genre_ids', 'publisher_id']));

            if (!$newProduct) {
                \DB::rollBack(); // Rollback transaction if product creation fails
                return response()->json(['error' => 'Product not created'], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($request->has('author_ids')) {
                $newProduct->authors()->attach($request->input('author_ids'));
            }

            if ($request->has('genre_ids')) {
                $newProduct->genres()->attach($request->input('genre_ids'));
            }

            if ($request->has('publisher_id')) {
                $newProduct->publishers()->attach($request->input('publisher_ids'))->save();
            }

            \DB::commit();

            return response()->json(
                ['data' => $this->serializeProduct($newProduct->fresh())], // Refresh the product to load relationships
                ResponseAlias::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reusable product validation rules
     *
     * @return array The validation rules used for product creation
     */


    private function getValidationRules(): array
    {
        return self::PRODUCT_VALIDATION_RULES;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->getProductOrFail($request);

        return response()->json(
            ['data' => $this->serializeProduct($product)],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->getProductOrFail($request);
        $product->delete();

        return response()->json(
            ['message' => 'Product deleted successfully'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @return array
     */
    private function getCategories(): array
    {
        return Category::all()->map(fn($category) => [
            'id' => $category->id,
            'name' => $category->name,
        ])->toArray();
    }

    /**
     * @param Request $request
     * @param array $rules
     * @return void
     */
    private function validateRequest(Request $request, array $rules): void
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            response()->json(['error' => $validator->messages()], ResponseAlias::HTTP_BAD_REQUEST)->throwResponse();
        }
    }

    /**
     * @param Request $request
     * @return Product
     */
    private function getProductOrFail(Request $request): Product
    {
        $this->validateRequest($request, ['id' => 'required|exists:products,id']);

        $product = Product::find($request->id);

        if (!$product) {
            response()->json(['error' => 'Product not found'], ResponseAlias::HTTP_NOT_FOUND)->throwResponse();
        }

        return $product;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function serializeProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'description' => $product->description,
            'price' => $product->price,
            'genre' => $product->genre->name ?? '',
            'sku' => $product->sku,
            'category_name' => $product->category->name ?? '',
            'author_name' => $product->author->name ?? '',
            'publisher_name' => $product->publisher->name ?? '',
            'pages' => $product->pages,
            'isbn' => $product->isbn,
            'published_date' => $product->published_date,
            'material' => $product->material,
            'color' => $product->color,
            'thumb' => $product->image,
        ];
    }
}
