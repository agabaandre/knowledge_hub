<?php

namespace App\Repositories;

use App\Models\PushNotification;

class PushNotificationsRepository
{
    public function getAll()
    {
        $notifications = PushNotification::orderBy('created_at','desc');
        $notifications = $notifications->whereNull('user_id');
        
       if(auth()->user()){
         $notifications = $notifications->orWhere('user_id',auth()->user()->id);
       }

        return $notifications->paginate(15);
    }

    public function find($id)
    {
        return PushNotification::find($id);
    }

    public function create(array $data)
    {
        return PushNotification::create($data);
    }

    public function update(PushNotification $pushNotification, array $data)
    {
        return $pushNotification->update($data);
    }

    public function delete(PushNotification $pushNotification)
    {
        return $pushNotification->delete();
    }

    public function getByUser($userId)
    {
        return PushNotification::where('user_id', $userId)->get();
    }

    public function markAsRead($ids)
    {
        $ids = (is_array($ids)) ? $ids : json_decode($ids);
        PushNotification::whereIn('id', $ids)->update(['is_read' => 1]);
    }

    public function getUnreadCount($userId)
    {
        return PushNotification::where('user_id', $userId)->where('is_read', 0)->count();
    }
}
