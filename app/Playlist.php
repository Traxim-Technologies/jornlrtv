<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
	public function getTotalVideos(){
	    return $this->hasMany('App\PlaylistVideo')->where('user_id',$this->user_id)->count();
	}

    public function userDetails() {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function channelDetails() {
        return $this->belongsTo('App\Channel', 'channel_id');
    }

    public function playlistVideos() {
    	return $this->hasMany('App\PlaylistVideo');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
        	'playlists.id as playlist_id',
            'playlists.channel_id as channel_id',
            'playlists.user_id as user_id',
            'playlists.title as title',
            'playlists.description as description',
        	'playlists.status as status',
        	'playlists.created_at',
        	'playlists.updated_at'
        );

    }
}
