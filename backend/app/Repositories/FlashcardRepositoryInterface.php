<?php

namespace App\Repositories;

interface FlashcardRepositoryInterface
{
    /**
     * Lấy tất cả flashcard
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Tìm flashcard theo ID
     *
     * @param int $id
     * @return \App\Models\Flashcard
     */
    public function find($id);

    /**
     * Tạo flashcard mới
     *
     * @param array $data
     * @return \App\Models\Flashcard
     */
    public function create(array $data);

    /**
     * Cập nhật flashcard
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Flashcard
     */
    public function update($id, array $data);

    /**
     * Xóa flashcard
     *
     * @param int $id
     * @return void
     */
    public function delete($id);
}
