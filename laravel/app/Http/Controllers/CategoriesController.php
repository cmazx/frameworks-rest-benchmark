<?php
declare(strict_types=1);

namespace App\Http\Controllers;


use App\Category;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Category as CategoryResource;
use http\Exception\RuntimeException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;

class CategoriesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param CategoryRequest $request
     *
     * @return CategoryResource
     */
    public function store(CategoryRequest $request)
    {
        return $this->createUpdate($request, new Category());
    }

    /**
     * @param int $id
     * @param CategoryRequest $request
     *
     * @return CategoryResource
     */
    public function update($id, CategoryRequest $request): CategoryResource
    {
        $id = $this->validateAndPrepareId($id);
        /**
         * @var Category $todo
         */
        $todo = Category::query()->findOrFail($id);


        return $this->createUpdate($request, $todo);
    }

    /**
     * @param CategoryRequest $request
     * @param Category $model
     *
     * @return CategoryResource
     */
    protected function createUpdate(CategoryRequest $request, Category $model): CategoryResource
    {
        $saved = $model
            ->fill($request->json()->all())
            ->save();

        //Some logic can prevent changing, so it's a question, do we need fire a exception, or it is fine way
        //so I just send not acceptable response
        if (!$saved) {
            abort(406, 'Request can not be processed');
        }

        return new CategoryResource($model);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::query()->get());
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function destroy($id): void
    {
        $id = $this->validateAndPrepareId($id);
        $todo = Category::query()->findOrFail($id);

        if (!$todo->delete()) {
            throw new RuntimeException('Error on delete todo #' . $id);
        }
    }

    /**
     * @param $id
     *
     * @return int
     */
    private function validateAndPrepareId($id): int
    {
        if (!is_numeric($id) && $id < 1) {
            abort(400, 'Id must be integer larger than 0');
        }

        return (int)$id;
    }
}
