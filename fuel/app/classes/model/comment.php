<?php

/**
 * Comment Model
 * 
 * @package App
 */
class Model_Comment extends \Model
{
    protected static $_table_name = 'comments';
    
    protected static $_primary_key = 'id';
    
    // Public properties for each field
    public $id;
    public $story_id;
    public $chapter_id;
    public $user_id;
    public $parent_id;
    public $content;
    public $is_approved;
    public $created_at;
    public $updated_at;
    public $user; // For storing user info from JOIN
    public $story; // For storing story info
    public $chapter; // For storing chapter info
    public $replies; // For storing replies array
    
    
    /**
     * Find comment by ID
     * 
     * @param int $id
     * @return Model_Comment|null
     */
    public static function find($id)
    {
        try {
            $query = \DB::query("SELECT * FROM comments WHERE id = :id");
            $result = $query->param('id', $id)->execute();
            
            if ($result->count() > 0) {
                $data = $result->current();
                $comment = new self();
                foreach ($data as $key => $value) {
                    if (property_exists($comment, $key)) {
                        $comment->$key = $value;
                    }
                }
                return $comment;
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::find error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new comment instance
     * 
     * @param array $data
     * @return Model_Comment
     */
    public static function forge($data = array())
    {
        $comment = new self();
        foreach ($data as $key => $value) {
            if (property_exists($comment, $key)) {
                $comment->$key = $value;
            }
        }
        return $comment;
    }
    
    /**
     * Save comment to database
     * 
     * @return bool
     */
    public function save()
    {
        try {
            if (isset($this->id) && $this->id) {
                // Update existing comment
                $this->updated_at = date('Y-m-d H:i:s');
                $query = \DB::update('comments')
                    ->set(array(
                        'story_id' => $this->story_id,
                        'chapter_id' => $this->chapter_id,
                        'user_id' => $this->user_id,
                        'parent_id' => $this->parent_id,
                        'content' => $this->content,
                        'is_approved' => $this->is_approved,
                        'updated_at' => $this->updated_at,
                    ))
                    ->where('id', $this->id);
                $result = $query->execute();
                return $result > 0;
            } else {
                // Insert new comment
                $this->created_at = date('Y-m-d H:i:s');
                $this->updated_at = date('Y-m-d H:i:s');
                $query = \DB::insert('comments')
                    ->set(array(
                        'story_id' => $this->story_id,
                        'chapter_id' => $this->chapter_id,
                        'user_id' => $this->user_id,
                        'parent_id' => $this->parent_id,
                        'content' => $this->content,
                        'is_approved' => $this->is_approved,
                        'created_at' => $this->created_at,
                        'updated_at' => $this->updated_at,
                    ));
                list($id) = $query->execute();
                $this->id = $id;
                return true;
            }
        } catch (\Exception $e) {
            \Log::error('Model_Comment::save error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete comment from database
     * 
     * @return bool
     */
    public function delete()
    {
        try {
            $query = \DB::delete('comments')->where('id', $this->id);
            $result = $query->execute();
            return $result > 0;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get comments for a story
     * 
     * @param int $story_id
     * @param int $chapter_id
     * @return array
     */
    public static function get_story_comments($story_id, $chapter_id = null)
    {
        try {
            $sql = "SELECT * FROM comments WHERE story_id = :story_id AND is_approved = 1 AND parent_id IS NULL";
            
            if ($chapter_id) {
                $sql .= " AND chapter_id = :chapter_id";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $query = \DB::query($sql);
            $query->param('story_id', $story_id);
            
            if ($chapter_id) {
                $query->param('chapter_id', $chapter_id);
            }
            
            $result = $query->execute();
            $comments = array();
            
            foreach ($result as $row) {
                $comment = new self();
                foreach ($row as $key => $value) {
                    if (property_exists($comment, $key)) {
                        $comment->$key = $value;
                    }
                }
                $comments[] = $comment;
            }
            
            return $comments;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::get_story_comments error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get comments for a chapter
     * 
     * @param int $chapter_id
     * @return array
     */
    public static function get_comments($story_id, $chapter_id = null)
    {
        try {
            $sql = "SELECT c.*, a.username, a.full_name FROM comments c LEFT JOIN admins a ON c.user_id = a.id WHERE c.story_id = :story_id AND c.is_approved = 1 AND c.parent_id IS NULL";
            
            if ($chapter_id) {
                $sql .= " AND c.chapter_id = :chapter_id";
            } else {
                $sql .= " AND c.chapter_id IS NULL";
            }
            
            $sql .= " ORDER BY c.created_at DESC";
            
            $query = \DB::query($sql);
            $query->param('story_id', $story_id);
            
            if ($chapter_id) {
                $query->param('chapter_id', $chapter_id);
            }
            
            $result = $query->execute();
            $comments = array();
            
            foreach ($result as $row) {
                $comment = new self();
                foreach ($row as $key => $value) {
                    if (property_exists($comment, $key)) {
                        $comment->$key = $value;
                    }
                }
                // Use full_name if available, otherwise fall back to username
                $display_name = isset($row['full_name']) && !empty($row['full_name']) 
                    ? $row['full_name'] 
                    : (isset($row['username']) ? $row['username'] : 'Anonymous');
                $comment->user = (object)array(
                    'username' => isset($row['username']) ? $row['username'] : 'Anonymous',
                    'full_name' => $display_name,
                    'display_name' => $display_name
                );
                $comments[] = $comment;
            }
            
            return $comments;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::get_comments error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get comment replies by parent ID (static method)
     * 
     * @param int $parent_id
     * @return array
     */
    public static function get_replies($parent_id)
    {
        try {
            $sql = "SELECT c.*, a.username, a.full_name FROM comments c LEFT JOIN admins a ON c.user_id = a.id WHERE c.parent_id = :parent_id AND c.is_approved = 1 ORDER BY c.created_at ASC";
            
            $query = \DB::query($sql);
            $query->param('parent_id', $parent_id);
            $result = $query->execute();
            $replies = array();
            
            foreach ($result as $row) {
                $reply = new self();
                foreach ($row as $key => $value) {
                    if (property_exists($reply, $key)) {
                        $reply->$key = $value;
                    }
                }
                // Use full_name if available, otherwise fall back to username
                $display_name = isset($row['full_name']) && !empty($row['full_name']) 
                    ? $row['full_name'] 
                    : (isset($row['username']) ? $row['username'] : 'Anonymous');
                $reply->user = (object)array(
                    'username' => isset($row['username']) ? $row['username'] : 'Anonymous',
                    'full_name' => $display_name,
                    'display_name' => $display_name
                );
                $replies[] = $reply;
            }
            
            return $replies;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::get_replies static error: ' . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Get comment count for story
     * 
     * @param int $story_id
     * @param int $chapter_id
     * @return int
     */
    public static function get_comment_count($story_id, $chapter_id = null)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM comments WHERE story_id = :story_id AND is_approved = 1";
            
            if ($chapter_id) {
                $sql .= " AND chapter_id = :chapter_id";
            }
            
            $query = \DB::query($sql);
            $query->param('story_id', $story_id);
            
            if ($chapter_id) {
                $query->param('chapter_id', $chapter_id);
            }
            
            $result = $query->execute();
            $current = $result->current();
            return isset($current['count']) ? $current['count'] : 0;
        } catch (\Exception $e) {
            \Log::error('Model_Comment::get_comment_count error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Create a new comment
     * 
     * @param array $data
     * @return Model_Comment
     */
    public static function create_comment($data)
    {
        $comment = self::forge($data);
        $comment->save();
        return $comment;
    }
    
    /**
     * Approve comment
     * 
     * @return bool
     */
    public function approve()
    {
        $this->is_approved = 1;
        return $this->save();
    }
    
    /**
     * Disapprove comment
     * 
     * @return bool
     */
    public function disapprove()
    {
        $this->is_approved = 0;
        return $this->save();
    }
    
    /**
     * Get formatted created time
     * 
     * @return string
     */
    public function get_formatted_time()
    {
        $time = strtotime($this->created_at);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Vừa xong';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phút trước';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' giờ trước';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . ' ngày trước';
        } else {
            return date('d/m/Y H:i', $time);
        }
    }
    
    /**
     * Count all comments
     * 
     * @return int
     */
    public static function count_all()
    {
        $result = \DB::query("SELECT COUNT(*) as total FROM comments")->execute();
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Count approved comments
     * 
     * @return int
     */
    public static function count_approved()
    {
        $result = \DB::query("SELECT COUNT(*) as total FROM comments WHERE is_approved = 1")->execute();
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Count pending comments
     * 
     * @return int
     */
    public static function count_pending()
    {
        $result = \DB::query("SELECT COUNT(*) as total FROM comments WHERE is_approved = 0")->execute();
        return isset($result[0]['total']) ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Get recent comments
     * 
     * @param int $limit
     * @return array
     */
    public static function get_recent_comments($limit = 5)
    {
        return self::find('all', array(
            'order_by' => array('created_at' => 'desc'),
            'limit' => $limit
        ));
    }
}
