<?php

namespace App\Observers;

use App\Models\Reply;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        //To avoid XSS attack, we will use HTMLPurifier to clean the user's reply content
        //the clean rules we use is the same with user's topic body content
        $content= clean($reply->content, 'user_topic_body');

        //if cleaned content is empty
        if(empty($content)){

            redirect()->back()->with('danger', 'Your reply contains unaccepted content, we have removed your reply.');
            session()->forget('success');
            return false;
        }

        $reply->content = $content;
    }

    public function created(Reply $reply)
    {
        //After a new reply being created, the topic's reply_count add 1;
        //$reply->topic->increment('reply_count', 1);//This is not the best way
        //Attention: ->increment will return a query contructor, which is the operation on database and will not trigger the TopicObserver

        //The following is better
        $reply_count = $reply->topic->replies->count();
        $reply->topic->reply_count = $reply_count;
        $reply->topic->save();

    }

    public function updating(Reply $reply)
    {
        //
    }
}
