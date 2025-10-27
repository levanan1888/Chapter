<?php

/**
 * Admin Comment Controller
 * Handles comment management in admin panel
 * 
 * @package    App
 * @subpackage Controller\Admin
 */
class Controller_Admin_Comment extends Controller_Admin_Base
{
    /**
     * Get replies recursively
     * 
     * @param int $parent_id
     * @return array
     */
    private function get_replies_recursive($parent_id)
    {
        $replies_sql = "SELECT c.*, a.username, a.full_name 
                       FROM comments c 
                       LEFT JOIN admins a ON c.user_id = a.id 
                       WHERE c.parent_id = " . intval($parent_id) . " 
                       ORDER BY c.created_at ASC";
        
        $replies_result = DB::query($replies_sql)->execute();
        $replies = array();
        
        foreach ($replies_result as $reply_row) {
            $reply = new stdClass();
            foreach ($reply_row as $key => $value) {
                $reply->$key = $value;
            }
            
            // Store user info for reply
            $reply_display_name = isset($reply_row['full_name']) && !empty($reply_row['full_name']) 
                ? $reply_row['full_name'] 
                : (isset($reply_row['username']) ? $reply_row['username'] : 'Anonymous');
            $reply->user = (object)array(
                'username' => isset($reply_row['username']) ? $reply_row['username'] : 'Anonymous',
                'full_name' => $reply_display_name
            );
            
            // Get nested replies
            $reply->replies = $this->get_replies_recursive($reply->id);
            
            $replies[] = $reply;
        }
        
        return $replies;
    }
    
    /**
     * List all comments
     * 
     * @return Response
     */
    public function action_index()
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        $page = Input::get('page', 1);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        try {
            // Get filter parameters
            $search = Input::get('search', '');
            $story_id = Input::get('story_id', '');
            $status = Input::get('status', '');
            
            // Build WHERE conditions
            $where_conditions = array("c.parent_id IS NULL");
            
            if (!empty($search)) {
                $search_escaped = DB::escape('%' . $search . '%');
                $where_conditions[] = "(c.content LIKE $search_escaped OR a.username LIKE $search_escaped OR a.full_name LIKE $search_escaped)";
            }
            
            if (!empty($story_id)) {
                $story_id_escaped = DB::escape($story_id);
                $where_conditions[] = "c.story_id = $story_id_escaped";
            }
            
            if ($status === 'approved') {
                $where_conditions[] = "c.is_approved = 1";
            } elseif ($status === 'pending') {
                $where_conditions[] = "c.is_approved = 0";
            }
            
            $where_clause = implode(' AND ', $where_conditions);
            
            // Get parent comments only with pagination and user info
            $sql = "SELECT c.*, a.username, a.full_name, s.title as story_title, s.slug as story_slug, ch.title as chapter_title, ch.chapter_number
                    FROM comments c 
                    LEFT JOIN admins a ON c.user_id = a.id 
                    LEFT JOIN stories s ON c.story_id = s.id 
                    LEFT JOIN chapters ch ON c.chapter_id = ch.id 
                    WHERE " . $where_clause . "
                    ORDER BY c.created_at DESC 
                    LIMIT " . intval($per_page) . " OFFSET " . intval($offset);
            
            // Debug SQL
            Log::info('Comments SQL: ' . $sql);
            
            $result = DB::query($sql)->execute();
            $comments = array();
            
            // Debug: Check if we have results
            if ($result === false) {
                Log::error('Comments query failed - SQL: ' . $sql);
                Log::error('Comments query failed - Params: ' . print_r($params, true));
                $result = array();
            } else {
                Log::info('Comments query result count: ' . count($result));
            }
            
            foreach ($result as $row) {
                $comment = new stdClass();
                foreach ($row as $key => $value) {
                    $comment->$key = $value;
                }
                
                // Store story and chapter info
                if (!empty($row['story_title'])) {
                    $comment->story = (object)array(
                        'title' => $row['story_title'],
                        'slug' => $row['story_slug'],
                        'id' => $row['story_id']
                    );
                }
                if (!empty($row['chapter_title'])) {
                    $comment->chapter = (object)array(
                        'title' => $row['chapter_title'],
                        'chapter_number' => $row['chapter_number']
                    );
                }
                // Store user info
                $display_name = isset($row['full_name']) && !empty($row['full_name']) 
                    ? $row['full_name'] 
                    : (isset($row['username']) ? $row['username'] : 'Anonymous');
                $comment->user = (object)array(
                    'username' => isset($row['username']) ? $row['username'] : 'Anonymous',
                    'full_name' => $display_name
                );
                
                // Get replies recursively for this parent comment
                $comment->replies = $this->get_replies_recursive($comment->id);

                
                $comments[] = $comment;
            }
            
            // Get total count with same filters
            $count_sql = "SELECT COUNT(*) as total FROM comments c 
                         LEFT JOIN admins a ON c.user_id = a.id 
                         WHERE " . $where_clause;
            $total_result = DB::query($count_sql)->execute();
            $total_comments = 0;
            if ($total_result !== false && isset($total_result[0]['total'])) {
                $total_comments = (int)$total_result[0]['total'];
            }
            $total_pages = ceil($total_comments / $per_page);
            
            // Get stories list for filter dropdown
            $stories_sql = "SELECT id, title FROM stories WHERE is_visible = 1 ORDER BY title";
            $stories_result = DB::query($stories_sql)->execute();
            $stories = array();
            if ($stories_result !== false) {
                foreach ($stories_result as $story_row) {
                    $stories[] = (object) $story_row;
                }
            }
            
            $data = array(
                'comments' => $comments,
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_count' => $total_comments,
                'stories' => $stories,
                'title' => 'Quản lý Bình luận',
                'content' => View::forge('admin/content/comments/index', array(
                    'comments' => $comments,
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_count' => $total_comments,
                    'stories' => $stories,
                ))
            );
            
            return View::forge('layouts/admin', $data);
            
        } catch (Exception $e) {
            Log::error('Admin comments index error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi tải danh sách bình luận');
            $data = array(
                'title' => 'Quản lý Bình luận',
                'content' => View::forge('admin/content/comments/index', array('comments' => array()))
            );
            return View::forge('layouts/admin', $data);
        }
    }
    
    /**
     * Approve a comment
     * 
     * @param int $id
     * @return Response
     */
    public function action_approve($id = null)
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        if (!$id) {
            Session::set_flash('error', 'Bình luận không tồn tại');
            return Response::redirect('admin/comments');
        }
        
        try {
            $comment = Model_Comment::find($id);
            
            if (!$comment) {
                Session::set_flash('error', 'Bình luận không tồn tại');
                return Response::redirect('admin/comments');
            }
            
            if ($comment->approve()) {
                Session::set_flash('success', 'Bình luận đã được duyệt thành công!');
                Log::info('Comment approved: ' . $id);
            } else {
                Session::set_flash('error', 'Có lỗi xảy ra khi duyệt bình luận');
            }
            
        } catch (Exception $e) {
            Log::error('Comment approve error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi duyệt bình luận');
        }
        
        return Response::redirect('admin/comments');
    }
    
    /**
     * Disapprove a comment
     * 
     * @param int $id
     * @return Response
     */
    public function action_disapprove($id = null)
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        if (!$id) {
            Session::set_flash('error', 'Bình luận không tồn tại');
            return Response::redirect('admin/comments');
        }
        
        try {
            $comment = Model_Comment::find($id);
            
            if (!$comment) {
                Session::set_flash('error', 'Bình luận không tồn tại');
                return Response::redirect('admin/comments');
            }
            
            if ($comment->disapprove()) {
                Session::set_flash('success', 'Bình luận đã được ẩn thành công!');
                Log::info('Comment disapproved: ' . $id);
            } else {
                Session::set_flash('error', 'Có lỗi xảy ra khi ẩn bình luận');
            }
            
        } catch (Exception $e) {
            Log::error('Comment disapprove error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi ẩn bình luận');
        }
        
        return Response::redirect('admin/comments');
    }
    
    /**
     * Delete a comment
     * 
     * @param int $id
     * @return Response
     */
    public function action_delete($id = null)
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        if (!$id) {
            Session::set_flash('error', 'Bình luận không tồn tại');
            return Response::redirect('admin/comments');
        }
        
        try {
            $comment = Model_Comment::find($id);
            
            if (!$comment) {
                Session::set_flash('error', 'Bình luận không tồn tại');
                return Response::redirect('admin/comments');
            }
            
            // Delete comment and its replies
            $comment->delete();
            
            Session::set_flash('success', 'Bình luận đã được xóa thành công!');
            Log::info('Comment deleted by admin: ' . $id);
            
        } catch (Exception $e) {
            Log::error('Comment delete error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi xóa bình luận');
        }
        
        return Response::redirect('admin/comments');
    }
    
    /**
     * View comment details
     * 
     * @param int $id
     * @return Response
     */
    public function action_view($id = null)
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        if (!$id) {
            Session::set_flash('error', 'Bình luận không tồn tại');
            return Response::redirect('admin/comments');
        }
        
        try {
            $comment = Model_Comment::query()
                ->related('story')
                ->related('user')
                ->related('chapter')
                ->related('parent')
                ->where('id', $id)
                ->get_one();
            
            if (!$comment) {
                Session::set_flash('error', 'Bình luận không tồn tại');
                return Response::redirect('admin/comments');
            }
            
            // Get replies
            $replies = $comment->get_replies();
            
            $data = array(
                'comment' => $comment,
                'replies' => $replies,
                'title' => 'Chi tiết bình luận',
                'content' => View::forge('admin/content/comments/view', array(
                    'comment' => $comment,
                    'replies' => $replies,
                ))
            );
            
            return View::forge('layouts/admin', $data);
            
        } catch (Exception $e) {
            Log::error('Comment view error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi tải bình luận');
            return Response::redirect('admin/comments');
        }
    }
    
    /**
     * Save reply to comment
     * 
     * @return Response
     */
    public function action_save_reply()
    {
        if (!Session::get('admin_id')) {
            Response::redirect('admin/login');
        }
        
        try {
            $parent_id = Input::post('parent_id');
            $content = Input::post('content');
            
            if (empty($parent_id) || empty($content)) {
                Session::set_flash('error', 'Thiếu thông tin bắt buộc');
                return Response::redirect('admin/comments');
            }
            
            // Get parent comment to get story_id and chapter_id
            $parent_comment = Model_Comment::find($parent_id);
            if (!$parent_comment) {
                Session::set_flash('error', 'Bình luận gốc không tồn tại');
                return Response::redirect('admin/comments');
            }
            
            // Create reply
            $reply = Model_Comment::forge([
                'story_id' => $parent_comment->story_id,
                'chapter_id' => $parent_comment->chapter_id,
                'user_id' => Session::get('admin_id'),
                'parent_id' => $parent_id,
                'content' => $content,
                'is_approved' => 1, // Auto-approve admin replies
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($reply->save()) {
                Session::set_flash('success', 'Trả lời đã được thêm thành công!');
                Log::info('Admin reply added: ' . $reply->id);
            } else {
                Session::set_flash('error', 'Có lỗi xảy ra khi thêm trả lời');
            }
            
        } catch (Exception $e) {
            Log::error('Save reply error: ' . $e->getMessage());
            Session::set_flash('error', 'Có lỗi xảy ra khi thêm trả lời');
        }
        
        return Response::redirect('admin/comments');
    }
}
