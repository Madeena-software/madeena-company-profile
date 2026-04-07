<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        return true; // All authenticated users can view all posts
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create posts
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        // Admin can update all posts, non-admin can only update their own
        return $user->isAdmin() || $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        // Admin can delete all posts, non-admin can only delete their own
        return $user->isAdmin() || $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
