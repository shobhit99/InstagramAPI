<?php
function scrape_insta($username) {
	@$insta_source = file_get_contents('http://instagram.com/'.$username);
	$shards = explode('window._sharedData = ', $insta_source);
	@$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array;
}
function scrape_insta_post($post) {
	@$insta_source = file_get_contents('http://instagram.com/p/'.$post);
	$shards = explode('window._sharedData = ', $insta_source);
	@$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	return $insta_array;
}

function profile($username){
    $json_data = scrape_insta($username);
    if($json_data['entry_data']!=NULL){
        $data = $json_data['entry_data']['ProfilePage'][0]['graphql']['user'];
        $followers = $data['edge_followed_by']['count'];
        $profile_pic = $data['profile_pic_url'];
        $id = $data['id'];
        $biography = $data['biography'];
        $total_posts = $data['edge_owner_to_timeline_media']['count'];
        $follows_count = $data['edge_follow']['count'];
        $post_picutres = array();
        if($total_posts > 9){
            for($i=0;$i<9;$i++){
                array_push($post_picutres, $data['edge_owner_to_timeline_media']['edges'][$i]['node']['display_url']);
            }
        }else{
            for($i=0;$i<$total_posts;$i++){
                array_push($post_picutres, $data['edge_owner_to_timeline_media']['edges'][$i]['node']['display_url']);
            }
        }
        
    }else{
        return 0;
    }
    $json_out = array("id"=>$id, "profile_pic"=>$profile_pic, "followers"=>$followers, "bio"=>$biography, "total_posts"=>$total_posts, "follows_count"=>$follows_count, "lastest_posts"=>$post_picutres);
    $js = json_encode($json_out, JSON_PRETTY_PRINT);
    return $js;
}
function post($code){
    $json_data = scrape_insta_post($code);
	if($json_data['entry_data']!=NULL)
	{
        $likes = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_media_preview_like']['count'];
        $comments = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_media_to_parent_comment']['count'];
        $media = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_resources'][2]['src'];
        $is_video = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['is_video'];
        if($is_video == true){
            $media = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['video_url'];
        }
        $caption = $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_media_to_caption']['edges'][0]['node']['text'];
        
        $comment_array = array();
        if($comments > 10){
            for($i=0;$i<10;$i++){
                    array_push($comment_array, $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_media_to_parent_comment']['edges'][$i]['node']['text']);
            }
        }else{
            for($i=0;$i<$comments;$i++){
                array_push($comment_array, $json_data['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_media_to_parent_comment']['edges'][$i]['node']['text']);
        }
        }
        $json_out = array("total_likes"=>$likes, "total_comments"=>$comments, "media"=>$media, "caption"=>$caption, "comments"=>$comment_array);
        return json_encode($json_out, JSON_PRETTY_PRINT);
    }
    else{
        return 0;
    }
}
header('Content-Type: application/json');