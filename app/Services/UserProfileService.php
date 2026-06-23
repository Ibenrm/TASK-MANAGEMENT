<?php

namespace App\Services;

use App\Models\UserProfile;

class UserProfileService
{
    public function getAll()
    {
        return UserProfile::with('user')->get();
    }

    public function getById($id)
    {
        return UserProfile::with('user')->findOrFail($id);
    }

    public function getByUserId($userId)
    {
        return UserProfile::where('user_id', $userId)->firstOrFail();
    }

    public function create(array $data)
    {
        return UserProfile::create($data);
    }

    public function update($id, array $data)
    {
        $userProfile = UserProfile::findOrFail($id);
        $userProfile->update($data);
        return $userProfile;
    }

    public function delete($id)
    {
        $userProfile = UserProfile::findOrFail($id);
        return $userProfile->delete();
    }
}
