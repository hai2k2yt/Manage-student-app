<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends BaseRepository
{
    protected array $sortFields = [
        'rating'
    ];
    protected array $filterFields = [
        'rating'
    ];

    protected function getModel(): string
    {
        return Comment::class;
    }

    public function getCommentList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
