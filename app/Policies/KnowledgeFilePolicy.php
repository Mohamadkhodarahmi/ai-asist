<?php

namespace App\Policies;

use App\Models\KnowledgeFile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KnowledgeFilePolicy
{
    /**
     * Determine whether the user can view any models.
     * We don't need this for API, but it's good practice to have.
     */
    public function viewAny(User $user): bool
    {
        // Any user associated with a business can view the list of files for their business.
        return !is_null($user->business_id);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KnowledgeFile $knowledgeFile): bool
    {
        // The user can view the file if their business_id matches the file's business_id.
        return $user->business_id === $knowledgeFile->business_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any user that belongs to a business can upload a new file.
        return !is_null($user->business_id);
    }

    /**
     * Determine whether the user can update the model.
     * (We don't have an update method in the controller, but it's here for completeness).
     */
    public function update(User $user, KnowledgeFile $knowledgeFile): bool
    {
        // The user can update the file if their business_id matches the file's business_id.
        return $user->business_id === $knowledgeFile->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KnowledgeFile $knowledgeFile): bool
    {
        // The user can delete the file if their business_id matches the file's business_id.
        return $user->business_id === $knowledgeFile->business_id;
    }
}
