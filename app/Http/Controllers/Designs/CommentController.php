<?php

namespace App\Http\Controllers\Designs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CommentInterface;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\DesignInterface;

class CommentController extends Controller
{

    public function __construct(
        protected CommentInterface $comments,
        protected DesignInterface $designs
    ) {
    }

    public function store(Request $request, $designId)
    {
        $this->validate($request, [
            'body' => ['required'],
        ]);

        $comment = $this->designs->addComment($designId, [
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = $this->comments->find($id);

        $this->authorize($comment);

        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->comments
            ->update($id, ['body' => $request->body]);

        return new CommentResource($comment);
    }

    public function destroy($id)
    {
        $comment = $this->comments->find($id);

        $this->authorize($comment);

        $this->comments->delete($id);

        return response()->json(['message' => 'Item deleted']);
    }
}
