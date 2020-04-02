<?php
declare(strict_types=1);

namespace App\Http\Controllers;


use App\Category;
use App\Http\Requests\TodoRequest;
use App\Http\Resources\Todo as TodoResource;
use App\Todo;
use http\Exception\RuntimeException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;

class TodoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param TodoRequest $request
     *
     * @return TodoResource
     */
    public function store(TodoRequest $request)
    {
        $category = Category::query()->findOrFail($request->get('category_id'));
        $todo = new Todo();
        $todo->category_id = $category->id;

        return $this->createUpdate($request, $todo);
    }

    /**
     * @param int $id
     * @param TodoRequest $request
     *
     * @return TodoResource
     */
    public function update($id, TodoRequest $request): TodoResource
    {
        $id = $this->validateAndPrepareId($id);
        /**
         * @var Todo $todo
         */
        $todo = Todo::query()->findOrFail($id);


        return $this->createUpdate($request, $todo);
    }

    /**
     * @param TodoRequest $request
     * @param Todo $model
     *
     * @return TodoResource
     */
    protected function createUpdate(TodoRequest $request, Todo $model): TodoResource
    {
        $saved = $model
            ->fill($request->json()->all())
            ->save();

        //Some logic can prevent changing, so it's a question, do we need fire a exception, or it is fine way
        //so I just send not acceptable response
        if (!$saved) {
            abort(406, 'Request can not be processed');
        }

        return new TodoResource($model);
    }

    /**
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $category = Category::query()->findOrFail($request->get('category_id'));

        return TodoResource::collection(Todo::ordered()->where('category_id', '=', $category->id)->get());
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function destroy($id): void
    {
        $id = $this->validateAndPrepareId($id);
        $todo = Todo::query()->findOrFail($id);

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
