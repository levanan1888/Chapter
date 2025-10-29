<?php

/**
 * Comment Controller
 * Handles comment operations for stories and chapters
 * 
 * @package App
 */
class Controller_Comment extends Controller
{
    /**
     * Get replies recursively
     * 
     * @param int $parent_id
     * @return array
     */
    private function get_replies_recursive($parent_id)
    {
        $replies = array();
        $reply_comments = \Model_Comment::get_replies($parent_id);
        
        foreach ($reply_comments as $reply) {
            $reply_user_name = 'Anonymous';
            if (isset($reply->user) && is_object($reply->user)) {
                $reply_user_name = isset($reply->user->full_name) && !empty($reply->user->full_name) 
                    ? $reply->user->full_name 
                    : (isset($reply->user->username) ? $reply->user->username : 'Anonymous');
            } elseif (isset($reply->username)) {
                $reply_user_name = $reply->username;
            }
            
            // Get nested replies for this reply
            $nested_replies = $this->get_replies_recursive($reply->id);
            
            $replies[] = array(
                'id' => $reply->id,
                'content' => $reply->content,
                'user_name' => $reply_user_name,
                'user_id' => $reply->user_id,
                'created_at' => $reply->created_at,
                'replies' => $nested_replies
            );
        }
        
        return $replies;
    }
    
    /**
     * Add a new comment
     * 
     * @return Response
     */
    public function action_add()
    {
        try {
            // Check if user is logged in
            if (!\Session::get('user_id')) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để bình luận'
                ]), 401)->set_header('Content-Type', 'application/json');
            }

            // Validate CSRF token
            if (!\Security::check_token()) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'CSRF token không hợp lệ'
                ]), 403)->set_header('Content-Type', 'application/json');
            }

            // Get input data
            $story_id = \Input::post('story_id');
            $chapter_id = \Input::post('chapter_id');
            $content = \Input::post('content');
            $parent_id = \Input::post('parent_id', null);

            // Validate required fields
            if (empty($story_id) || empty($content)) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Thiếu thông tin bắt buộc'
                ]), 400)->set_header('Content-Type', 'application/json');
            }

            // Validate story exists
            $story = \Model_Story::find($story_id);
            if (!$story) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Truyện không tồn tại'
                ]), 404)->set_header('Content-Type', 'application/json');
            }

            // Validate chapter exists if provided
            if ($chapter_id) {
                $chapter = \Model_Chapter::find($chapter_id);
                if (!$chapter || $chapter->story_id != $story_id) {
                    return \Response::forge(json_encode([
                        'success' => false,
                        'message' => 'Chương không tồn tại'
                    ]), 404)->set_header('Content-Type', 'application/json');
                }
            }

            // Create comment
            $comment = \Model_Comment::forge([
                'story_id' => $story_id,
                'chapter_id' => $chapter_id,
                'user_id' => \Session::get('user_id'),
                'parent_id' => $parent_id,
                'content' => $content,
                'is_approved' => 1, // Auto-approve for now
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($comment->save()) {
                \Log::info('Comment added successfully: ' . $comment->id);
                return \Response::forge(json_encode([
                    'success' => true,
                    'message' => 'Bình luận đã được thêm thành công',
                    'comment_id' => $comment->id
                ]), 200)->set_header('Content-Type', 'application/json');
            } else {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Không thể lưu bình luận'
                ]), 500)->set_header('Content-Type', 'application/json');
            }

        } catch (\Exception $e) {
            \Log::error('Error adding comment: ' . $e->getMessage());
            return \Response::forge(json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm bình luận'
            ]), 500)->set_header('Content-Type', 'application/json');
        }
    }

    /**
     * Get CSRF token for AJAX forms
     * 
     * @return Response
     */
    public function action_get_token()
    {
        try {
            return \Response::forge(json_encode([
                'success' => true,
                'token' => \Security::fetch_token()
            ]), 200)->set_header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            \Log::error('Error getting CSRF token: ' . $e->getMessage());
            return \Response::forge(json_encode([
                'success' => false,
                'message' => 'Không thể lấy token'
            ]), 500)->set_header('Content-Type', 'application/json');
        }
    }

    /**
     * Get comments for a story/chapter
     * 
     * @return Response
     */
    public function action_get_comments()
    {
        try {
            $story_id = \Input::get('story_id');
            $chapter_id = \Input::get('chapter_id');

            if (empty($story_id)) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Thiếu story_id'
                ]), 400)->set_header('Content-Type', 'application/json');
            }

            // Get parent comments only (no replies)
            $comments = \Model_Comment::get_comments($story_id, $chapter_id);

            // Format comments with user info and replies
            $formatted_comments = array();
            foreach ($comments as $comment) {
                // Get display name (full_name preferred over username)
                $user_name = 'Anonymous';
                if (isset($comment->user) && is_object($comment->user)) {
                    // Prioritize full_name, fall back to username
                    $user_name = isset($comment->user->full_name) && !empty($comment->user->full_name) 
                        ? $comment->user->full_name 
                        : (isset($comment->user->username) ? $comment->user->username : 'Anonymous');
                } elseif (isset($comment->username)) {
                    $user_name = $comment->username;
                }
                
                // Get replies for this comment recursively
                $replies = $this->get_replies_recursive($comment->id);
                
                $formatted_comments[] = array(
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $user_name,
                    'user_id' => $comment->user_id,
                    'created_at' => $comment->created_at,
                    'replies' => $replies
                );
            }

            return \Response::forge(json_encode($formatted_comments), 200)
                ->set_header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            \Log::error('Error getting comments: ' . $e->getMessage());
            return \Response::forge(json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải bình luận'
            ]), 500)->set_header('Content-Type', 'application/json');
        }
    }

    /**
     * Delete a comment
     * 
     * @param int $id Comment ID
     * @return Response
     */
    public function action_delete($id = null)
    {
        try {
            // Check if user is logged in
            if (!\Session::get('user_id')) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để thực hiện thao tác này'
                ]), 401)->set_header('Content-Type', 'application/json');
            }

            if (empty($id)) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Thiếu ID bình luận'
                ]), 400)->set_header('Content-Type', 'application/json');
            }

            $comment = \Model_Comment::find($id);
            if (!$comment) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bình luận không tồn tại'
                ]), 404)->set_header('Content-Type', 'application/json');
            }

            // Check if user owns the comment or is admin
            if ($comment->user_id != \Session::get('user_id') && !\Session::get('is_admin')) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bình luận này'
                ]), 403)->set_header('Content-Type', 'application/json');
            }

            if ($comment->delete()) {
                \Log::info('Comment deleted: ' . $id);
                return \Response::forge(json_encode([
                    'success' => true,
                    'message' => 'Bình luận đã được xóa'
                ]), 200)->set_header('Content-Type', 'application/json');
            } else {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Không thể xóa bình luận'
                ]), 500)->set_header('Content-Type', 'application/json');
            }

        } catch (\Exception $e) {
            \Log::error('Error deleting comment: ' . $e->getMessage());
            return \Response::forge(json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bình luận'
            ]), 500)->set_header('Content-Type', 'application/json');
        }
    }

    /**
     * Edit a comment
     * 
     * @return Response
     */
    public function action_edit()
    {
        try {
            // Check if user is logged in
            if (!\Session::get('user_id')) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để chỉnh sửa bình luận'
                ]), 401)->set_header('Content-Type', 'application/json');
            }

            // Validate CSRF token
            if (!\Security::check_token()) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'CSRF token không hợp lệ'
                ]), 403)->set_header('Content-Type', 'application/json');
            }

            $comment_id = \Input::post('comment_id');
            $content = \Input::post('content');

            if (empty($comment_id) || empty($content)) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Thiếu thông tin bắt buộc'
                ]), 400)->set_header('Content-Type', 'application/json');
            }

            $comment = \Model_Comment::find($comment_id);
            if (!$comment) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bình luận không tồn tại'
                ]), 404)->set_header('Content-Type', 'application/json');
            }

            // Check if user owns the comment
            if ($comment->user_id != \Session::get('user_id') && !\Session::get('is_admin')) {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa bình luận này'
                ]), 403)->set_header('Content-Type', 'application/json');
            }

            // Update comment content
            $comment->content = $content;
            $comment->updated_at = date('Y-m-d H:i:s');

            if ($comment->save()) {
                \Log::info('Comment edited: ' . $comment_id);
                return \Response::forge(json_encode([
                    'success' => true,
                    'message' => 'Chỉnh sửa bình luận thành công!'
                ]), 200)->set_header('Content-Type', 'application/json');
            } else {
                return \Response::forge(json_encode([
                    'success' => false,
                    'message' => 'Không thể lưu thay đổi'
                ]), 500)->set_header('Content-Type', 'application/json');
            }

        } catch (\Exception $e) {
            \Log::error('Error editing comment: ' . $e->getMessage());
            return \Response::forge(json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi chỉnh sửa bình luận'
            ]), 500)->set_header('Content-Type', 'application/json');
        }
    }
}